import { RenderMode, ServerRoute } from '@angular/ssr';

export const serverRoutes: ServerRoute[] = [
  {
    path: '',
    renderMode: RenderMode.Server,
  },
  {
    path: 'projects',
    renderMode: RenderMode.Server,
  },
  {
    path: 'projects/:slug',
    renderMode: RenderMode.Server,
  },
  {
    path: 'about',
    renderMode: RenderMode.Server,
  },
  {
    path: 'services',
    renderMode: RenderMode.Server,
  },
  {
    path: 'blog',
    renderMode: RenderMode.Server,
  },
  {
    path: 'blog/:slug',
    renderMode: RenderMode.Server,
  },
  {
    path: 'contact',
    renderMode: RenderMode.Server,
  },
  {
    path: 'privacy',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en',
    renderMode: RenderMode.Server,
  },
  {
    path: 'ar',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en/projects',
    renderMode: RenderMode.Server,
  },
  {
    path: 'ar/projects',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en/projects/:slug',
    renderMode: RenderMode.Server,
  },
  {
    path: 'ar/projects/:slug',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en/about',
    renderMode: RenderMode.Server,
  },
  {
    path: 'ar/about',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en/services',
    renderMode: RenderMode.Server,
  },
  {
    path: 'ar/services',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en/blog',
    renderMode: RenderMode.Server,
  },
  {
    path: 'ar/blog',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en/blog/:slug',
    renderMode: RenderMode.Server,
  },
  {
    path: 'ar/blog/:slug',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en/contact',
    renderMode: RenderMode.Server,
  },
  {
    path: 'ar/contact',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en/privacy',
    renderMode: RenderMode.Server,
  },
  {
    path: 'ar/privacy',
    renderMode: RenderMode.Server,
  },
  {
    path: 'en/**',
    renderMode: RenderMode.Server,
    status: 404,
  },
  {
    path: 'ar/**',
    renderMode: RenderMode.Server,
    status: 404,
  },
  {
    path: '**',
    renderMode: RenderMode.Server,
    status: 404,
  },
];
