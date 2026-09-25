import { TestBed } from '@angular/core/testing';
import { ActivatedRouteSnapshot, convertToParamMap } from '@angular/router';
import { firstValueFrom, of, throwError, toArray } from 'rxjs';
import { PublicApiService } from '../../core/api/public-api.service';
import { PublicPageFacade, PublicPageKey } from './public-page.facade';

describe('PublicPageFacade', () => {
  let lastProjectsCall: unknown;
  let failServices = false;
  let emptyServices = false;

  beforeEach(() => {
    lastProjectsCall = null;
    failServices = false;
    emptyServices = false;

    TestBed.configureTestingModule({
      providers: [{ provide: PublicApiService, useClass: FakePublicApiService }],
    });
  });

  it('returns loading before API-backed projects success state', async () => {
    const states = await firstValueFrom(
      TestBed.inject(PublicPageFacade)
        .load(snapshot('projects'), 'en', '/en/projects')
        .pipe(toArray()),
    );

    expect(states.map((state) => state.status)).toEqual(['loading', 'success']);
    expect(states[1].title).toBe('Projects');
    expect(lastProjectsCall).toEqual(['en', { perPage: 12 }]);
  });

  it('maps empty list responses to explicit empty state', async () => {
    emptyServices = true;
    const states = await firstValueFrom(
      TestBed.inject(PublicPageFacade)
        .load(snapshot('services'), 'ar', '/ar/services')
        .pipe(toArray()),
    );

    expect(states.map((state) => state.status)).toEqual(['loading', 'empty']);
    expect(states[1].title).toBe('الخدمات');
  });

  it('maps API failures to explicit error state without fake content fallback', async () => {
    failServices = true;
    const states = await firstValueFrom(
      TestBed.inject(PublicPageFacade)
        .load(snapshot('services'), 'en', '/en/services')
        .pipe(toArray()),
    );

    expect(states.map((state) => state.status)).toEqual(['loading', 'error']);
    expect(states[1].payload).toBeUndefined();
  });

  class FakePublicApiService {
    projects(locale: string, params: unknown) {
      lastProjectsCall = [locale, params];

      return of({
        data: [
          {
            category: null,
            cover_image_url: null,
            is_featured: false,
            like_count: 0,
            published_at: null,
            slug: 'published-work',
            summary: 'Public summary',
            technologies: [],
            title: 'Published work',
            view_count: 0,
          },
        ],
      });
    }

    services() {
      if (failServices) {
        return throwError(() => new Error('API unavailable'));
      }

      return of({
        data: emptyServices
          ? []
          : [
              {
                description: 'Service',
                icon: null,
                slug: 'service',
                sort_order: 1,
                title: 'Service',
              },
            ],
      });
    }
  }
});

function snapshot(pageKey: PublicPageKey, slug = ''): ActivatedRouteSnapshot {
  const route = new ActivatedRouteSnapshot();
  route.data = { pageKey };
  route.params = slug ? { slug } : {};
  route.queryParams = {};
  route.fragment = null;
  route.url = [];
  Object.defineProperty(route, 'paramMap', {
    configurable: true,
    value: convertToParamMap(route.params),
  });

  return route;
}
