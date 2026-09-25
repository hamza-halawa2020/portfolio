process.env.INT001_PLAYWRIGHT_CONFIG =
  process.env.INT001_PLAYWRIGHT_CONFIG ?? 'playwright.qa002.config.ts';

await import('./int001-e2e-runner.mjs');

process.exit(process.exitCode ?? 0);
