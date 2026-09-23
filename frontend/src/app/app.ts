import { Component, computed, inject, signal } from '@angular/core';
import { NavigationEnd, Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { filter } from 'rxjs';
import { LocaleService } from './core/i18n/locale.service';
import { ShellNavigationService } from './core/layout/shell-navigation.service';
import { ThemePreference, ThemeService } from './core/layout/theme.service';

@Component({
  imports: [RouterLink, RouterLinkActive, RouterOutlet],
  selector: 'app-root',
  styleUrl: './app.css',
  templateUrl: './app.html',
})
export class App {
  private readonly localeService = inject(LocaleService);
  private readonly navigation = inject(ShellNavigationService);
  private readonly router = inject(Router);
  protected readonly themeService = inject(ThemeService);

  protected readonly currentLocale = this.localeService.locale;
  protected readonly copy = this.localeService.copy;
  protected readonly navItems = this.navigation.items;
  protected readonly localizedSlug = this.navigation.localizedSlug;
  protected readonly isMenuOpen = signal(false);
  protected readonly isRtl = computed(() => this.currentLocale() === 'ar');
  protected readonly themeOptions: readonly ThemePreference[] = ['light', 'dark', 'system'];

  constructor() {
    this.localeService.activateLocaleFromUrl(this.router.url);

    this.router.events.pipe(filter((event) => event instanceof NavigationEnd)).subscribe((event) => {
      this.localeService.activateLocaleFromUrl(event.urlAfterRedirects);
      this.isMenuOpen.set(false);
    });
  }

  protected localizedUrl(pageKey: string): string {
    return this.navigation.localizedUrl(pageKey, this.currentLocale());
  }

  protected languageSwitchUrl(locale: 'en' | 'ar'): string {
    return this.navigation.switchLocaleUrl(this.router.url, locale, this.localizedSlug());
  }

  protected setLocale(locale: 'en' | 'ar'): void {
    this.localeService.activateLocale(locale, true);
  }

  protected setTheme(theme: ThemePreference): void {
    this.themeService.setPreference(theme);
  }

  protected toggleMenu(): void {
    this.isMenuOpen.update((isOpen) => !isOpen);
  }
}
