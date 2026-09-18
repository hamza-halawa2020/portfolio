import { expect, test } from '@playwright/test';

test('renders localized shell navigation and theme controls', async ({ page }) => {
  await page.goto('/en');

  await expect(page).toHaveTitle(/Home \| Portfolio Platform/);
  await expect(page.locator('html')).toHaveAttribute('lang', 'en');
  await expect(page.getByRole('navigation', { name: 'Primary navigation' })).toContainText('Projects');

  await page.getByRole('link', { name: 'AR' }).click();
  await expect(page).toHaveURL(/\/ar$/);
  await expect(page.locator('html')).toHaveAttribute('dir', 'rtl');

  await page.getByRole('button', { name: 'Dark' }).click();
  await expect(page.locator('html')).toHaveAttribute('data-bs-theme', 'dark');
});

test('returns a localized not found page for unknown routes', async ({ page }) => {
  const response = await page.goto('/unknown-route');

  expect(response?.status()).toBe(404);
  await expect(page.getByRole('heading', { level: 1 })).toContainText('Page not found');
});
