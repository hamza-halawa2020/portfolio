export interface PublicSiteConfig {
  readonly apiBaseUrl: string;
  readonly publicOrigin: string;
}

export const publicSiteConfig: PublicSiteConfig = {
  apiBaseUrl: 'http://localhost/api/v1',
  publicOrigin: 'https://example.com',
};
