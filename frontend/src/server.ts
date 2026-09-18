import {
  AngularNodeAppEngine,
  createNodeRequestHandler,
  isMainModule,
  writeResponseToNodeResponse,
} from '@angular/ssr/node';
import express from 'express';
import { join } from 'node:path';

const browserDistFolder = join(import.meta.dirname, '../browser');

const app = express();
const angularApp = new AngularNodeAppEngine({
  allowedHosts: (process.env['NG_ALLOWED_HOSTS'] ?? 'localhost,127.0.0.1')
    .split(',')
    .map((host) => host.trim())
    .filter(Boolean),
});

/**
 * Example Express Rest API endpoints can be defined here.
 * Uncomment and define endpoints as necessary.
 *
 * Example:
 * ```ts
 * app.get('/api/{*splat}', (req, res) => {
 *   // Handle API request
 * });
 * ```
 */

/**
 * Serve static files from /browser
 */
app.use(
  express.static(browserDistFolder, {
    maxAge: '1y',
    index: false,
    redirect: false,
  }),
);

app.get('/', (_req, res) => {
  res.redirect(302, '/en');
});

/**
 * Handle all other requests by rendering the Angular application.
 */
app.use((req, res, next) => {
  angularApp
    .handle(req)
    .then(async (response) =>
      response ? writeResponseToNodeResponse(await withShellFallbackText(response, req.originalUrl), res) : next(),
    )
    .catch(next);
});

async function withShellFallbackText(response: Response, originalUrl: string): Promise<Response> {
  const contentType = response.headers.get('content-type') ?? '';

  if (!contentType.includes('text/html')) {
    return response;
  }

  const copy = shellFallbackCopy(originalUrl);
  const html = await response.text();
  const transformed = html
    .replace(/(<p[^>]*class="eyebrow"[^>]*>)(?:<!--ngetn-->)?(<\/p>)/, `$1${escapeHtml(copy.eyebrow)}$2`)
    .replace(/(<h1[^>]*>)(?:<!--ngetn-->)?(<\/h1>)/, `$1${escapeHtml(copy.title)}$2`)
    .replace(/(<p[^>]*class="lede"[^>]*>)(?:<!--ngetn-->)?(<\/p>)/, `$1${escapeHtml(copy.description)}$2`)
    .replace(/(<p[^>]*class="page-note"[^>]*>)(?:<!--ngetn-->)?(<\/p>)/, `$1${escapeHtml(copy.note)}$2`);

  const headers = new Headers(response.headers);
  headers.delete('content-length');

  return new Response(transformed, {
    headers,
    status: response.status,
    statusText: response.statusText,
  });
}

function shellFallbackCopy(originalUrl: string): { description: string; eyebrow: string; note: string; title: string } {
  const path = originalUrl.split('?')[0].split('#')[0] || '/en';
  const segments = path.split('/').filter(Boolean);
  const section = segments[1] ?? 'home';
  const isDetail = segments.length > 2;
  const titles: Record<string, string> = {
    about: 'About',
    blog: isDetail ? 'Blog detail' : 'Blog',
    contact: 'Contact',
    home: 'Home',
    privacy: 'Privacy',
    projects: isDetail ? 'Project detail' : 'Projects',
    services: 'Services',
  };

  return {
    description:
      segments[0] === 'en' && section in titles
        ? 'This server-rendered route placeholder is intentionally noindexed until full page content is implemented.'
        : 'The page you requested was not found.',
    eyebrow: 'Frontend foundation',
    note:
      'This route is intentionally wired for FE-001. Full page content, API data, and interaction states remain in later tasks.',
    title: segments[0] === 'en' && section in titles ? titles[section] : 'Page not found',
  };
}

function escapeHtml(value: string): string {
  return value
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

/**
 * Start the server if this module is the main entry point, or it is ran via PM2.
 * The server listens on the port defined by the `PORT` environment variable, or defaults to 4000.
 */
if (isMainModule(import.meta.url) || process.env['pm_id']) {
  const port = process.env['PORT'] || 4000;
  app.listen(port, (error) => {
    if (error) {
      throw error;
    }

    console.log(`Node Express server listening on http://localhost:${port}`);
  });
}

/**
 * Request handler used by the Angular CLI (for dev-server and during build) or Firebase Cloud Functions.
 */
export const reqHandler = createNodeRequestHandler(app);
