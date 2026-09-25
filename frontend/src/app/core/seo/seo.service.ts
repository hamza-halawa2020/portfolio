import { DOCUMENT } from '@angular/common';
import { inject, Injectable } from '@angular/core';
import { Meta, Title } from '@angular/platform-browser';
import { AppLocale } from '../i18n/locale.service';
import { publicSiteConfig } from '../config/public-site.config';

export interface SeoAlternate {
  readonly locale: AppLocale | 'x-default';
  readonly path: string;
}

export interface SeoMeta {
  readonly title: string;
  readonly description: string;
  readonly path: string;
  readonly locale: AppLocale;
  readonly alternates?: readonly SeoAlternate[];
  readonly canonicalUrl?: string | null;
  readonly imageUrl?: string | null;
  readonly jsonLd?: readonly unknown[];
  readonly noindex?: boolean;
  readonly type?: 'website' | 'article';
}

@Injectable({ providedIn: 'root' })
export class SeoService {
  private readonly document = inject(DOCUMENT);
  private readonly meta = inject(Meta);
  private readonly title = inject(Title);

  apply(meta: SeoMeta): void {
    const canonical = this.safeAbsolute(meta.canonicalUrl) ?? this.absoluteUrl(meta.path);

    this.title.setTitle(meta.title);
    this.meta.updateTag({ content: meta.description, name: 'description' });
    this.meta.updateTag({
      content: meta.noindex ? 'noindex, nofollow' : 'index, follow',
      name: 'robots',
    });
    this.meta.updateTag({ content: meta.title, property: 'og:title' });
    this.meta.updateTag({ content: meta.description, property: 'og:description' });
    this.meta.updateTag({ content: canonical, property: 'og:url' });
    this.meta.updateTag({
      content: meta.type === 'article' ? 'article' : 'website',
      property: 'og:type',
    });
    this.meta.updateTag({ content: meta.locale, property: 'og:locale' });
    this.meta.updateTag({ content: 'summary_large_image', name: 'twitter:card' });
    this.meta.updateTag({ content: meta.title, name: 'twitter:title' });
    this.meta.updateTag({ content: meta.description, name: 'twitter:description' });
    this.setLink('canonical', canonical);
    this.clearAlternates();
    this.clearManagedMeta('property', 'og:image');
    this.clearManagedMeta('name', 'twitter:image');
    this.clearJsonLd();

    for (const alternate of meta.alternates ?? []) {
      this.addAlternate(alternate.locale, this.absoluteUrl(alternate.path));
    }

    const imageUrl = this.safeAbsolute(meta.imageUrl);
    if (imageUrl) {
      this.addManagedMeta('property', 'og:image', imageUrl);
      this.addManagedMeta('name', 'twitter:image', imageUrl);
    }

    for (const schema of meta.jsonLd ?? []) {
      this.addJsonLd(schema);
    }
  }

  absoluteUrl(path: string): string {
    const normalizedPath = path.startsWith('/') ? path : `/${path}`;

    return `${publicSiteConfig.publicOrigin.replace(/\/$/, '')}${normalizedPath}`;
  }

  private safeAbsolute(url: string | null | undefined): string | null {
    if (!url) {
      return null;
    }

    if (url.startsWith('/')) {
      return this.absoluteUrl(url);
    }

    try {
      const parsed = new URL(url);

      return parsed.protocol === 'https:' ? parsed.toString() : null;
    } catch {
      return null;
    }
  }

  private setLink(rel: string, href: string): void {
    let link = this.document.head.querySelector<HTMLLinkElement>(`link[rel="${rel}"]`);

    if (!link) {
      link = this.document.createElement('link');
      link.setAttribute('rel', rel);
      this.document.head.appendChild(link);
    }

    link.setAttribute('href', href);
  }

  private addAlternate(locale: AppLocale | 'x-default', href: string): void {
    const link = this.document.createElement('link');
    link.setAttribute('rel', 'alternate');
    link.setAttribute('hreflang', locale);
    link.setAttribute('href', href);
    link.setAttribute('data-managed-by', 'seo-service');
    this.document.head.appendChild(link);
  }

  private clearAlternates(): void {
    this.document.head
      .querySelectorAll('link[rel="alternate"][data-managed-by="seo-service"]')
      .forEach((link) => {
        link.remove();
      });
  }

  private addManagedMeta(attribute: 'name' | 'property', key: string, content: string): void {
    const tag = this.document.createElement('meta');
    tag.setAttribute(attribute, key);
    tag.setAttribute('content', content);
    tag.setAttribute('data-managed-by', 'seo-service');
    this.document.head.appendChild(tag);
  }

  private clearManagedMeta(attribute: 'name' | 'property', key: string): void {
    this.document.head
      .querySelectorAll(`meta[${attribute}="${key}"][data-managed-by="seo-service"]`)
      .forEach((tag) => {
        tag.remove();
      });
  }

  private addJsonLd(schema: unknown): void {
    const script = this.document.createElement('script');
    script.setAttribute('type', 'application/ld+json');
    script.setAttribute('data-managed-by', 'seo-service');
    script.textContent = JSON.stringify(schema).replace(/</g, '\\u003C');
    this.document.head.appendChild(script);
  }

  private clearJsonLd(): void {
    this.document.head
      .querySelectorAll('script[type="application/ld+json"][data-managed-by="seo-service"]')
      .forEach((script) => {
        script.remove();
      });
  }
}
