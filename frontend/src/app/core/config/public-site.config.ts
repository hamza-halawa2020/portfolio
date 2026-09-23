export interface PublicSiteConfig {
  readonly apiBaseUrl: string;
  readonly publicOrigin: string;
}

interface RuntimePortfolioConfig {
  readonly apiBaseUrl?: string;
  readonly publicOrigin?: string;
}

declare const process:
  | {
      readonly env?: Record<string, string | undefined>;
    }
  | undefined;

const runtimeConfig = (globalThis as { PORTFOLIO_PUBLIC_CONFIG?: RuntimePortfolioConfig }).PORTFOLIO_PUBLIC_CONFIG;
const serverEnv = typeof process === 'undefined' ? undefined : process.env;

export const publicSiteConfig: PublicSiteConfig = {
  apiBaseUrl: runtimeConfig?.apiBaseUrl ?? serverEnv?.['PORTFOLIO_API_BASE_URL'] ?? 'http://localhost:8000/api/v1',
  publicOrigin: runtimeConfig?.publicOrigin ?? serverEnv?.['PORTFOLIO_PUBLIC_ORIGIN'] ?? 'https://example.com',
};
