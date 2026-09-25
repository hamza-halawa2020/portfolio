import { inject, Injectable } from '@angular/core';
import { ActivatedRouteSnapshot } from '@angular/router';
import { catchError, forkJoin, map, Observable, of, startWith } from 'rxjs';
import { PublicApiService } from '../../core/api/public-api.service';
import {
  AboutPayload,
  ApiCollection,
  BlogPostDetail,
  BlogPostSummary,
  ProjectDetail,
  ProjectSummary,
  Service,
  SitePayload,
  Testimonial,
} from '../../core/api/public-api.models';
import { AppLocale } from '../../core/i18n/locale.service';

export type PublicPageKey =
  | 'home'
  | 'projects'
  | 'projectDetail'
  | 'about'
  | 'services'
  | 'blog'
  | 'blogDetail'
  | 'contact'
  | 'privacy'
  | 'notFound';

export type PageStatus = 'loading' | 'success' | 'empty' | 'error';

export interface PageState {
  readonly status: PageStatus;
  readonly pageKey: PublicPageKey;
  readonly locale: AppLocale;
  readonly path: string;
  readonly title: string;
  readonly description: string;
  readonly payload?: PagePayload;
}

export type PagePayload =
  | HomePayload
  | ProjectsPayload
  | AboutPayload
  | ServicesPayload
  | BlogPayload
  | ProjectDetailPayload
  | BlogDetailPayload
  | ContactPayload
  | StaticPayload;

export interface HomePayload {
  readonly site: SitePayload;
  readonly projects: readonly ProjectSummary[];
  readonly services: readonly Service[];
  readonly about: AboutPayload;
  readonly testimonials: readonly Testimonial[];
  readonly posts: readonly BlogPostSummary[];
}

export interface ProjectsPayload {
  readonly projects: ApiCollection<ProjectSummary>;
}

export interface ServicesPayload {
  readonly services: ApiCollection<Service>;
}

export interface BlogPayload {
  readonly posts: ApiCollection<BlogPostSummary>;
}

export interface ProjectDetailPayload {
  readonly project: ProjectDetail;
}

export interface BlogDetailPayload {
  readonly post: BlogPostDetail;
}

export interface ContactPayload extends StaticPayload {
  readonly site: SitePayload;
}

export interface StaticPayload {
  readonly body: readonly string[];
}

interface PageCopy {
  readonly title: string;
  readonly description: string;
}

const COPY: Record<PublicPageKey, Record<AppLocale, PageCopy>> = {
  about: {
    ar: { description: 'نبذة مهنية وخبرة وتقنيات منشورة من واجهة المحتوى العامة.', title: 'نبذة' },
    en: {
      description: 'Professional profile, experience, and skills from the public content API.',
      title: 'About',
    },
  },
  blog: {
    ar: { description: 'مقالات منشورة من المدونة العامة.', title: 'المدونة' },
    en: { description: 'Published articles from the public blog.', title: 'Blog' },
  },
  blogDetail: {
    ar: { description: 'مقال منشور من المدونة العامة.', title: 'تفاصيل المقال' },
    en: { description: 'Published article from the public blog.', title: 'Blog detail' },
  },
  contact: {
    ar: { description: 'قنوات التواصل العامة المتاحة.', title: 'تواصل' },
    en: { description: 'Public contact options and social links.', title: 'Contact' },
  },
  home: {
    ar: { description: 'واجهة عامة لملف مطور لارافيل وأنجولار.', title: 'الرئيسية' },
    en: { description: 'Public portfolio for a Laravel and Angular developer.', title: 'Home' },
  },
  notFound: {
    ar: { description: 'الصفحة المطلوبة غير موجودة.', title: 'الصفحة غير موجودة' },
    en: { description: 'The requested page was not found.', title: 'Page not found' },
  },
  privacy: {
    ar: { description: 'ملخص خصوصية يوضح البيانات العامة والمؤجلة.', title: 'الخصوصية' },
    en: {
      description: 'Privacy summary for public portfolio data and deferred interactions.',
      title: 'Privacy',
    },
  },
  projectDetail: {
    ar: { description: 'تفاصيل عمل منشور من واجهة الأعمال العامة.', title: 'تفاصيل العمل' },
    en: {
      description: 'Published project detail from the public projects API.',
      title: 'Project detail',
    },
  },
  projects: {
    ar: { description: 'أعمال منشورة من واجهة المشاريع العامة.', title: 'الأعمال' },
    en: { description: 'Published work from the public projects API.', title: 'Projects' },
  },
  services: {
    ar: { description: 'خدمات منشورة من واجهة الخدمات العامة.', title: 'الخدمات' },
    en: { description: 'Published services from the public services API.', title: 'Services' },
  },
};

@Injectable({ providedIn: 'root' })
export class PublicPageFacade {
  private readonly api = inject(PublicApiService);

  load(snapshot: ActivatedRouteSnapshot, locale: AppLocale, path: string): Observable<PageState> {
    const pageKey = (snapshot.data['pageKey'] as PublicPageKey | undefined) ?? 'notFound';
    const slug = snapshot.paramMap.get('slug') ?? '';
    const copy = COPY[pageKey][locale];

    if (pageKey === 'notFound') {
      return of(this.success(pageKey, locale, path, copy, { body: [] }));
    }

    return this.request(pageKey, locale, slug).pipe(
      map((payload) => this.toState(pageKey, locale, path, copy, payload)),
      catchError(() => of({ ...this.base(pageKey, locale, path, copy), status: 'error' as const })),
      startWith({ ...this.base(pageKey, locale, path, copy), status: 'loading' as const }),
    );
  }

  private request(
    pageKey: PublicPageKey,
    locale: AppLocale,
    slug: string,
  ): Observable<PagePayload> {
    switch (pageKey) {
      case 'home':
        return forkJoin({
          about: this.api.about(locale).pipe(map((response) => response.data)),
          posts: this.api
            .posts(locale, { perPage: 3, sort: 'latest' })
            .pipe(map((response) => response.data)),
          projects: this.api
            .projects(locale, { featured: true, perPage: 3 })
            .pipe(map((response) => response.data)),
          services: this.api
            .services(locale, { perPage: 4 })
            .pipe(map((response) => response.data)),
          site: this.api.site(locale).pipe(map((response) => response.data)),
          testimonials: this.api
            .testimonials(locale, { featured: true, perPage: 3 })
            .pipe(map((response) => response.data)),
        });
      case 'projects':
        return this.api.projects(locale, { perPage: 12 }).pipe(map((projects) => ({ projects })));
      case 'projectDetail':
        return this.api.project(locale, slug).pipe(map((response) => ({ project: response.data })));
      case 'about':
        return this.api.about(locale).pipe(map((response) => response.data));
      case 'services':
        return this.api.services(locale, { perPage: 12 }).pipe(map((services) => ({ services })));
      case 'blog':
        return this.api
          .posts(locale, { perPage: 12, sort: 'latest' })
          .pipe(map((posts) => ({ posts })));
      case 'blogDetail':
        return this.api.post(locale, slug).pipe(map((response) => ({ post: response.data })));
      case 'contact':
        return this.api.site(locale).pipe(
          map((response) => ({
            body: this.contactBody(locale, response.data),
            site: response.data,
          })),
        );
      case 'privacy':
        return of({ body: this.privacyBody(locale) });
      default:
        return of({ body: [] });
    }
  }

  private toState(
    pageKey: PublicPageKey,
    locale: AppLocale,
    path: string,
    copy: PageCopy,
    payload: PagePayload,
  ): PageState {
    const title = this.payloadTitle(pageKey, payload) ?? copy.title;
    const description = this.payloadDescription(pageKey, payload) ?? copy.description;
    const state = this.success(pageKey, locale, path, { description, title }, payload);

    return this.isEmpty(pageKey, payload) ? { ...state, status: 'empty' } : state;
  }

  private success(
    pageKey: PublicPageKey,
    locale: AppLocale,
    path: string,
    copy: PageCopy,
    payload: PagePayload,
  ): PageState {
    return {
      ...this.base(pageKey, locale, path, copy),
      payload,
      status: 'success',
    };
  }

  private base(
    pageKey: PublicPageKey,
    locale: AppLocale,
    path: string,
    copy: PageCopy,
  ): Omit<PageState, 'status'> {
    return {
      description: copy.description,
      locale,
      pageKey,
      path,
      title: copy.title,
    };
  }

  private isEmpty(pageKey: PublicPageKey, payload: PagePayload): boolean {
    if (pageKey === 'projects') {
      return ((payload as ProjectsPayload).projects.data.length ?? 0) === 0;
    }

    if (pageKey === 'services') {
      return ((payload as ServicesPayload).services.data.length ?? 0) === 0;
    }

    if (pageKey === 'blog') {
      return ((payload as BlogPayload).posts.data.length ?? 0) === 0;
    }

    return false;
  }

  private payloadTitle(pageKey: PublicPageKey, payload: PagePayload): string | null {
    if (pageKey === 'projectDetail') {
      return (payload as ProjectDetailPayload).project.title;
    }

    if (pageKey === 'blogDetail') {
      return (payload as BlogDetailPayload).post.title;
    }

    return null;
  }

  private payloadDescription(pageKey: PublicPageKey, payload: PagePayload): string | null {
    if (pageKey === 'projectDetail') {
      return (payload as ProjectDetailPayload).project.summary;
    }

    if (pageKey === 'blogDetail') {
      return (payload as BlogDetailPayload).post.excerpt;
    }

    return null;
  }

  private contactBody(locale: AppLocale, site: SitePayload): readonly string[] {
    const publicEmail = this.settingValue(site, 'site.public_email', locale);
    const whatsappUrl = this.settingValue(site, 'site.whatsapp_url', locale);

    return locale === 'ar'
      ? [
          'تتوفر قنوات التواصل العامة المنشورة من إعدادات الموقع.',
          publicEmail ? `البريد العام: ${publicEmail}` : 'لم يتم نشر بريد عام بعد.',
          whatsappUrl ? 'رابط واتساب متاح من إعدادات الموقع.' : 'لم يتم نشر رابط واتساب بعد.',
        ]
      : [
          'Only public contact channels returned by site settings are shown here.',
          publicEmail ? `Public email: ${publicEmail}` : 'No public email has been published yet.',
          whatsappUrl
            ? 'A WhatsApp contact link is available from site settings.'
            : 'No WhatsApp link has been published yet.',
        ];
  }

  private privacyBody(locale: AppLocale): readonly string[] {
    return locale === 'ar'
      ? [
          'تعرض هذه الصفحة ملخصا عاما للخصوصية إلى أن تتم صياغة سياسة قانونية نهائية.',
          'تستخدم التفاعلات العامة معرفات مجهولة ومجزأة في الخادم ولا تخزن عناوين IP الخام في جداول التفاعل.',
          'نماذج التواصل والشهادات والمشاهدات والإعجابات تستخدم التحقق ومحددات المعدل وملف تعريف زائر مشفر.',
        ]
      : [
          'This page provides a plain privacy summary until final legal copy is managed.',
          'Public interactions use server-side anonymous hashed identifiers and do not store raw IP addresses in interaction tables.',
          'Contact forms, testimonial submissions, project views, and likes use validation, rate limiting, and an encrypted visitor cookie.',
        ];
  }

  private settingValue(site: SitePayload, key: string, locale: AppLocale): string | null {
    const value = site.settings[key];

    if (value === null || typeof value === 'undefined') {
      return null;
    }

    if (typeof value === 'object') {
      const localized = value[locale] ?? value.en;

      return localized === null || typeof localized === 'undefined' ? null : String(localized);
    }

    return String(value);
  }
}
