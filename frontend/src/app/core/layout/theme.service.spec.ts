import { PLATFORM_ID } from '@angular/core';
import { TestBed } from '@angular/core/testing';
import { Meta } from '@angular/platform-browser';
import { ThemeService } from './theme.service';

describe('ThemeService', () => {
  const storage = new Map<string, string>();
  let mediaListener: ((event: MediaQueryListEvent) => void) | null;
  let prefersDark = false;

  beforeEach(() => {
    storage.clear();
    mediaListener = null;
    prefersDark = false;
    Object.defineProperty(globalThis, 'localStorage', {
      configurable: true,
      value: {
        getItem: (key: string) => storage.get(key) ?? null,
        removeItem: (key: string) => storage.delete(key),
        setItem: (key: string, value: string) => storage.set(key, value),
      },
    });
    Object.defineProperty(globalThis, 'matchMedia', {
      configurable: true,
      value: () => ({
        addEventListener: (_type: string, listener: (event: MediaQueryListEvent) => void) => {
          mediaListener = listener;
        },
        matches: prefersDark,
      }),
    });
    document.documentElement.removeAttribute('data-bs-theme');
    document.documentElement.removeAttribute('data-theme-preference');
    document.head
      .querySelectorAll('meta[name="theme-color"]')
      .forEach((element) => element.remove());
    TestBed.resetTestingModule();
  });

  it('applies and persists selected theme preference', () => {
    const service = TestBed.inject(ThemeService);

    service.setPreference('dark');

    expect(document.documentElement.dataset['bsTheme']).toBe('dark');
    expect(document.documentElement.dataset['themePreference']).toBe('dark');
    expect(globalThis.localStorage.getItem('portfolio.theme')).toBe('dark');
    expect(TestBed.inject(Meta).getTag('name="theme-color"')?.content).toBe('#101010');
  });

  it('resolves system preference and reacts to system changes', () => {
    prefersDark = true;
    const service = TestBed.inject(ThemeService);

    service.setPreference('system');
    expect(service.appliedTheme()).toBe('dark');

    prefersDark = false;
    mediaListener?.({ matches: false } as MediaQueryListEvent);

    expect(service.appliedTheme()).toBe('light');
    expect(document.documentElement.dataset['bsTheme']).toBe('light');
  });

  it('is safe during server rendering without storage or matchMedia', () => {
    Object.defineProperty(globalThis, 'localStorage', { configurable: true, value: undefined });
    Object.defineProperty(globalThis, 'matchMedia', { configurable: true, value: undefined });
    TestBed.resetTestingModule();
    TestBed.configureTestingModule({
      providers: [{ provide: PLATFORM_ID, useValue: 'server' }],
    });

    const service = TestBed.inject(ThemeService);
    service.setPreference('system');

    expect(service.preference()).toBe('system');
    expect(service.appliedTheme()).toBe('light');
    expect(document.documentElement.dataset['bsTheme']).toBe('light');
  });
});
