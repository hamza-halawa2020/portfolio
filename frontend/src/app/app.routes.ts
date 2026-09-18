import { Routes } from '@angular/router';

const publicPage = () => import('./pages/public-page/public-page').then((module) => module.PublicPage);

export const routes: Routes = [
  { path: '', pathMatch: 'full', redirectTo: 'en' },
  {
    path: ':locale',
    children: [
      { path: '', loadComponent: publicPage, data: { pageKey: 'home' } },
      { path: 'projects', loadComponent: publicPage, data: { pageKey: 'projects' } },
      { path: 'projects/:slug', loadComponent: publicPage, data: { pageKey: 'projectDetail' } },
      { path: 'about', loadComponent: publicPage, data: { pageKey: 'about' } },
      { path: 'services', loadComponent: publicPage, data: { pageKey: 'services' } },
      { path: 'blog', loadComponent: publicPage, data: { pageKey: 'blog' } },
      { path: 'blog/:slug', loadComponent: publicPage, data: { pageKey: 'blogDetail' } },
      { path: 'contact', loadComponent: publicPage, data: { pageKey: 'contact' } },
      { path: 'privacy', loadComponent: publicPage, data: { pageKey: 'privacy' } },
      { path: '**', loadComponent: publicPage, data: { pageKey: 'notFound' } },
    ],
  },
  { path: '**', loadComponent: publicPage, data: { pageKey: 'notFound' } },
];
