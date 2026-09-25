import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting, HttpTestingController } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { PublicApiService } from './public-api.service';

describe('PublicApiService', () => {
  let http: HttpTestingController;
  let service: PublicApiService;

  beforeEach(() => {
    TestBed.resetTestingModule();
    TestBed.configureTestingModule({
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });

    http = TestBed.inject(HttpTestingController);
    service = TestBed.inject(PublicApiService);
  });

  afterEach(() => {
    http.verify();
  });

  it('maps localized project list query parameters to the Laravel API contract', () => {
    service
      .projects('ar', { featured: true, page: 2, perPage: 6, sort: 'featured' })
      .subscribe((response) => {
        expect(response.data[0].slug).toBe('work-ar');
        expect(response.data[0].title).toBe('عمل منشور');
        expect(response.data[0].technologies[0].slug).toBe('angular');
      });

    const request = http.expectOne((candidate) =>
      candidate.urlWithParams.includes('/api/v1/projects?'),
    );

    expect(request.request.method).toBe('GET');
    expect(request.request.params.get('locale')).toBe('ar');
    expect(request.request.params.get('featured')).toBe('1');
    expect(request.request.params.get('page')).toBe('2');
    expect(request.request.params.get('per_page')).toBe('6');
    expect(request.request.params.get('sort')).toBe('featured');

    request.flush({
      data: [
        {
          category: { name: 'ويب', slug: 'web' },
          cover_image_url: 'https://example.com/work.webp',
          is_featured: true,
          like_count: 0,
          published_at: '2026-09-23T00:00:00.000000Z',
          slug: 'work-ar',
          summary: 'ملخص عام',
          technologies: [{ name: 'Angular', slug: 'angular' }],
          title: 'عمل منشور',
          view_count: 0,
        },
      ],
    });
  });

  it('encodes dynamic detail slugs without exposing write endpoint state', () => {
    service.project('en', 'client work').subscribe((response) => {
      expect(response.data.title).toBe('Client work');
    });

    const request = http.expectOne((candidate) =>
      candidate.urlWithParams.includes('/api/v1/projects/client%20work?locale=en'),
    );

    expect(request.request.method).toBe('GET');
    request.flush({ data: { slug: 'client-work', title: 'Client work' } });
  });
});
