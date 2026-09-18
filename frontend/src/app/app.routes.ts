import { Routes } from '@angular/router';

const publicPage = () => import('./pages/public-page/public-page').then((module) => module.PublicPage);

const englishSsrAliases: Routes = [
  { path: '', pathMatch: 'full', loadComponent: publicPage, data: { pageKey: 'home' } },
  { path: 'projects', loadComponent: publicPage, data: { pageKey: 'projects' } },
  { path: 'projects/:slug', loadComponent: publicPage, data: { pageKey: 'projectDetail' } },
  { path: 'about', loadComponent: publicPage, data: { pageKey: 'about' } },
  { path: 'services', loadComponent: publicPage, data: { pageKey: 'services' } },
  { path: 'blog', loadComponent: publicPage, data: { pageKey: 'blog' } },
  { path: 'blog/:slug', loadComponent: publicPage, data: { pageKey: 'blogDetail' } },
  { path: 'contact', loadComponent: publicPage, data: { pageKey: 'contact' } },
  { path: 'privacy', loadComponent: publicPage, data: { pageKey: 'privacy' } },
];

export const routes: Routes = [
  ...englishSsrAliases,
  { path: 'en', loadComponent: publicPage, data: { pageKey: 'home' } },
  { path: 'en/projects', loadComponent: publicPage, data: { pageKey: 'projects' } },
  { path: 'en/projects/:slug', loadComponent: publicPage, data: { pageKey: 'projectDetail' } },
  { path: 'en/about', loadComponent: publicPage, data: { pageKey: 'about' } },
  { path: 'en/services', loadComponent: publicPage, data: { pageKey: 'services' } },
  { path: 'en/blog', loadComponent: publicPage, data: { pageKey: 'blog' } },
  { path: 'en/blog/:slug', loadComponent: publicPage, data: { pageKey: 'blogDetail' } },
  { path: 'en/contact', loadComponent: publicPage, data: { pageKey: 'contact' } },
  { path: 'en/privacy', loadComponent: publicPage, data: { pageKey: 'privacy' } },
  { path: 'en/**', loadComponent: publicPage, data: { pageKey: 'notFound' } },
  { path: 'ar', loadComponent: publicPage, data: { pageKey: 'home' } },
  { path: 'ar/projects', loadComponent: publicPage, data: { pageKey: 'projects' } },
  { path: 'ar/projects/:slug', loadComponent: publicPage, data: { pageKey: 'projectDetail' } },
  { path: 'ar/about', loadComponent: publicPage, data: { pageKey: 'about' } },
  { path: 'ar/services', loadComponent: publicPage, data: { pageKey: 'services' } },
  { path: 'ar/blog', loadComponent: publicPage, data: { pageKey: 'blog' } },
  { path: 'ar/blog/:slug', loadComponent: publicPage, data: { pageKey: 'blogDetail' } },
  { path: 'ar/contact', loadComponent: publicPage, data: { pageKey: 'contact' } },
  { path: 'ar/privacy', loadComponent: publicPage, data: { pageKey: 'privacy' } },
  { path: 'ar/**', loadComponent: publicPage, data: { pageKey: 'notFound' } },
  { path: '**', loadComponent: publicPage, data: { pageKey: 'notFound' } },
];
