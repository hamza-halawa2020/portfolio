import { Component, computed, inject, signal } from '@angular/core';
import { NavigationEnd, Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { filter } from 'rxjs';
import { LocaleService } from './core/i18n/locale.service';
import { ShellNavigationService } from './core/layout/shell-navigation.service';
import { ThemePreference, ThemeService } from './core/layout/theme.service';
import { SeoService } from './core/seo/seo.service';

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
  private readonly seo = inject(SeoService);
  protected readonly themeService = inject(ThemeService);

  protected readonly currentLocale = this.localeService.locale;
  protected readonly copy = this.localeService.copy;
  protected readonly navItems = this.navigation.items;
  protected readonly isMenuOpen = signal(false);
  protected readonly routeActive = signal(false);
  protected readonly isRtl = computed(() => this.currentLocale() === 'ar');
  protected readonly themeOptions: readonly ThemePreference[] = ['light', 'dark', 'system'];
  protected readonly fallbackPage = computed(() => this.englishFallbackForUrl(this.router.url));

  constructor() {
    this.localeService.activateLocaleFromUrl(this.router.url);
    this.applyFallbackSeo(this.router.url);

    this.router.events.pipe(filter((event) => event instanceof NavigationEnd)).subscribe((event) => {
      this.localeService.activateLocaleFromUrl(event.urlAfterRedirects);
      this.applyFallbackSeo(event.urlAfterRedirects);
      this.isMenuOpen.set(false);
    });
  }

  protected localizedUrl(pageKey: string): string {
    return this.navigation.localizedUrl(pageKey, this.currentLocale());
  }

  protected languageSwitchUrl(locale: 'en' | 'ar'): string {
    return this.navigation.switchLocaleUrl(this.router.url, locale);
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

  protected onRouteActivate(): void {
    this.routeActive.set(true);
  }

  private applyFallbackSeo(url: string): void {
    const page = this.englishFallbackForUrl(url);

    this.seo.apply({
      description: page.description,
      locale: 'en',
      noindex: true,
      path: page.path,
      title: `${page.title} | ${this.copy().brand}`,
    });
  }

  private englishFallbackForUrl(url: string): { description: string; path: string; title: string } {
    const path = url.split('?')[0].split('#')[0] || '/en';
    const normalizedPath = path === '/' ? '/en' : path;
    const segments = normalizedPath.split('/').filter(Boolean);
    const section = segments[1] ?? 'home';
    const isDetail = segments.length > 2;
    const titles: Record<string, string> = {
      about: 'About',
      blog: isDetail ? 'Blog detail' : 'Blog',
      contact: 'Contact',
      home: 'Home',
      privacy: 'Privacy',
      projects: isDetail ? 'Project detail' : 'Projects',
      services: 'Services',
    };

    if (segments[0] !== 'en' || !(section in titles)) {
      return {
        description: this.copy().notFoundDescription,
        path: normalizedPath.startsWith('/en') ? normalizedPath : '/en/not-found',
        title: this.copy().notFoundTitle,
      };
    }

    return {
      description: 'This server-rendered route placeholder is intentionally noindexed until full page content is implemented.',
      path: normalizedPath,
      title: titles[section],
    };
  }
}
