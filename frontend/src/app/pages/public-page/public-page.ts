import { AsyncPipe, DatePipe } from '@angular/common';
import { Component, DestroyRef, inject, OnInit } from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { ActivatedRouteSnapshot, NavigationEnd, Router, RouterLink } from '@angular/router';
import { filter, Observable, tap } from 'rxjs';
import {
  AboutPayload,
  BlogPostSummary,
  ProjectSummary,
  Service,
  Skill,
} from '../../core/api/public-api.models';
import {
  ContactSubmissionPayload,
  TestimonialSubmissionPayload,
} from '../../core/interactions/public-interaction.models';
import { PublicInteractionService } from '../../core/interactions/public-interaction.service';
import { AppLocale, LocaleService } from '../../core/i18n/locale.service';
import { SeoService } from '../../core/seo/seo.service';
import { ShellNavigationService } from '../../core/layout/shell-navigation.service';
import { ContactFormComponent } from './components/contact-form.component';
import { ProjectInteractionsComponent } from './components/project-interactions.component';
import { TestimonialFormComponent } from './components/testimonial-form.component';
import {
  BlogDetailPayload,
  BlogPayload,
  ContactPayload,
  HomePayload,
  PageState,
  ProjectDetailPayload,
  ProjectsPayload,
  PublicPageFacade,
  ServicesPayload,
  StaticPayload,
} from './public-page.facade';

@Component({
  imports: [
    AsyncPipe,
    ContactFormComponent,
    DatePipe,
    ProjectInteractionsComponent,
    RouterLink,
    TestimonialFormComponent,
  ],
  selector: 'app-public-page',
  styleUrl: './public-page.css',
  templateUrl: './public-page.html',
})
export class PublicPage implements OnInit {
  private readonly facade = inject(PublicPageFacade);
  private readonly destroyRef = inject(DestroyRef);
  private readonly interactions = inject(PublicInteractionService);
  private readonly localeService = inject(LocaleService);
  private readonly navigation = inject(ShellNavigationService);
  private readonly router = inject(Router);
  private readonly seo = inject(SeoService);

  protected readonly copy = this.localeService.copy;
  protected readonly contactSubmission = this.interactions.contactSubmission;
  protected readonly locale = this.localeService.locale;
  protected readonly projectLikeState = this.interactions.projectLikeState;
  protected readonly projectViewCounts = this.interactions.projectViewCounts;
  protected readonly testimonialSubmission = this.interactions.testimonialSubmission;
  protected pageState$!: Observable<PageState>;

  ngOnInit(): void {
    this.loadCurrentRoute();

    this.router.events
      .pipe(
        filter((event) => event instanceof NavigationEnd),
        takeUntilDestroyed(this.destroyRef),
      )
      .subscribe(() => {
        this.loadCurrentRoute();
      });
  }

  private loadCurrentRoute(): void {
    const snapshot = this.activeSnapshot();
    const path = snapshot.pathFromRoot
      .flatMap((routeSnapshot) => routeSnapshot.url.map((segment) => segment.path))
      .join('/');
    const locale = this.localeService.activateLocaleFromUrl(`/${path}`);
    const canonicalPath = this.canonicalPath(path, locale);

    this.pageState$ = this.facade
      .load(snapshot, locale, canonicalPath)
      .pipe(tap((state) => this.applySeo(state)));
  }

  private activeSnapshot(): ActivatedRouteSnapshot {
    let snapshot = this.router.routerState.snapshot.root;

    while (snapshot.firstChild) {
      snapshot = snapshot.firstChild;
    }

    return snapshot;
  }

  protected asHome(state: PageState): HomePayload {
    return state.payload as HomePayload;
  }

  protected asProjects(state: PageState): ProjectsPayload {
    return state.payload as ProjectsPayload;
  }

  protected asProjectDetail(state: PageState): ProjectDetailPayload {
    return state.payload as ProjectDetailPayload;
  }

  protected asAbout(state: PageState): AboutPayload {
    return state.payload as AboutPayload;
  }

  protected asServices(state: PageState): ServicesPayload {
    return state.payload as ServicesPayload;
  }

  protected asBlog(state: PageState): BlogPayload {
    return state.payload as BlogPayload;
  }

  protected asBlogDetail(state: PageState): BlogDetailPayload {
    return state.payload as BlogDetailPayload;
  }

  protected asContact(state: PageState): ContactPayload {
    return state.payload as ContactPayload;
  }

  protected asStatic(state: PageState): StaticPayload {
    return state.payload as StaticPayload;
  }

  protected projectUrl(project: ProjectSummary): string {
    return `/${this.locale()}/projects/${project.slug}`;
  }

  protected postUrl(post: BlogPostSummary): string {
    return `/${this.locale()}/blog/${post.slug}`;
  }

  protected contactUrl(): string {
    return `/${this.locale()}/contact`;
  }

  protected projectsUrl(): string {
    return `/${this.locale()}/projects`;
  }

  protected servicesUrl(): string {
    return `/${this.locale()}/services`;
  }

  protected blogUrl(): string {
    return `/${this.locale()}/blog`;
  }

  protected whatsappUrl(payload: ContactPayload): string | null {
    const url = this.localizedSetting(payload, 'site.whatsapp_url');

    if (!url) {
      return null;
    }

    const message = this.localizedSetting(payload, 'site.whatsapp_message');

    if (!message) {
      return url;
    }

    try {
      const parsed = new URL(url);
      parsed.searchParams.set('text', message);

      return parsed.toString();
    } catch {
      return url;
    }
  }

  protected submitContact(payload: ContactSubmissionPayload): void {
    this.interactions.submitContact(payload);
  }

  protected submitTestimonial(payload: TestimonialSubmissionPayload): void {
    this.interactions.submitTestimonial(payload);
  }

  protected toggleProjectLike(project: ProjectDetailPayload['project']): void {
    this.interactions.toggleProjectLike(this.locale(), project);
  }

  protected hasImage(url: string | null): url is string {
    return Boolean(url);
  }

  protected trackProject(_index: number, project: ProjectSummary): string {
    return project.slug;
  }

  protected trackPost(_index: number, post: BlogPostSummary): string {
    return post.slug;
  }

  protected trackService(_index: number, service: Service): string {
    return service.slug;
  }

  protected trackSkill(_index: number, skill: Skill): string {
    return `${skill.group ?? 'skill'}-${skill.name}`;
  }

  private applySeo(state: PageState): void {
    if (state.status === 'loading') {
      return;
    }

    const detailSeo = this.detailSeo(state);
    this.navigation.setLocalizedSlugMapping(this.slugMapping(state));

    this.seo.apply({
      alternates: this.alternatesFor(state),
      canonicalUrl: detailSeo.canonicalUrl,
      description: detailSeo.description ?? state.description,
      imageUrl: detailSeo.imageUrl,
      jsonLd: this.jsonLdFor(
        state,
        detailSeo.title ?? state.title,
        detailSeo.description ?? state.description,
      ),
      locale: state.locale,
      noindex:
        state.status === 'error' ||
        state.status === 'empty' ||
        state.pageKey === 'notFound' ||
        detailSeo.noindex,
      path: state.path,
      title: `${detailSeo.title ?? state.title} | ${this.localeService.translate('brand', state.locale)}`,
      type: state.pageKey === 'blogDetail' ? 'article' : 'website',
    });

    this.applyInteractions(state);
  }

  private applyInteractions(state: PageState): void {
    if (state.status !== 'success' || state.pageKey !== 'projectDetail') {
      return;
    }

    const project = (state.payload as ProjectDetailPayload | undefined)?.project;

    if (!project) {
      return;
    }

    this.interactions.recordProjectView(state.locale, project);
    this.interactions.loadProjectLikeState(state.locale, project);
  }

  private alternatesFor(
    state: PageState,
  ): readonly { locale: AppLocale | 'x-default'; path: string }[] {
    if (state.pageKey === 'notFound') {
      return [];
    }

    if (state.pageKey === 'projectDetail') {
      const project = (state.payload as ProjectDetailPayload | undefined)?.project;

      return this.dynamicAlternates('projects', project?.localized_slugs);
    }

    if (state.pageKey === 'blogDetail') {
      const post = (state.payload as BlogDetailPayload | undefined)?.post;

      return this.dynamicAlternates('blog', post?.localized_slugs);
    }

    return [
      { locale: state.locale, path: state.path },
      {
        locale: state.locale === 'ar' ? 'en' : 'ar',
        path: state.path.startsWith('/ar')
          ? state.path.replace('/ar', '/en')
          : state.path.replace('/en', '/ar'),
      },
      {
        locale: 'x-default' as const,
        path: state.path.startsWith('/ar') ? state.path.replace('/ar', '/en') : state.path,
      },
    ];
  }

  private dynamicAlternates(
    section: 'blog' | 'projects',
    slugs: Partial<Record<AppLocale, string | null>> | undefined,
  ): readonly { locale: AppLocale | 'x-default'; path: string }[] {
    if (!slugs?.en || !slugs?.ar) {
      return [];
    }

    return [
      { locale: 'en', path: `/en/${section}/${slugs.en}` },
      { locale: 'ar', path: `/ar/${section}/${slugs.ar}` },
      { locale: 'x-default', path: `/en/${section}/${slugs.en}` },
    ];
  }

  private slugMapping(state: PageState) {
    if (state.pageKey === 'projectDetail') {
      const project = (state.payload as ProjectDetailPayload | undefined)?.project;

      return project?.localized_slugs
        ? { slugs: this.nonNullSlugs(project.localized_slugs), type: 'project' as const }
        : undefined;
    }

    if (state.pageKey === 'blogDetail') {
      const post = (state.payload as BlogDetailPayload | undefined)?.post;

      return post?.localized_slugs
        ? { slugs: this.nonNullSlugs(post.localized_slugs), type: 'blog' as const }
        : undefined;
    }

    return undefined;
  }

  private nonNullSlugs(
    slugs: Partial<Record<AppLocale, string | null>>,
  ): Partial<Record<AppLocale, string>> {
    return {
      ...(slugs.en ? { en: slugs.en } : {}),
      ...(slugs.ar ? { ar: slugs.ar } : {}),
    };
  }

  private detailSeo(state: PageState): {
    canonicalUrl?: string | null;
    description?: string | null;
    imageUrl?: string | null;
    noindex?: boolean;
    title?: string | null;
  } {
    if (state.pageKey === 'projectDetail') {
      const project = (state.payload as ProjectDetailPayload | undefined)?.project;

      return {
        canonicalUrl: project?.seo?.canonical_url,
        description: project?.seo?.description || project?.summary,
        imageUrl: project?.seo?.og_image_url || project?.cover_image_url,
        noindex: project?.seo?.robots?.index === false || project?.seo?.robots?.follow === false,
        title: project?.seo?.title || project?.title,
      };
    }

    if (state.pageKey === 'blogDetail') {
      const post = (state.payload as BlogDetailPayload | undefined)?.post;

      return {
        canonicalUrl: post?.seo?.canonical_url,
        description: post?.seo?.description || post?.excerpt,
        imageUrl: post?.seo?.og_image_url || post?.cover_image_url,
        noindex: post?.seo?.robots?.index === false || post?.seo?.robots?.follow === false,
        title: post?.seo?.title || post?.title,
      };
    }

    return {};
  }

  private jsonLdFor(state: PageState, title: string, description: string): readonly unknown[] {
    const url = this.seo.absoluteUrl(state.path);
    const base = [this.websiteSchema(state), this.breadcrumbSchema(state, title)];

    if (state.pageKey === 'home') {
      return [
        this.websiteSchema(state),
        {
          '@context': 'https://schema.org',
          '@type': 'Person',
          description,
          name: this.localeService.translate('brand', state.locale),
          url,
        },
      ];
    }

    if (state.pageKey === 'blogDetail') {
      const post = (state.payload as BlogDetailPayload | undefined)?.post;

      return [
        ...base,
        {
          '@context': 'https://schema.org',
          '@type': 'BlogPosting',
          datePublished: post?.published_at ?? undefined,
          description,
          headline: title,
          image: post?.cover_image_url ? this.seo.absoluteUrl(post.cover_image_url) : undefined,
          mainEntityOfPage: url,
        },
      ];
    }

    if (state.pageKey === 'projectDetail') {
      const project = (state.payload as ProjectDetailPayload | undefined)?.project;

      return [
        ...base,
        {
          '@context': 'https://schema.org',
          '@type': 'CreativeWork',
          datePublished: project?.published_at ?? undefined,
          description,
          headline: title,
          image: project?.cover_image_url
            ? this.seo.absoluteUrl(project.cover_image_url)
            : undefined,
          url,
        },
      ];
    }

    return state.pageKey === 'notFound' ? [] : base;
  }

  private websiteSchema(state: PageState): unknown {
    return {
      '@context': 'https://schema.org',
      '@type': 'WebSite',
      inLanguage: state.locale,
      name: this.localeService.translate('brand', state.locale),
      url: this.seo.absoluteUrl(`/${state.locale}`),
    };
  }

  private breadcrumbSchema(state: PageState, title: string): unknown {
    const parts = state.path.split('/').filter(Boolean);
    const locale = parts[0] ?? state.locale;
    const items = [
      {
        '@type': 'ListItem',
        item: this.seo.absoluteUrl(`/${locale}`),
        name: this.localeService.translate('brand', state.locale),
        position: 1,
      },
    ];

    if (parts.length > 1) {
      items.push({
        '@type': 'ListItem',
        item: this.seo.absoluteUrl(state.path),
        name: title,
        position: 2,
      });
    }

    return {
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: items,
    };
  }

  private canonicalPath(path: string, locale: AppLocale): string {
    if (!path) {
      return `/${locale}`;
    }

    if (path.startsWith('en') || path.startsWith('ar')) {
      return `/${path}`;
    }

    return `/en/${path}`;
  }

  private localizedSetting(payload: ContactPayload, key: string): string | null {
    const value = payload.site.settings[key];

    if (value === null || typeof value === 'undefined') {
      return null;
    }

    if (typeof value === 'object') {
      const localized = value[this.locale()] ?? value.en;

      return localized === null || typeof localized === 'undefined' ? null : String(localized);
    }

    return String(value);
  }
}
