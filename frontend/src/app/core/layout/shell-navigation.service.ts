import { Injectable, signal } from '@angular/core';
import { AppLocale, NavKey } from '../i18n/locale.service';

export interface ShellNavItem {
  readonly key: Exclude<NavKey, 'privacy'>;
  readonly path: Record<AppLocale, string>;
}

export interface LocalizedSlugMapping {
  readonly type: 'project' | 'blog';
  readonly slugs: Partial<Record<AppLocale, string>>;
}

const ITEMS: readonly ShellNavItem[] = [
  { key: 'home', path: { ar: '/ar', en: '/en' } },
  { key: 'projects', path: { ar: '/ar/projects', en: '/en/projects' } },
  { key: 'services', path: { ar: '/ar/services', en: '/en/services' } },
  { key: 'blog', path: { ar: '/ar/blog', en: '/en/blog' } },
  { key: 'about', path: { ar: '/ar/about', en: '/en/about' } },
  { key: 'contact', path: { ar: '/ar/contact', en: '/en/contact' } },
];

const STATIC_SEGMENT_MAP: Record<string, string> = {
  about: 'about',
  blog: 'blog',
  contact: 'contact',
  privacy: 'privacy',
  projects: 'projects',
  services: 'services',
};

@Injectable({ providedIn: 'root' })
export class ShellNavigationService {
  private readonly localizedSlugSignal = signal<LocalizedSlugMapping | undefined>(undefined);

  readonly items = ITEMS;
  readonly localizedSlug = this.localizedSlugSignal.asReadonly();

  setLocalizedSlugMapping(mapping: LocalizedSlugMapping | undefined): void {
    this.localizedSlugSignal.set(mapping);
  }

  localizedUrl(pageKey: string, locale: AppLocale): string {
    return ITEMS.find((item) => item.key === pageKey)?.path[locale] ?? `/${locale}`;
  }

  switchLocaleUrl(
    url: string,
    targetLocale: AppLocale,
    localizedSlug?: LocalizedSlugMapping,
  ): string {
    const path = url.split('?')[0].split('#')[0];
    const parts = path.split('/').filter(Boolean);
    const currentLocale = parts[0] === 'ar' || parts[0] === 'en' ? parts[0] : 'en';
    const segment = parts[1] ?? '';

    if (currentLocale === targetLocale && parts.length > 0) {
      return path || `/${targetLocale}`;
    }

    if (!segment) {
      return `/${targetLocale}`;
    }

    if ((segment === 'projects' || segment === 'blog') && parts.length > 2) {
      const expectedType = segment === 'projects' ? 'project' : 'blog';
      const targetSlug =
        localizedSlug?.type === expectedType ? localizedSlug.slugs[targetLocale] : undefined;

      if (targetSlug) {
        return `/${targetLocale}/${segment}/${targetSlug}`;
      }

      return `/${targetLocale}/${segment}`;
    }

    return `/${targetLocale}/${STATIC_SEGMENT_MAP[segment] ?? ''}`.replace(/\/$/, '');
  }
}
