import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { PublicInteractionApiService } from './public-interaction-api.service';

describe('PublicInteractionApiService', () => {
  let http: HttpTestingController;
  let service: PublicInteractionApiService;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });

    http = TestBed.inject(HttpTestingController);
    service = TestBed.inject(PublicInteractionApiService);
  });

  afterEach(() => {
    http.verify();
  });

  it('uses credentialed project interaction endpoints with localized slugs', () => {
    service.projectLikeState('ar', 'client work').subscribe((response) => {
      expect(response.data.liked).toBe(true);
      expect(response.data.count).toBe(3);
    });

    const state = http.expectOne((request) => request.urlWithParams.includes('/api/v1/projects/client%20work/likes?locale=ar'));
    expect(state.request.method).toBe('GET');
    expect(state.request.withCredentials).toBe(true);
    state.flush({ data: { count: 3, liked: true } });

    service.recordProjectView('en', 'client-work').subscribe((response) => {
      expect(response.data.created).toBe(true);
    });

    const view = http.expectOne((request) => request.url.endsWith('/api/v1/projects/client-work/views'));
    expect(view.request.method).toBe('POST');
    expect(view.request.withCredentials).toBe(true);
    expect(view.request.body).toEqual({ locale: 'en' });
    view.flush({ data: { count: 1, created: true } });
  });

  it('posts form payloads exactly to the public write contract', () => {
    service.submitContact({
      email: 'client@example.test',
      locale: 'en',
      message: 'I would like to discuss a project.',
      name: 'Client Person',
      privacy_consent: true,
    }).subscribe((response) => {
      expect(response.data.message).toContain('received');
    });

    const contact = http.expectOne((request) => request.url.endsWith('/api/v1/contact'));
    expect(contact.request.method).toBe('POST');
    expect(contact.request.withCredentials).toBe(true);
    expect(contact.request.body).toEqual({
      email: 'client@example.test',
      locale: 'en',
      message: 'I would like to discuss a project.',
      name: 'Client Person',
      privacy_consent: true,
    });
    contact.flush({ data: { message: 'Submission received successfully.' } });
  });
});
