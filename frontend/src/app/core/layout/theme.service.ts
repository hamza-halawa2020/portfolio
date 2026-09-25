import { DOCUMENT, isPlatformBrowser } from '@angular/common';
import { effect, inject, Injectable, PLATFORM_ID, signal } from '@angular/core';
import { Meta } from '@angular/platform-browser';

export type ThemePreference = 'light' | 'dark' | 'system';
export type AppliedTheme = 'light' | 'dark';

@Injectable({ providedIn: 'root' })
export class ThemeService {
  private readonly document = inject(DOCUMENT);
  private readonly meta = inject(Meta);
  private readonly platformId = inject(PLATFORM_ID);
  private readonly storageKey = 'portfolio.theme';
  private readonly preferenceSignal = signal<ThemePreference>(this.readInitialPreference());
  private readonly appliedThemeSignal = signal<AppliedTheme>(
    this.resolveAppliedTheme(this.preferenceSignal()),
  );

  readonly preference = this.preferenceSignal.asReadonly();
  readonly appliedTheme = this.appliedThemeSignal.asReadonly();

  constructor() {
    effect(() => this.applyTheme(this.preferenceSignal()));
    this.systemThemeQuery()?.addEventListener('change', () => {
      if (this.preferenceSignal() === 'system') {
        this.applyTheme('system');
      }
    });
  }

  setPreference(preference: ThemePreference): void {
    this.preferenceSignal.set(preference);
    this.applyTheme(preference);

    const storage = this.storage();

    if (storage) {
      storage.setItem(this.storageKey, preference);
    }
  }

  private applyTheme(preference: ThemePreference): void {
    const theme = this.resolveAppliedTheme(preference);
    this.appliedThemeSignal.set(theme);
    this.document.documentElement.setAttribute('data-bs-theme', theme);
    this.document.documentElement.setAttribute('data-theme-preference', preference);
    this.meta.updateTag({ content: theme === 'dark' ? '#101010' : '#ffffff', name: 'theme-color' });
  }

  private readInitialPreference(): ThemePreference {
    const storage = this.storage();

    if (!storage) {
      return 'system';
    }

    const stored = storage.getItem(this.storageKey);

    return stored === 'light' || stored === 'dark' || stored === 'system' ? stored : 'system';
  }

  private resolveAppliedTheme(preference: ThemePreference): AppliedTheme {
    if (preference !== 'system') {
      return preference;
    }

    return this.systemThemeQuery()?.matches ? 'dark' : 'light';
  }

  private systemThemeQuery(): MediaQueryList | null {
    if (!isPlatformBrowser(this.platformId) || typeof globalThis.matchMedia !== 'function') {
      return null;
    }

    return globalThis.matchMedia('(prefers-color-scheme: dark)');
  }

  private storage(): Storage | null {
    if (!isPlatformBrowser(this.platformId) || typeof globalThis.localStorage === 'undefined') {
      return null;
    }

    return globalThis.localStorage;
  }
}
