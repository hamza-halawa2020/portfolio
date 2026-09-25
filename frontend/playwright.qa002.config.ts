import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env['PLAYWRIGHT_BASE_URL'];

if (!baseURL) {
  throw new Error('PLAYWRIGHT_BASE_URL is required for the isolated QA-002 harness.');
}

export default defineConfig({
  testDir: './e2e/qa002',
  forbidOnly: Boolean(process.env['CI']),
  reporter: 'line',
  timeout: 30_000,
  use: {
    baseURL,
    trace: 'on-first-retry',
  },
  projects: [
    {
      name: 'desktop-chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'mobile-chromium',
      use: { ...devices['Pixel 7'] },
    },
  ],
});
