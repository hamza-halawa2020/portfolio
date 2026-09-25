import { expect, Page, TestInfo, test } from '@playwright/test';

const apiOrigin = process.env['INT001_API_ORIGIN'];
const apiBaseUrl = process.env['INT001_API_BASE_URL'];
const frontendOrigin = process.env['PLAYWRIGHT_BASE_URL'];

if (!apiOrigin || !apiBaseUrl || !frontendOrigin) {
  throw new Error('INT001_API_ORIGIN, INT001_API_BASE_URL, and PLAYWRIGHT_BASE_URL are required.');
}

test.beforeEach(async ({ page }) => {
  page.on('console', (message) => {
    const text = message.text();
    expect(text).not.toMatch(
      /hydration|visitor_id_hash|ip_hash|user_agent_hash|secret|admin_notes/i,
    );
    if (/Failed to load resource: the server responded with a status of 500/.test(text)) {
      return;
    }
    expect(message.type(), text).not.toBe('error');
  });

  page.on('pageerror', (error) => {
    throw error;
  });
});

test('records project views once after browser rendering and preserves idempotency on reload', async ({
  page,
}, testInfo) => {
  const slug = projectSlug('e2e-project-view', testInfo);
  const viewRequests: string[] = [];
  page.on('request', (request) => {
    if (request.method() === 'POST' && request.url().includes(`/projects/${slug}/views`)) {
      viewRequests.push(request.url());
    }
  });

  await page.goto(`/en/projects/${slug}`);
  await expect(page.getByRole('heading', { level: 1, name: 'E2E view project' })).toBeVisible();
  await expect(page.getByTestId('project-view-count')).toHaveText('1');
  expect(viewRequests).toHaveLength(1);

  await page.reload();
  await expect(page.getByRole('heading', { level: 1, name: 'E2E view project' })).toBeVisible();
  await expect(page.getByTestId('project-view-count')).toHaveText('1');
  expect(viewRequests).toHaveLength(2);
});

test('does not block project content when browser view tracking fails', async ({
  page,
}, testInfo) => {
  const slug = projectSlug('e2e-project-view-failure', testInfo);
  await page.route(`**/api/v1/projects/${slug}/views`, async (route) => {
    await route.fulfill({
      contentType: 'application/json',
      status: 500,
      body: JSON.stringify({ message: 'Expected E2E view failure' }),
    });
  });

  await page.goto(`/en/projects/${slug}`);
  await expect(
    page.getByRole('heading', { level: 1, name: 'E2E view failure project' }),
  ).toBeVisible();
  await expect(page.getByText('Body for E2E view failure project.')).toBeVisible();
});

test('loads like state from the visitor cookie and keeps it after reload', async ({
  page,
  context,
}, testInfo) => {
  const slug = projectSlug('e2e-project-like', testInfo);
  let stateHeaders: Record<string, string> | null = null;
  page.on('response', async (response) => {
    if (response.url().includes(`/projects/${slug}/likes?`)) {
      stateHeaders = await response.allHeaders();
    }
  });

  await page.goto(`/en/projects/${slug}`);
  await expect(page.getByRole('button', { name: 'Like E2E like project' })).toBeVisible();
  await expect(page.getByTestId('project-like-count')).toHaveText('0');

  await page.getByRole('button', { name: 'Like E2E like project' }).click();
  await expect(page.getByRole('button', { name: 'Unlike E2E like project' })).toBeVisible();
  await expect(page.getByTestId('project-like-count')).toHaveText('1');

  const cookies = await context.cookies(apiOrigin);
  const visitorCookie = cookies.find((cookie) => cookie.name === 'e2e_portfolio_visitor');
  expect(visitorCookie?.httpOnly).toBe(true);
  expect(await page.evaluate(() => document.cookie)).not.toContain('e2e_portfolio_visitor');
  expect(stateHeaders?.['access-control-allow-origin']).toBe(frontendOrigin);
  expect(stateHeaders?.['access-control-allow-credentials']).toBe('true');

  await page.reload();
  await expect(page.getByRole('button', { name: 'Unlike E2E like project' })).toBeVisible();
  await expect(page.getByTestId('project-like-count')).toHaveText('1');

  await page.getByRole('button', { name: 'Unlike E2E like project' }).click();
  await expect(page.getByRole('button', { name: 'Like E2E like project' })).toBeVisible();
  await expect(page.getByTestId('project-like-count')).toHaveText('0');
});

test('prevents rapid duplicate like submissions and preserves state on failure', async ({
  page,
}, testInfo) => {
  const rapidSlug = projectSlug('e2e-project-rapid', testInfo);
  const failureSlug = projectSlug('e2e-project-like-failure', testInfo);
  let likeRequests = 0;
  page.on('request', (request) => {
    if (request.method() === 'POST' && request.url().endsWith(`/projects/${rapidSlug}/likes`)) {
      likeRequests += 1;
    }
  });

  await page.goto(`/en/projects/${rapidSlug}`);
  const rapidButton = page.getByRole('button', { name: 'Like E2E rapid like project' });
  await expect(rapidButton).toBeEnabled();
  await rapidButton.evaluate((button: HTMLButtonElement) => {
    button.click();
    button.click();
    button.click();
  });
  await expect(page.getByRole('button', { name: 'Unlike E2E rapid like project' })).toBeVisible();
  await expect(page.getByTestId('project-like-count')).toHaveText('1');
  expect(likeRequests).toBe(1);

  await page.route(`**/api/v1/projects/${failureSlug}/likes`, async (route) => {
    if (route.request().method() === 'POST') {
      await route.fulfill({
        contentType: 'application/json',
        status: 500,
        body: JSON.stringify({ message: 'Expected E2E like failure' }),
      });
    } else {
      await route.continue();
    }
  });

  await page.goto(`/en/projects/${failureSlug}`);
  await page.getByRole('button', { name: 'Like E2E like failure project' }).click();
  await expect(page.getByText('The like could not be updated. Please try again.')).toBeVisible();
  await expect(page.getByRole('button', { name: 'Like E2E like failure project' })).toBeVisible();
  await expect(page.getByTestId('project-like-count')).toHaveText('0');
});

test('submits contact messages with validation, consent, live confirmation, and no browser storage leakage', async ({
  page,
}) => {
  const unique = `contact-${Date.now()}@example.test`;
  let contactRequests = 0;
  page.on('request', (request) => {
    if (request.method() === 'POST' && request.url().endsWith('/api/v1/contact')) {
      contactRequests += 1;
    }
  });

  await page.goto('/en/contact');
  await page.getByRole('button', { name: 'Send message' }).click();
  await expect(page.getByText('Please check this field.').first()).toBeVisible();
  await expect(page.locator('app-contact-form').getByLabel('Name')).toHaveAttribute(
    'aria-invalid',
    'true',
  );

  await fillContactForm(page, {
    email: unique,
    message: `This fictional browser message ${unique} should be accepted once.`,
    name: 'E2E Contact Person',
  });
  await page.getByRole('button', { name: 'Send message' }).click();
  await expect(page.getByText('Message received successfully.')).toBeVisible();
  expect(contactRequests).toBe(1);

  const storageDump = await page.evaluate(() => JSON.stringify({ localStorage, sessionStorage }));
  expect(storageDump).not.toContain(unique);
  expect(await page.locator('body').innerText()).not.toMatch(
    /visitor_id_hash|ip_hash|admin_notes/i,
  );
});

test('submits testimonials for moderation without immediate public exposure', async ({ page }) => {
  const uniqueContent = `This fictional testimonial ${Date.now()} is for moderation only.`;

  await page.goto('/en/contact');
  await page.getByRole('button', { name: 'Submit testimonial' }).click();
  await expect(
    page.locator('app-testimonial-form').getByText('Please check this field.').first(),
  ).toBeVisible();

  await fillTestimonialForm(page, {
    content: uniqueContent,
    email: `testimonial-${Date.now()}@example.test`,
    name: 'E2E Testimonial Person',
  });
  await page.getByRole('button', { name: 'Submit testimonial' }).click();
  await expect(page.getByText('Testimonial received for review.')).toBeVisible();

  const publicTestimonials = await page.evaluate(async (url) => {
    const response = await fetch(`${url}/testimonials?locale=en`, { credentials: 'include' });
    return response.text();
  }, apiBaseUrl);
  expect(publicTestimonials).not.toContain(uniqueContent);
  await expect(page.locator('body')).not.toContainText(
    /contact_email|reviewed_at|admin_notes|internal_notes/i,
  );
});

test('renders configured WhatsApp action and hides it when configuration is missing on client navigation', async ({
  page,
}) => {
  await page.goto('/en/contact');
  const whatsapp = page.getByRole('link', { name: 'Contact on WhatsApp' });
  await expect(whatsapp).toHaveAttribute('href', /^https:\/\/wa\.me\/15550101010\?text=/);
  await expect(whatsapp).toHaveAttribute('target', '_blank');
  await expect(whatsapp).toHaveAttribute('rel', /noopener/);
  const whatsappHref = new URL((await whatsapp.getAttribute('href')) ?? '');
  expect(whatsappHref.searchParams.get('text')).toBe(
    'Hello, I would like to discuss an E2E project.',
  );

  await page.route('**/api/v1/site**', async (route) => {
    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({
        data: { navigation: [], services: [], settings: {}, skills: [], social_links: [] },
      }),
    });
  });
  await page.goto('/en');
  await page.locator('#main-content').getByRole('link', { name: 'Contact', exact: true }).click();
  await expect(page.getByRole('link', { name: 'Contact on WhatsApp' })).toHaveCount(0);
});

test('supports Arabic RTL interaction pages without private data or failed browser requests', async ({
  page,
}) => {
  await page.goto('/ar/contact');
  await expect(page.locator('html')).toHaveAttribute('lang', 'ar');
  await expect(page.locator('html')).toHaveAttribute('dir', 'rtl');
  await expect(page.locator('body')).not.toContainText(/visitor_id_hash|ip_hash|secret|admin/i);
});

async function fillContactForm(
  page: Page,
  values: { email: string; message: string; name: string },
): Promise<void> {
  const form = page.locator('app-contact-form');
  await form.getByLabel('Name').fill(values.name);
  await form.getByLabel('Email').fill(values.email);
  await form.getByRole('textbox', { name: 'Message' }).fill(values.message);
  await form.getByLabel(/I agree that my details/).check();
}

async function fillTestimonialForm(
  page: Page,
  values: { content: string; email: string; name: string },
): Promise<void> {
  const form = page.locator('app-testimonial-form');
  await form.getByLabel('Name').fill(values.name);
  await form.getByLabel('Verification email').fill(values.email);
  await form.getByRole('textbox', { name: 'Testimonial' }).fill(values.content);
  await form.getByLabel(/I agree that this testimonial/).check();
}

function projectSlug(base: string, testInfo: TestInfo): string {
  return `${base}-${testInfo.project.name}`;
}
