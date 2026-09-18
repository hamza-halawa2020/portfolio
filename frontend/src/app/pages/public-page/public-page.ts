import { Component, computed, inject, OnInit } from '@angular/core';
import { RouterLink, ActivatedRoute } from '@angular/router';
import { LocaleService } from '../../core/i18n/locale.service';
import { SeoService } from '../../core/seo/seo.service';

type PublicPageKey =
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

interface PageCopy {
  readonly title: string;
  readonly description: string;
}

const PAGE_COPY: Record<PublicPageKey, Record<'en' | 'ar', PageCopy>> = {
  about: {
    ar: { description: 'مسار نبذة عن المطور والسيرة المهنية جاهز للعرض عبر SSR.', title: 'نبذة' },
    en: { description: 'The developer biography and resume route is ready for SSR rendering.', title: 'About' },
  },
  blog: {
    ar: { description: 'مسار قائمة المقالات جاهز للربط بواجهة المقالات لاحقا.', title: 'المدونة' },
    en: { description: 'The blog listing route is ready for later API integration.', title: 'Blog' },
  },
  blogDetail: {
    ar: { description: 'مسار تفاصيل المقالات الديناميكي يبقى معروضا عبر SSR ولا يختفي من البناء.', title: 'تفاصيل المقال' },
    en: { description: 'The dynamic blog detail route stays server-rendered and is not dropped from production output.', title: 'Blog detail' },
  },
  contact: {
    ar: { description: 'مسار التواصل جاهز لنموذج الرسائل وتكامل واتساب في المهام اللاحقة.', title: 'تواصل' },
    en: { description: 'The contact route is ready for the later message form and WhatsApp integration.', title: 'Contact' },
  },
  home: {
    ar: { description: 'أساس الواجهة العامة ثنائي اللغة جاهز لاستقبال محتوى الصفحة الرئيسية.', title: 'الرئيسية' },
    en: { description: 'The bilingual public frontend foundation is ready for homepage content.', title: 'Home' },
  },
  notFound: {
    ar: { description: 'الصفحة المطلوبة غير موجودة.', title: 'الصفحة غير موجودة' },
    en: { description: 'The requested page was not found.', title: 'Page not found' },
  },
  privacy: {
    ar: { description: 'مسار سياسة الخصوصية جاهز لشرح التحليلات والمعرفات المجهولة لاحقا.', title: 'الخصوصية' },
    en: { description: 'The privacy route is ready for the later analytics and visitor identifier policy.', title: 'Privacy' },
  },
  projectDetail: {
    ar: { description: 'مسار تفاصيل الأعمال الديناميكي يبقى معروضا عبر SSR ولا يضيف روابط أو بيانات تجريبية.', title: 'تفاصيل العمل' },
    en: { description: 'The dynamic project detail route stays server-rendered and does not add demo links or credentials.', title: 'Project detail' },
  },
  projects: {
    ar: { description: 'مسار قائمة الأعمال جاهز للمرشحات والبيانات العامة في المهام اللاحقة.', title: 'الأعمال' },
    en: { description: 'The projects listing route is ready for later filters and public API data.', title: 'Projects' },
  },
  services: {
    ar: { description: 'مسار الخدمات جاهز للمحتوى المدار من لوحة التحكم.', title: 'الخدمات' },
    en: { description: 'The services route is ready for dashboard-managed content.', title: 'Services' },
  },
};

@Component({
  imports: [RouterLink],
  selector: 'app-public-page',
  styleUrl: './public-page.css',
  templateUrl: './public-page.html',
})
export class PublicPage implements OnInit {
  private readonly localeService = inject(LocaleService);
  private readonly route = inject(ActivatedRoute);
  private readonly seo = inject(SeoService);

  protected readonly copy = this.localeService.copy;
  protected readonly locale = this.localeService.locale;
  protected readonly page = computed(() => PAGE_COPY[this.pageKey()][this.locale()]);
  protected readonly isNotFound = computed(() => this.pageKey() === 'notFound');

  ngOnInit(): void {
    const path = this.route.snapshot.pathFromRoot.flatMap((snapshot) => snapshot.url.map((segment) => segment.path)).join('/');
    const locale = this.localeService.activateLocaleFromUrl(`/${path}`);
    const page = PAGE_COPY[this.pageKey()][locale];
    const canonicalPath = path ? `/${path}` : `/${locale}`;

    this.seo.apply({
      alternates: this.alternatesForCurrentRoute(canonicalPath),
      description: page.description,
      locale,
      noindex: true,
      path: canonicalPath,
      title: `${page.title} | ${this.copy().brand}`,
    });
  }

  private pageKey(): PublicPageKey {
    return (this.route.snapshot.data['pageKey'] as PublicPageKey | undefined) ?? 'notFound';
  }

  private alternatesForCurrentRoute(path: string): readonly { locale: 'en' | 'ar'; path: string }[] {
    if (this.pageKey() === 'projectDetail' || this.pageKey() === 'blogDetail' || this.pageKey() === 'notFound') {
      return [];
    }

    const alternatePath = path.startsWith('/ar') ? path.replace('/ar', '/en') : path.replace('/en', '/ar');

    return [
      { locale: this.locale(), path },
      { locale: this.locale() === 'ar' ? 'en' : 'ar', path: alternatePath },
    ];
  }
}
