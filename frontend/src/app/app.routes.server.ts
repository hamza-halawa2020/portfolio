import { RenderMode, ServerRoute } from '@angular/ssr';

export const serverRoutes: ServerRoute[] = [
  {
    path: '',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale/projects',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale/projects/:slug',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale/about',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale/services',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale/blog',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale/blog/:slug',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale/contact',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale/privacy',
    renderMode: RenderMode.Server,
  },
  {
    path: ':locale/**',
    renderMode: RenderMode.Server,
    status: 404,
  },
  {
    path: '**',
    renderMode: RenderMode.Server,
    status: 404,
  },
];
