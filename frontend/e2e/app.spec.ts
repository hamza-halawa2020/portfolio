import { expect, test } from '@playwright/test';

test('renders the initialized application shell', async ({ page }) => {
  await page.goto('/');

  await expect(page).toHaveTitle('Portfolio Platform');
  await expect(page.getByRole('main')).toContainText('The platform frontend is initialized.');
});
