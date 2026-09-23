import { AsyncPipe, DatePipe } from '@angular/common';
import { Component, DestroyRef, inject, OnInit } from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { ActivatedRouteSnapshot, NavigationEnd, Router, RouterLink } from '@angular/router';
import { filter, Observable, tap } from 'rxjs';
import { AboutPayload, BlogPostSummary, ProjectSummary, Service, Skill } from '../../core/api/public-api.models';
import { AppLocale, LocaleService } from '../../core/i18n/locale.service';
import { SeoService } from '../../core/seo/seo.service';
import {
  BlogDetailPayload,
  BlogPayload,
  HomePayload,
  PageState,
  ProjectDetailPayload,
  ProjectsPayload,
  PublicPageFacade,
  ServicesPayload,
  StaticPayload,
} from './public-page.facade';

@Component({
  imports: [AsyncPipe, DatePipe, RouterLink],
  selector: 'app-public-page',
  styleUrl: './public-page.css',
  templateUrl: './public-page.html',
})
export class PublicPage implements OnInit {
  private readonly facade = inject(PublicPageFacade);
  private readonly destroyRef = inject(DestroyRef);
  private readonly localeService = inject(LocaleService);
  private readonly router = inject(Router);
  private readonly seo = inject(SeoService);

  protected readonly copy = this.localeService.copy;
  protected readonly locale = this.localeService.locale;
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
    const path = snapshot.pathFromRoot.flatMap((routeSnapshot) => routeSnapshot.url.map((segment) => segment.path)).join('/');
    const locale = this.localeService.activateLocaleFromUrl(`/${path}`);
    const canonicalPath = this.canonicalPath(path, locale);

    this.pageState$ = this.facade.load(snapshot, locale, canonicalPath).pipe(tap((state) => this.applySeo(state)));
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

    this.seo.apply({
      alternates: this.alternatesFor(state),
      description: state.description,
      locale: state.locale,
      noindex: state.status === 'error' || state.status === 'empty' || state.pageKey === 'notFound',
      path: state.path,
      title: `${state.title} | ${this.localeService.translate('brand', state.locale)}`,
    });
  }

  private alternatesFor(state: PageState): readonly { locale: AppLocale; path: string }[] {
    if (state.pageKey === 'projectDetail' || state.pageKey === 'blogDetail' || state.pageKey === 'notFound') {
      return [];
    }

    return [
      { locale: state.locale, path: state.path },
      { locale: state.locale === 'ar' ? 'en' : 'ar', path: state.path.startsWith('/ar') ? state.path.replace('/ar', '/en') : state.path.replace('/en', '/ar') },
    ];
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
}
