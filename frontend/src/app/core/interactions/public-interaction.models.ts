import { AppLocale } from '../i18n/locale.service';

export interface ProjectInteractionPayload {
  readonly count: number;
  readonly created?: boolean;
  readonly liked?: boolean;
}

export interface ContactSubmissionPayload {
  readonly locale: AppLocale;
  readonly name: string;
  readonly email: string;
  readonly phone?: string | null;
  readonly company?: string | null;
  readonly project_type?: string | null;
  readonly budget_range?: string | null;
  readonly message: string;
  readonly privacy_consent: boolean;
  readonly website?: string | null;
}

export interface TestimonialSubmissionPayload {
  readonly locale: AppLocale;
  readonly name: string;
  readonly company?: string | null;
  readonly position?: string | null;
  readonly content: string;
  readonly rating?: number | null;
  readonly contact_email: string;
  readonly project?: string | null;
  readonly publication_consent: boolean;
  readonly website?: string | null;
}

export interface AcknowledgmentPayload {
  readonly message: string;
}

export interface ApiValidationFailure {
  readonly type: 'validation';
  readonly message: string;
  readonly errors: Record<string, readonly string[]>;
}

export interface ApiRateLimitFailure {
  readonly type: 'rate-limit';
  readonly message: string;
  readonly retryAfterSeconds?: number;
}

export interface ApiRequestFailure {
  readonly type: 'network' | 'server' | 'unknown';
  readonly message: string;
}

export type PublicInteractionFailure = ApiValidationFailure | ApiRateLimitFailure | ApiRequestFailure;

export interface ProjectLikeViewState {
  readonly slug: string;
  readonly count: number;
  readonly liked: boolean;
  readonly status: 'idle' | 'loading' | 'ready' | 'submitting' | 'failure';
  readonly failure?: PublicInteractionFailure;
}

export interface SubmissionState {
  readonly status: 'idle' | 'submitting' | 'success' | 'validation-error' | 'rate-limit' | 'failure';
  readonly message?: string;
  readonly errors?: Record<string, readonly string[]>;
}
