import { HttpClient, HttpErrorResponse, HttpParams } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiItem } from '../api/public-api.models';
import { publicSiteConfig } from '../config/public-site.config';
import { AppLocale } from '../i18n/locale.service';
import {
  AcknowledgmentPayload,
  ContactSubmissionPayload,
  ProjectInteractionPayload,
  TestimonialSubmissionPayload,
} from './public-interaction.models';

@Injectable({ providedIn: 'root' })
export class PublicInteractionApiService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = publicSiteConfig.apiBaseUrl.replace(/\/$/, '');

  projectLikeState(locale: AppLocale, slug: string): Observable<ApiItem<ProjectInteractionPayload>> {
    return this.http.get<ApiItem<ProjectInteractionPayload>>(`${this.baseUrl}/projects/${encodeURIComponent(slug)}/likes`, {
      params: this.localeParams(locale),
      transferCache: false,
      withCredentials: true,
    });
  }

  recordProjectView(locale: AppLocale, slug: string): Observable<ApiItem<ProjectInteractionPayload>> {
    return this.http.post<ApiItem<ProjectInteractionPayload>>(
      `${this.baseUrl}/projects/${encodeURIComponent(slug)}/views`,
      { locale },
      { withCredentials: true },
    );
  }

  likeProject(locale: AppLocale, slug: string): Observable<ApiItem<ProjectInteractionPayload>> {
    return this.http.post<ApiItem<ProjectInteractionPayload>>(
      `${this.baseUrl}/projects/${encodeURIComponent(slug)}/likes`,
      { locale },
      { withCredentials: true },
    );
  }

  unlikeProject(locale: AppLocale, slug: string): Observable<ApiItem<ProjectInteractionPayload>> {
    return this.http.delete<ApiItem<ProjectInteractionPayload>>(`${this.baseUrl}/projects/${encodeURIComponent(slug)}/likes`, {
      params: this.localeParams(locale),
      withCredentials: true,
    });
  }

  submitContact(payload: ContactSubmissionPayload): Observable<ApiItem<AcknowledgmentPayload>> {
    return this.http.post<ApiItem<AcknowledgmentPayload>>(`${this.baseUrl}/contact`, payload, { withCredentials: true });
  }

  submitTestimonial(payload: TestimonialSubmissionPayload): Observable<ApiItem<AcknowledgmentPayload>> {
    return this.http.post<ApiItem<AcknowledgmentPayload>>(`${this.baseUrl}/testimonials`, payload, { withCredentials: true });
  }

  static validationErrors(error: unknown): Record<string, readonly string[]> {
    if (error instanceof HttpErrorResponse && error.status === 422 && isRecord(error.error?.errors)) {
      return Object.fromEntries(
        Object.entries(error.error.errors).map(([field, messages]) => [field, Array.isArray(messages) ? messages.map(String) : [String(messages)]]),
      );
    }

    return {};
  }

  private localeParams(locale: AppLocale): HttpParams {
    return new HttpParams().set('locale', locale);
  }
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null;
}
