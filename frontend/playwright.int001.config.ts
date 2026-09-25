import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env['PLAYWRIGHT_BASE_URL'];

if (!baseURL) {
  throw new Error('PLAYWRIGHT_BASE_URL is required for the INT-001 interaction suite.');
}

export default defineConfig({
  forbidOnly: true,
  reporter: 'line',
  testDir: './e2e/int001',
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
