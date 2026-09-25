import { expect, test } from '@playwright/test';

const privatePayloadPattern =
  /APP_KEY|VISITOR_HASH_SECRET|visitor_id_hash|ip_hash|user_agent_hash|admin_notes|internal_notes|contact_email/i;

test.beforeEach(async ({ page }) => {
  page.on('console', (message) => {
    const text = message.text();
    expect(text).not.toMatch(privatePayloadPattern);

    if (text.includes('Failed to load resource: the server responded with a status of 404')) {
      return;
    }

    expect(message.type(), text).not.toBe('error');
  });

  page.on('pageerror', (error) => {
    throw error;
  });
});

test('key public pages expose accessible landmarks, headings, direction, and image text', async ({
  page,
}) => {
  const pages = [
    { dir: 'ltr', lang: 'en', path: '/en' },
    { dir: 'rtl', lang: 'ar', path: '/ar' },
    { dir: 'ltr', lang: 'en', path: '/en/projects' },
    { dir: 'rtl', lang: 'ar', path: '/ar/projects' },
    { dir: 'ltr', lang: 'en', path: '/en/contact' },
    { dir: 'rtl', lang: 'ar', path: '/ar/contact' },
    { dir: 'ltr', lang: 'en', path: '/en/privacy' },
    { dir: 'rtl', lang: 'ar', path: '/ar/privacy' },
  ];

  for (const target of pages) {
    const response = await page.goto(target.path);
    expect(response?.status(), target.path).toBe(200);
    await expect(page.locator('html')).toHaveAttribute('lang', target.lang);
    await expect(page.locator('html')).toHaveAttribute('dir', target.dir);
    await expect(page.locator('#main-content')).toHaveCount(1);
    await expect(
      page.getByRole('navigation', { name: /Primary navigation|التنقل الرئيسي/ }),
    ).toBeVisible();
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
    await expect(page.getByRole('contentinfo')).toBeVisible();

    const imagesWithoutAlt = await page
      .locator('img:not([alt]), img[alt=""]')
      .evaluateAll((images) => images.length);
    expect(imagesWithoutAlt, `${target.path} images without alt text`).toBe(0);

    const horizontalOverflow = await page.evaluate(
      () => document.documentElement.scrollWidth > document.documentElement.clientWidth + 1,
    );
    expect(horizontalOverflow, `${target.path} horizontal overflow`).toBe(false);
    await expect(page.locator('body')).not.toContainText(privatePayloadPattern);
  }
});

test('keyboard and form controls remain accessible in English and Arabic contact pages', async ({
  page,
}) => {
  await page.goto('/en/contact');
  await page.keyboard.press('Tab');
  await expect(page.getByRole('link', { name: 'Skip to content' })).toBeFocused();

  await expect(page.locator('app-contact-form').getByLabel('Name')).toBeVisible();
  await expect(page.locator('app-contact-form').getByLabel('Email')).toBeVisible();
  await expect(
    page.locator('app-contact-form').getByRole('textbox', { name: 'Message' }),
  ).toBeVisible();
  await expect(
    page.locator('app-contact-form').getByLabel(/I agree that my details/),
  ).toBeVisible();
  await expect(page.locator('app-testimonial-form').getByLabel('Verification email')).toBeVisible();

  await page.goto('/ar/contact');
  await expect(page.locator('html')).toHaveAttribute('dir', 'rtl');
  await expect(page.locator('app-contact-form input, app-contact-form textarea')).not.toHaveCount(
    0,
  );
  await expect(
    page.locator('app-testimonial-form input, app-testimonial-form textarea'),
  ).not.toHaveCount(0);
});

test('public rendering and project interaction bootstrapping do not leak private browser state', async ({
  page,
}) => {
  await page.goto('/en/projects');
  const firstProjectHref = await page.locator('.content-card').first().getAttribute('href');
  expect(firstProjectHref).toContain('/en/projects/');

  await page.goto(firstProjectHref ?? '/en/projects');
  await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
  await expect(page.locator('body')).not.toContainText(privatePayloadPattern);

  const browserReadableCookies = await page.evaluate(() => document.cookie);
  expect(browserReadableCookies).not.toContain('portfolio_visitor');
  expect(browserReadableCookies).not.toContain('e2e_portfolio_visitor');

  const storageDump = await page.evaluate(() =>
    JSON.stringify({
      localStorage: Object.entries(localStorage),
      sessionStorage: Object.entries(sessionStorage),
    }),
  );
  expect(storageDump).not.toMatch(/liked|visitor|hash|contact|testimonial/i);
});

test('documents local Core Web Vitals timing inputs with generous SSR smoke thresholds', async ({
  page,
}) => {
  const response = await page.goto('/en');
  expect(response?.status()).toBe(200);
  await expect(page.getByRole('heading', { level: 1 })).toBeVisible();

  const timing = await page.evaluate(() => {
    const navigation = performance.getEntriesByType('navigation')[0] as
      PerformanceNavigationTiming | undefined;

    return {
      domContentLoaded: navigation?.domContentLoadedEventEnd ?? 0,
      loadEventEnd: navigation?.loadEventEnd ?? 0,
      transferSize: navigation?.transferSize ?? 0,
    };
  });

  expect(timing.domContentLoaded).toBeGreaterThan(0);
  expect(timing.domContentLoaded).toBeLessThan(8_000);
  expect(timing.loadEventEnd).toBeGreaterThan(0);
  expect(timing.loadEventEnd).toBeLessThan(10_000);
  expect(timing.transferSize).toBeGreaterThanOrEqual(0);
});
