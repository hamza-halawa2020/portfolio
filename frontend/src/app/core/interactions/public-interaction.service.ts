import { HttpErrorResponse } from '@angular/common/http';
import { isPlatformBrowser } from '@angular/common';
import { inject, Injectable, PLATFORM_ID, signal } from '@angular/core';
import { EMPTY, catchError, finalize, tap } from 'rxjs';
import { ProjectDetail } from '../api/public-api.models';
import { AppLocale } from '../i18n/locale.service';
import { PublicInteractionApiService } from './public-interaction-api.service';
import {
  ContactSubmissionPayload,
  ProjectLikeViewState,
  PublicInteractionFailure,
  SubmissionState,
  TestimonialSubmissionPayload,
} from './public-interaction.models';

const IDLE_SUBMISSION: SubmissionState = { status: 'idle' };

@Injectable({ providedIn: 'root' })
export class PublicInteractionService {
  private readonly api = inject(PublicInteractionApiService);
  private readonly platformId = inject(PLATFORM_ID);
  private readonly recordedViews = new Set<string>();
  private readonly pendingLikes = new Set<string>();
  private readonly _projectLikeState = signal<ProjectLikeViewState | null>(null);
  private readonly _projectViewCounts = signal<Record<string, number>>({});
  private readonly _contactSubmission = signal<SubmissionState>(IDLE_SUBMISSION);
  private readonly _testimonialSubmission = signal<SubmissionState>(IDLE_SUBMISSION);

  readonly contactSubmission = this._contactSubmission.asReadonly();
  readonly projectLikeState = this._projectLikeState.asReadonly();
  readonly projectViewCounts = this._projectViewCounts.asReadonly();
  readonly testimonialSubmission = this._testimonialSubmission.asReadonly();

  recordProjectView(locale: AppLocale, project: ProjectDetail): void {
    if (!this.isBrowser()) {
      return;
    }

    const key = `${locale}:${project.slug}`;
    if (this.recordedViews.has(key)) {
      return;
    }

    this.recordedViews.add(key);
    this.api.recordProjectView(locale, project.slug).pipe(
      tap((response) => this.setViewCount(project.slug, response.data.count)),
      catchError(() => EMPTY),
    ).subscribe();
  }

  loadProjectLikeState(locale: AppLocale, project: ProjectDetail): void {
    if (!this.isBrowser()) {
      this._projectLikeState.set({ count: project.like_count, liked: false, slug: project.slug, status: 'idle' });
      return;
    }

    this._projectLikeState.set({ count: project.like_count, liked: false, slug: project.slug, status: 'loading' });
    this.api.projectLikeState(locale, project.slug).pipe(
      tap((response) => {
        this._projectLikeState.set({
          count: response.data.count,
          liked: response.data.liked ?? false,
          slug: project.slug,
          status: 'ready',
        });
      }),
      catchError((error) => {
        this._projectLikeState.set({
          count: project.like_count,
          failure: this.toFailure(error),
          liked: false,
          slug: project.slug,
          status: 'failure',
        });

        return EMPTY;
      }),
    ).subscribe();
  }

  toggleProjectLike(locale: AppLocale, project: ProjectDetail): void {
    if (!this.isBrowser() || this.pendingLikes.has(project.slug)) {
      return;
    }

    const current = this._projectLikeState();
    const liked = current?.slug === project.slug ? current.liked : false;
    this.pendingLikes.add(project.slug);
    this._projectLikeState.set({
      count: current?.slug === project.slug ? current.count : project.like_count,
      liked,
      slug: project.slug,
      status: 'submitting',
    });

    const request = liked ? this.api.unlikeProject(locale, project.slug) : this.api.likeProject(locale, project.slug);
    request.pipe(
      tap((response) => {
        this._projectLikeState.set({
          count: response.data.count,
          liked: response.data.liked ?? !liked,
          slug: project.slug,
          status: 'ready',
        });
      }),
      catchError((error) => {
        this._projectLikeState.set({
          count: current?.slug === project.slug ? current.count : project.like_count,
          failure: this.toFailure(error),
          liked,
          slug: project.slug,
          status: 'failure',
        });

        return EMPTY;
      }),
      finalize(() => this.pendingLikes.delete(project.slug)),
    ).subscribe();
  }

  submitContact(payload: ContactSubmissionPayload): void {
    if (!this.isBrowser() || this._contactSubmission().status === 'submitting') {
      return;
    }

    this._contactSubmission.set({ status: 'submitting' });
    this.api.submitContact(payload).pipe(
      tap((response) => this._contactSubmission.set({ message: response.data.message, status: 'success' })),
      catchError((error) => {
        this._contactSubmission.set(this.toSubmissionState(error));

        return EMPTY;
      }),
    ).subscribe();
  }

  submitTestimonial(payload: TestimonialSubmissionPayload): void {
    if (!this.isBrowser() || this._testimonialSubmission().status === 'submitting') {
      return;
    }

    this._testimonialSubmission.set({ status: 'submitting' });
    this.api.submitTestimonial(payload).pipe(
      tap((response) => this._testimonialSubmission.set({ message: response.data.message, status: 'success' })),
      catchError((error) => {
        this._testimonialSubmission.set(this.toSubmissionState(error));

        return EMPTY;
      }),
    ).subscribe();
  }

  resetContactSubmission(): void {
    this._contactSubmission.set(IDLE_SUBMISSION);
  }

  resetTestimonialSubmission(): void {
    this._testimonialSubmission.set(IDLE_SUBMISSION);
  }

  private setViewCount(slug: string, count: number): void {
    this._projectViewCounts.update((counts) => ({ ...counts, [slug]: count }));
  }

  private toSubmissionState(error: unknown): SubmissionState {
    const failure = this.toFailure(error);

    if (failure.type === 'validation') {
      return { errors: failure.errors, message: failure.message, status: 'validation-error' };
    }

    if (failure.type === 'rate-limit') {
      return { message: failure.message, status: 'rate-limit' };
    }

    return { message: failure.message, status: 'failure' };
  }

  private toFailure(error: unknown): PublicInteractionFailure {
    if (error instanceof HttpErrorResponse) {
      if (error.status === 422) {
        return {
          errors: PublicInteractionApiService.validationErrors(error),
          message: typeof error.error?.message === 'string' ? error.error.message : 'Please check the highlighted fields.',
          type: 'validation',
        };
      }

      if (error.status === 429) {
        const retryAfter = Number(error.headers.get('Retry-After'));

        return {
          message: 'Too many attempts. Please try again shortly.',
          retryAfterSeconds: Number.isFinite(retryAfter) ? retryAfter : undefined,
          type: 'rate-limit',
        };
      }

      if (error.status === 0) {
        return { message: 'The network request could not be completed.', type: 'network' };
      }

      return { message: 'The request could not be completed.', type: 'server' };
    }

    return { message: 'The request could not be completed.', type: 'unknown' };
  }

  private isBrowser(): boolean {
    return isPlatformBrowser(this.platformId);
  }
}
