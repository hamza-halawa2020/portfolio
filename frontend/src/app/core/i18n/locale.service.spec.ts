import { TestBed } from '@angular/core/testing';
import { LocaleService } from './locale.service';

describe('LocaleService', () => {
  const storage = new Map<string, string>();

  beforeEach(() => {
    storage.clear();
    Object.defineProperty(globalThis, 'localStorage', {
      configurable: true,
      value: {
        getItem: (key: string) => storage.get(key) ?? null,
        setItem: (key: string, value: string) => storage.set(key, value),
      },
    });
    TestBed.resetTestingModule();
  });

  it('sets lang and dir from localized URLs', () => {
    const service = TestBed.inject(LocaleService);

    expect(service.activateLocaleFromUrl('/ar/projects')).toBe('ar');
    expect(document.documentElement.lang).toBe('ar');
    expect(document.documentElement.dir).toBe('rtl');

    expect(service.activateLocaleFromUrl('/en/blog')).toBe('en');
    expect(document.documentElement.lang).toBe('en');
    expect(document.documentElement.dir).toBe('ltr');
  });

  it('defaults unsupported route locales to English without browser detection', () => {
    const service = TestBed.inject(LocaleService);

    expect(service.activateLocaleFromUrl('/fr/projects')).toBe('en');
    expect(document.documentElement.lang).toBe('en');
    expect(document.documentElement.dir).toBe('ltr');
  });

  it('persists explicit locale choices safely', () => {
    const service = TestBed.inject(LocaleService);

    service.activateLocale('ar', true);

    expect(globalThis.localStorage.getItem('portfolio.locale')).toBe('ar');
    expect(service.persistedLocale()).toBe('ar');
  });

  it('falls back to English for missing localized dynamic values', () => {
    const service = TestBed.inject(LocaleService);

    service.activateLocale('ar');

    expect(service.localizedValue({ en: 'Fallback title' })).toBe('Fallback title');
    expect(service.localizedValue({})).toBe('');
  });
});
