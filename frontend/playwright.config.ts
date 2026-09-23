import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env['PLAYWRIGHT_BASE_URL'] ?? 'http://127.0.0.1:4200';
const useExternalServer = Boolean(process.env['PLAYWRIGHT_BASE_URL']);

export default defineConfig({
  testDir: './e2e',
  webServer: useExternalServer
    ? undefined
    : {
        command: 'npm run start -- --host 127.0.0.1',
        url: baseURL,
        reuseExistingServer: !process.env['CI'],
        timeout: 120_000,
      },
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
