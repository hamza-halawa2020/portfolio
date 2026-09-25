import { expect, test } from '@playwright/test';

test.beforeEach(async ({ page }) => {
  page.on('console', (message) => {
    if (
      message.text().includes('Failed to load resource: the server responded with a status of 404')
    ) {
      return;
    }

    expect(message.type(), message.text()).not.toBe('error');
  });
  page.on('pageerror', (error) => {
    throw error;
  });
});

test('renders localized shell navigation and theme controls', async ({ page, isMobile }) => {
  await page.goto('/en');

  await expect(page).toHaveTitle(/Home \| Portfolio Platform/);
  await expect(page.locator('html')).toHaveAttribute('lang', 'en');
  await expect(page.getByRole('navigation', { name: 'Primary navigation' })).toContainText(
    'Projects',
  );

  if (isMobile) {
    await page.getByRole('button', { name: 'Open navigation' }).click();
  }

  await page.getByRole('button', { name: 'Dark' }).click();
  await expect(page.locator('html')).toHaveAttribute('data-bs-theme', 'dark');

  await page.getByRole('link', { name: 'AR' }).click();
  await expect(page).toHaveURL(/\/ar$/);
  await expect(page).toHaveTitle(/الرئيسية \| منصة الملف الشخصي/);
  await expect(page.locator('html')).toHaveAttribute('lang', 'ar');
  await expect(page.locator('html')).toHaveAttribute('dir', 'rtl');
});

test('persists explicit theme preference after reload and supports system mode', async ({
  page,
  isMobile,
}) => {
  await page.emulateMedia({ colorScheme: 'dark' });
  await page.goto('/en');

  if (isMobile) {
    await page.getByRole('button', { name: 'Open navigation' }).click();
  }

  await page.getByRole('button', { name: 'Light' }).click();
  await expect(page.locator('html')).toHaveAttribute('data-bs-theme', 'light');

  await page.reload();
  await expect(page.locator('html')).toHaveAttribute('data-bs-theme', 'light');
  await expect(page.locator('html')).toHaveAttribute('data-theme-preference', 'light');

  if (isMobile) {
    await page.getByRole('button', { name: 'Open navigation' }).click();
  }

  await page.getByRole('button', { name: 'System' }).click();
  await expect(page.locator('html')).toHaveAttribute('data-bs-theme', 'dark');
  await expect(page.locator('html')).toHaveAttribute('data-theme-preference', 'system');
});

test('supports keyboard access to the skip link and mobile navigation', async ({
  page,
  isMobile,
}) => {
  await page.goto('/en');

  await page.keyboard.press('Tab');
  await expect(page.getByRole('link', { name: 'Skip to content' })).toBeFocused();

  if (isMobile) {
    await page.getByRole('button', { name: 'Open navigation' }).click();
    await expect(page.getByRole('link', { name: 'Services', exact: true })).toBeVisible();
  }
});

test('returns a localized not found page for unknown routes', async ({ page }) => {
  const response = await page.goto('/unknown-route');

  expect(response?.status()).toBe(404);
  await expect(page.getByRole('heading', { level: 1 })).toContainText('Page not found');
});

test('uses API-provided localized slugs when switching detail page languages', async ({
  page,
  isMobile,
}) => {
  await page.goto('/en/projects');
  const firstProjectHref = await page.locator('.content-card').first().getAttribute('href');
  expect(firstProjectHref).toContain('/en/projects/');
  await page.goto(firstProjectHref ?? '/en/projects');

  if (isMobile) {
    await page.getByRole('button', { name: 'Open navigation' }).click();
  }

  await expect(page.getByRole('link', { name: 'AR' })).toHaveAttribute(
    'href',
    /\/ar\/projects\/[^/]+$/,
  );
  await page.getByRole('link', { name: 'AR' }).click();
  await expect(page).toHaveURL(/\/ar\/projects\/[^/]+$/);
  expect(new URL(page.url()).pathname.split('/').pop()).not.toBe(
    firstProjectHref?.split('/').pop(),
  );
});

test('renders SEO metadata and JSON-LD in hydrated pages without duplicates', async ({
  page,
  isMobile,
}) => {
  await page.goto('/en/projects');

  await expect(page.locator('link[rel="canonical"]')).toHaveAttribute('href', /\/en\/projects$/);
  await expect(page.locator('meta[property="og:title"]')).toHaveAttribute('content', /Projects/);
  await expect(page.locator('meta[name="twitter:card"]')).toHaveAttribute(
    'content',
    'summary_large_image',
  );
  await expect(page.locator('link[rel="alternate"][hreflang="x-default"]')).toHaveCount(1);
  await expect(page.locator('script[type="application/ld+json"]')).not.toHaveCount(0);

  if (isMobile) {
    await page.getByRole('button', { name: 'Open navigation' }).click();
  }

  await page.getByRole('link', { name: 'Blog', exact: true }).click();
  await expect(page).toHaveURL(/\/en\/blog$/);
  await expect(page.locator('link[rel="canonical"]')).toHaveCount(1);
  await expect(page.locator('meta[property="og:title"]')).toHaveCount(1);
});
