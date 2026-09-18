import { DOCUMENT } from '@angular/common';
import { inject, Injectable } from '@angular/core';
import { Meta, Title } from '@angular/platform-browser';
import { AppLocale } from '../i18n/locale.service';
import { publicSiteConfig } from '../config/public-site.config';

export interface SeoAlternate {
  readonly locale: AppLocale;
  readonly path: string;
}

export interface SeoMeta {
  readonly title: string;
  readonly description: string;
  readonly path: string;
  readonly locale: AppLocale;
  readonly alternates?: readonly SeoAlternate[];
  readonly noindex?: boolean;
}

@Injectable({ providedIn: 'root' })
export class SeoService {
  private readonly document = inject(DOCUMENT);
  private readonly meta = inject(Meta);
  private readonly title = inject(Title);

  apply(meta: SeoMeta): void {
    this.title.setTitle(meta.title);
    this.meta.updateTag({ content: meta.description, name: 'description' });
    this.meta.updateTag({ content: meta.noindex ? 'noindex, nofollow' : 'noindex, follow', name: 'robots' });
    this.setLink('canonical', this.absoluteUrl(meta.path));
    this.clearAlternates();

    for (const alternate of meta.alternates ?? []) {
      this.addAlternate(alternate.locale, this.absoluteUrl(alternate.path));
    }
  }

  private absoluteUrl(path: string): string {
    const normalizedPath = path.startsWith('/') ? path : `/${path}`;

    return `${publicSiteConfig.publicOrigin.replace(/\/$/, '')}${normalizedPath}`;
  }

  private setLink(rel: string, href: string): void {
    let link = this.document.head.querySelector<HTMLLinkElement>(`link[rel="${rel}"]`);

    if (!link) {
      link = this.document.createElement('link');
      link.rel = rel;
      this.document.head.appendChild(link);
    }

    link.href = href;
  }

  private addAlternate(locale: AppLocale, href: string): void {
    const link = this.document.createElement('link');
    link.rel = 'alternate';
    link.hreflang = locale;
    link.href = href;
    link.dataset['managedBy'] = 'seo-service';
    this.document.head.appendChild(link);
  }

  private clearAlternates(): void {
    this.document.head.querySelectorAll('link[rel="alternate"][data-managed-by="seo-service"]').forEach((link) => {
      link.remove();
    });
  }
}
