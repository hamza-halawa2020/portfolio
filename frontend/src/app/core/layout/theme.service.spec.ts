import { TestBed } from '@angular/core/testing';
import { ThemeService } from './theme.service';

describe('ThemeService', () => {
  const storage = new Map<string, string>();

  beforeEach(() => {
    storage.clear();
    Object.defineProperty(globalThis, 'localStorage', {
      configurable: true,
      value: {
        clear: () => storage.clear(),
        getItem: (key: string) => storage.get(key) ?? null,
        removeItem: (key: string) => storage.delete(key),
        setItem: (key: string, value: string) => storage.set(key, value),
      },
    });
    document.documentElement.removeAttribute('data-bs-theme');
    document.documentElement.removeAttribute('data-theme-preference');
    TestBed.resetTestingModule();
  });

  it('applies and persists selected theme preference', () => {
    const service = TestBed.inject(ThemeService);

    service.setPreference('dark');

    expect(document.documentElement.dataset['bsTheme']).toBe('dark');
    expect(document.documentElement.dataset['themePreference']).toBe('dark');
    expect(globalThis.localStorage.getItem('portfolio.theme')).toBe('dark');
  });
});
