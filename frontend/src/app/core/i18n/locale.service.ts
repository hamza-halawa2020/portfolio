import { DOCUMENT, isPlatformBrowser } from '@angular/common';
import { computed, effect, inject, Injectable, PLATFORM_ID, signal } from '@angular/core';

export type AppLocale = 'en' | 'ar';
export type NavKey = 'home' | 'projects' | 'services' | 'blog' | 'about' | 'contact' | 'privacy';
export type ThemeCopyKey = 'light' | 'dark' | 'system';

export interface UiStateCopy {
  readonly loading: string;
  readonly empty: string;
  readonly error: string;
  readonly validationRequired: string;
}

export interface ShellCopy {
  readonly brand: string;
  readonly skipToContent: string;
  readonly menu: string;
  readonly closeMenu: string;
  readonly primaryNavigation: string;
  readonly footerNavigation: string;
  readonly languageLabel: string;
  readonly themeLabel: string;
  readonly themes: Record<ThemeCopyKey, string>;
  readonly footerLead: string;
  readonly footerNote: string;
  readonly placeholderEyebrow: string;
  readonly placeholderNote: string;
  readonly notFoundEyebrow: string;
  readonly notFoundTitle: string;
  readonly notFoundDescription: string;
  readonly backHome: string;
  readonly states: UiStateCopy;
  readonly navigation: Record<NavKey, string>;
}

const ENGLISH_COPY: ShellCopy = {
  backHome: 'Back home',
  brand: 'Portfolio Platform',
  closeMenu: 'Close navigation',
  footerLead: 'Bilingual Laravel and Angular portfolio platform.',
  footerNavigation: 'Footer navigation',
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
  placeholderNote:
    'This route is intentionally wired for the frontend foundation. Full page content, API data, and interaction states remain in later tasks.',
  primaryNavigation: 'Primary navigation',
  skipToContent: 'Skip to content',
  states: {
    empty: 'No public content is available yet.',
    error: 'Something went wrong. Please try again.',
    loading: 'Loading content.',
    validationRequired: 'This field is required.',
  },
  themeLabel: 'Theme',
  themes: {
    dark: 'Dark',
    light: 'Light',
    system: 'System',
  },
};

const ARABIC_COPY: ShellCopy = {
  backHome: 'العودة للرئيسية',
  brand: 'منصة الملف الشخصي',
  closeMenu: 'إغلاق التنقل',
  footerLead: 'منصة ملف شخصي ثنائية اللغة مبنية بلارافيل وأنجولار.',
  footerNavigation: 'تنقل التذييل',
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
  placeholderNote:
    'هذا المسار مهيأ ضمن أساس الواجهة. محتوى الصفحات الكامل وبيانات الواجهة البرمجية وحالات التفاعل مؤجلة لمهام لاحقة.',
  primaryNavigation: 'التنقل الرئيسي',
  skipToContent: 'تجاوز إلى المحتوى',
  states: {
    empty: 'لا يوجد محتوى عام متاح بعد.',
    error: 'حدث خطأ. يرجى المحاولة مرة أخرى.',
    loading: 'جار تحميل المحتوى.',
    validationRequired: 'هذا الحقل مطلوب.',
  },
  themeLabel: 'المظهر',
  themes: {
    dark: 'داكن',
    light: 'فاتح',
    system: 'النظام',
  },
};

const COPY: Record<AppLocale, ShellCopy> = {
  ar: ARABIC_COPY,
  en: ENGLISH_COPY,
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
      this.applyDocumentLocale(this.localeSignal());
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
    return this.isLocale(url.split('?')[0].split('/').filter(Boolean)[0])
      ? (url.split('?')[0].split('/').filter(Boolean)[0] as AppLocale)
      : 'en';
  }

  persistedLocale(): AppLocale | null {
    const storage = this.storage();

    if (!storage) {
      return null;
    }

    const stored = storage.getItem(this.storageKey);

    return this.isLocale(stored) ? stored : null;
  }

  translate<K extends keyof ShellCopy>(key: K, locale = this.localeSignal()): ShellCopy[K] {
    return COPY[locale][key] ?? COPY.en[key];
  }

  localizedValue(values: Partial<Record<AppLocale, string>>, locale = this.localeSignal()): string {
    return values[locale] ?? values.en ?? '';
  }

  private applyDocumentLocale(locale: AppLocale): void {
    this.document.documentElement.lang = locale;
    this.document.documentElement.dir = locale === 'ar' ? 'rtl' : 'ltr';
  }

  private isLocale(value: unknown): value is AppLocale {
    return value === 'ar' || value === 'en';
  }

  private storage(): Storage | null {
    if (!isPlatformBrowser(this.platformId) || typeof globalThis.localStorage === 'undefined') {
      return null;
    }

    return globalThis.localStorage;
  }
}
