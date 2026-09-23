import { HttpClient, HttpParams } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { AppLocale } from '../i18n/locale.service';
import { publicSiteConfig } from '../config/public-site.config';
import {
  AboutPayload,
  ApiCollection,
  ApiItem,
  BlogPostDetail,
  BlogPostSummary,
  ProjectDetail,
  ProjectSummary,
  Service,
  SitePayload,
  Testimonial,
} from './public-api.models';

export interface ListParams {
  readonly page?: number;
  readonly perPage?: number;
  readonly featured?: boolean;
  readonly sort?: string;
}

@Injectable({ providedIn: 'root' })
export class PublicApiService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = publicSiteConfig.apiBaseUrl.replace(/\/$/, '');

  site(locale: AppLocale): Observable<ApiItem<SitePayload>> {
    return this.get('site', locale);
  }

  about(locale: AppLocale): Observable<ApiItem<AboutPayload>> {
    return this.get('about', locale);
  }

  projects(locale: AppLocale, params: ListParams = {}): Observable<ApiCollection<ProjectSummary>> {
    return this.get('projects', locale, params);
  }

  project(locale: AppLocale, slug: string): Observable<ApiItem<ProjectDetail>> {
    return this.get(`projects/${encodeURIComponent(slug)}`, locale);
  }

  services(locale: AppLocale, params: ListParams = {}): Observable<ApiCollection<Service>> {
    return this.get('services', locale, params);
  }

  testimonials(locale: AppLocale, params: ListParams = {}): Observable<ApiCollection<Testimonial>> {
    return this.get('testimonials', locale, params);
  }

  posts(locale: AppLocale, params: ListParams = {}): Observable<ApiCollection<BlogPostSummary>> {
    return this.get('posts', locale, params);
  }

  post(locale: AppLocale, slug: string): Observable<ApiItem<BlogPostDetail>> {
    return this.get(`posts/${encodeURIComponent(slug)}`, locale);
  }

  private get<T>(path: string, locale: AppLocale, params: ListParams = {}): Observable<T> {
    let httpParams = new HttpParams().set('locale', locale);

    if (params.page) {
      httpParams = httpParams.set('page', params.page);
    }

    if (params.perPage) {
      httpParams = httpParams.set('per_page', params.perPage);
    }

    if (typeof params.featured === 'boolean') {
      httpParams = httpParams.set('featured', params.featured ? '1' : '0');
    }

    if (params.sort) {
      httpParams = httpParams.set('sort', params.sort);
    }

    return this.http.get<T>(`${this.baseUrl}/${path}`, { params: httpParams });
  }
}
