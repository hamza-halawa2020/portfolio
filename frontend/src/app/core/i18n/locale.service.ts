import { DOCUMENT } from '@angular/common';
import { computed, effect, inject, Injectable, PLATFORM_ID, signal } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';

export type AppLocale = 'en' | 'ar';

export interface ShellCopy {
  readonly brand: string;
  readonly skipToContent: string;
  readonly menu: string;
  readonly closeMenu: string;
  readonly languageLabel: string;
  readonly themeLabel: string;
  readonly themes: Record<'light' | 'dark' | 'system', string>;
  readonly footerLead: string;
  readonly footerNote: string;
  readonly placeholderEyebrow: string;
  readonly placeholderNote: string;
  readonly notFoundEyebrow: string;
  readonly notFoundTitle: string;
  readonly notFoundDescription: string;
  readonly backHome: string;
  readonly navigation: Record<string, string>;
}

const COPY: Record<AppLocale, ShellCopy> = {
  en: {
    backHome: 'Back home',
    brand: 'Portfolio Platform',
    closeMenu: 'Close navigation',
    footerLead: 'Bilingual Laravel and Angular portfolio platform.',
    footerNote: 'Public content is managed from the private dashboard.',
    languageLabel: 'Language',
    menu: 'Open navigation',
    navigation: {
      about: 'About',
      blog: 'Blog',
      contact: 'Contact',
      home: 'Home',
      privacy: 'Privacy',
      projects: 'Projects',
      services: 'Services',
    },
    notFoundDescription: 'The page you requested was not found.',
    notFoundEyebrow: '404',
    notFoundTitle: 'Page not found',
    placeholderEyebrow: 'Frontend foundation',
    placeholderNote: 'This route is intentionally wired for FE-001. Full page content, API data, and interaction states remain in later tasks.',
    skipToContent: 'Skip to content',
    themeLabel: 'Theme',
    themes: {
      dark: 'Dark',
      light: 'Light',
      system: 'System',
    },
  },
  ar: {
    backHome: 'العودة للرئيسية',
    brand: 'منصة الملف الشخصي',
    closeMenu: 'إغلاق التنقل',
    footerLead: 'منصة ملف شخصي ثنائية اللغة مبنية بلارافيل وأنجولار.',
    footerNote: 'يدار المحتوى العام من لوحة تحكم خاصة.',
    languageLabel: 'اللغة',
    menu: 'فتح التنقل',
    navigation: {
      about: 'نبذة',
      blog: 'المدونة',
      contact: 'تواصل',
      home: 'الرئيسية',
      privacy: 'الخصوصية',
      projects: 'الأعمال',
      services: 'الخدمات',
    },
    notFoundDescription: 'الصفحة المطلوبة غير موجودة.',
    notFoundEyebrow: '404',
    notFoundTitle: 'الصفحة غير موجودة',
    placeholderEyebrow: 'أساس الواجهة',
    placeholderNote: 'هذا المسار مهيأ عمدا ضمن FE-001. محتوى الصفحات الكامل وبيانات الواجهة البرمجية وحالات التفاعل مؤجلة لمهام لاحقة.',
    skipToContent: 'تجاوز إلى المحتوى',
    themeLabel: 'المظهر',
    themes: {
      dark: 'داكن',
      light: 'فاتح',
      system: 'النظام',
    },
  },
};

@Injectable({ providedIn: 'root' })
export class LocaleService {
  private readonly document = inject(DOCUMENT);
  private readonly platformId = inject(PLATFORM_ID);
  private readonly storageKey = 'portfolio.locale';
  private readonly localeSignal = signal<AppLocale>('en');

  readonly locale = this.localeSignal.asReadonly();
  readonly direction = computed(() => (this.localeSignal() === 'ar' ? 'rtl' : 'ltr'));
  readonly copy = computed(() => COPY[this.localeSignal()]);

  constructor() {
    effect(() => {
      const locale = this.localeSignal();
      this.document.documentElement.lang = locale;
      this.document.documentElement.dir = locale === 'ar' ? 'rtl' : 'ltr';
    });
  }

  activateLocale(locale: AppLocale, persist = false): void {
    this.localeSignal.set(locale);
    this.applyDocumentLocale(locale);

    const storage = this.storage();

    if (persist && storage) {
      storage.setItem(this.storageKey, locale);
    }
  }

  activateLocaleFromUrl(url: string): AppLocale {
    const locale = this.resolveLocaleFromUrl(url);
    this.activateLocale(locale);

    return locale;
  }

  resolveLocaleFromUrl(url: string): AppLocale {
    return url.split('?')[0].split('/').filter(Boolean)[0] === 'ar' ? 'ar' : 'en';
  }

  persistedLocale(): AppLocale | null {
    const storage = this.storage();

    if (!storage) {
      return null;
    }

    const stored = storage.getItem(this.storageKey);

    return stored === 'ar' || stored === 'en' ? stored : null;
  }

  private applyDocumentLocale(locale: AppLocale): void {
    this.document.documentElement.lang = locale;
    this.document.documentElement.dir = locale === 'ar' ? 'rtl' : 'ltr';
  }

  private storage(): Storage | null {
    if (!isPlatformBrowser(this.platformId) || typeof globalThis.localStorage === 'undefined') {
      return null;
    }

    return globalThis.localStorage;
  }
}
