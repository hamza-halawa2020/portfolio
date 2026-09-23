import { DOCUMENT, isPlatformBrowser } from '@angular/common';
import { effect, inject, Injectable, PLATFORM_ID, signal } from '@angular/core';

export type ThemePreference = 'light' | 'dark' | 'system';
type AppliedTheme = 'light' | 'dark';

@Injectable({ providedIn: 'root' })
export class ThemeService {
  private readonly document = inject(DOCUMENT);
  private readonly platformId = inject(PLATFORM_ID);
  private readonly storageKey = 'portfolio.theme';
  private readonly preferenceSignal = signal<ThemePreference>(this.readInitialPreference());

  readonly preference = this.preferenceSignal.asReadonly();

  constructor() {
    effect(() => this.applyTheme(this.preferenceSignal()));

    if (this.hasMatchMedia()) {
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (this.preferenceSignal() === 'system') {
          this.applyTheme('system');
        }
      });
    }
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
    this.document.documentElement.setAttribute('data-bs-theme', theme);
    this.document.documentElement.setAttribute('data-theme-preference', preference);
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

    if (!this.hasMatchMedia()) {
      return 'light';
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  private hasMatchMedia(): boolean {
    return isPlatformBrowser(this.platformId) && typeof window.matchMedia === 'function';
  }

  private storage(): Storage | null {
    if (!isPlatformBrowser(this.platformId) || typeof globalThis.localStorage === 'undefined') {
      return null;
    }

    return globalThis.localStorage;
  }
}
