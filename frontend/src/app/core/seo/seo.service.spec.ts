import { TestBed } from '@angular/core/testing';
import { Meta, Title } from '@angular/platform-browser';
import { SeoService } from './seo.service';

describe('SeoService', () => {
  beforeEach(() => {
    document.head
      .querySelectorAll(
        'link[rel="canonical"], link[rel="alternate"], meta[data-managed-by="seo-service"], script[data-managed-by="seo-service"]',
      )
      .forEach((node) => node.remove());
  });

  it('writes metadata, social tags, alternates, and escaped JSON-LD without duplicates', () => {
    const service = TestBed.inject(SeoService);
    const title = TestBed.inject(Title);
    const meta = TestBed.inject(Meta);

    service.apply({
      alternates: [
        { locale: 'en', path: '/en' },
        { locale: 'ar', path: '/ar' },
        { locale: 'x-default', path: '/en' },
      ],
      description: 'A server-rendered portfolio shell.',
      imageUrl: '/storage/cover.webp',
      jsonLd: [{ '@context': 'https://schema.org', '@type': 'WebSite', name: '<Portfolio>' }],
      locale: 'en',
      path: '/en',
      title: 'Home | Portfolio Platform',
    });

    expect(title.getTitle()).toBe('Home | Portfolio Platform');
    expect(meta.getTag('name="description"')?.content).toBe('A server-rendered portfolio shell.');
    expect(meta.getTag('name="robots"')?.content).toBe('index, follow');
    expect(meta.getTag('property="og:title"')?.content).toBe('Home | Portfolio Platform');
    expect(meta.getTag('name="twitter:card"')?.content).toBe('summary_large_image');
    expect(document.head.querySelector('link[rel="canonical"]')?.getAttribute('href')).toBe(
      'https://example.com/en',
    );
    expect(document.head.querySelectorAll('link[rel="alternate"]').length).toBe(3);
    expect(document.head.querySelector('meta[property="og:image"]')?.getAttribute('content')).toBe(
      'https://example.com/storage/cover.webp',
    );
    expect(
      document.head.querySelector('script[type="application/ld+json"]')?.textContent,
    ).toContain('\\u003CPortfolio>');

    service.apply({
      description: 'Second page.',
      locale: 'en',
      path: '/en/projects',
      title: 'Projects | Portfolio Platform',
    });

    expect(document.head.querySelectorAll('script[type="application/ld+json"]').length).toBe(0);
    expect(document.head.querySelectorAll('link[rel="alternate"]').length).toBe(0);
    expect(document.head.querySelectorAll('meta[property="og:image"]').length).toBe(0);
  });
});
