import { TestBed } from '@angular/core/testing';
import { Meta, Title } from '@angular/platform-browser';
import { SeoService } from './seo.service';

describe('SeoService', () => {
  beforeEach(() => {
    document.head.querySelectorAll('link[rel="canonical"], link[rel="alternate"]').forEach((link) => link.remove());
  });

  it('writes title, description, canonical, robots, and alternates', () => {
    const service = TestBed.inject(SeoService);
    const title = TestBed.inject(Title);
    const meta = TestBed.inject(Meta);

    service.apply({
      alternates: [
        { locale: 'en', path: '/en' },
        { locale: 'ar', path: '/ar' },
      ],
      description: 'A server-rendered portfolio shell.',
      locale: 'en',
      path: '/en',
      title: 'Home | Portfolio Platform',
    });

    expect(title.getTitle()).toBe('Home | Portfolio Platform');
    expect(meta.getTag('name="description"')?.content).toBe('A server-rendered portfolio shell.');
    expect(meta.getTag('name="robots"')?.content).toBe('noindex, follow');
    expect(document.head.querySelector('link[rel="canonical"]')?.getAttribute('href')).toBe('https://example.com/en');
    expect(document.head.querySelectorAll('link[rel="alternate"]').length).toBe(2);
  });
});
