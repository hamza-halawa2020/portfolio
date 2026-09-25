import { Component, EventEmitter, Input, Output, OnChanges, SimpleChanges } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { AppLocale } from '../../../core/i18n/locale.service';
import { SubmissionState, TestimonialSubmissionPayload } from '../../../core/interactions/public-interaction.models';

@Component({
  imports: [ReactiveFormsModule],
  selector: 'app-testimonial-form',
  standalone: true,
  template: `
    <form class="interaction-form" [formGroup]="form" (ngSubmit)="submit()">
      <input class="visually-hidden" type="text" formControlName="website" tabindex="-1" autocomplete="off" aria-hidden="true" />

      <label>
        <span>{{ locale === 'ar' ? 'الاسم' : 'Name' }}</span>
        <input type="text" formControlName="name" autocomplete="name" [attr.aria-invalid]="fieldError('name') ? 'true' : null" />
        @if (fieldError('name')) { <small>{{ fieldError('name') }}</small> }
      </label>

      <label>
        <span>{{ locale === 'ar' ? 'بريد التحقق' : 'Verification email' }}</span>
        <input type="email" formControlName="contact_email" autocomplete="email" [attr.aria-invalid]="fieldError('contact_email') ? 'true' : null" />
        @if (fieldError('contact_email')) { <small>{{ fieldError('contact_email') }}</small> }
      </label>

      <label>
        <span>{{ locale === 'ar' ? 'الشركة' : 'Company' }}</span>
        <input type="text" formControlName="company" autocomplete="organization" />
      </label>

      <label>
        <span>{{ locale === 'ar' ? 'المسمى' : 'Position' }}</span>
        <input type="text" formControlName="position" />
      </label>

      <label>
        <span>{{ locale === 'ar' ? 'التقييم' : 'Rating' }}</span>
        <select formControlName="rating">
          <option [ngValue]="null">{{ locale === 'ar' ? 'بدون تقييم' : 'No rating' }}</option>
          @for (rating of ratings; track rating) {
            <option [ngValue]="rating">{{ rating }}</option>
          }
        </select>
      </label>

      <label class="wide">
        <span>{{ locale === 'ar' ? 'الشهادة' : 'Testimonial' }}</span>
        <textarea rows="5" formControlName="content" [attr.aria-invalid]="fieldError('content') ? 'true' : null"></textarea>
        @if (fieldError('content')) { <small>{{ fieldError('content') }}</small> }
      </label>

      <label class="checkbox-label wide">
        <input type="checkbox" formControlName="publication_consent" />
        <span>{{ locale === 'ar' ? 'أوافق على مراجعة ونشر هذه الشهادة.' : 'I agree that this testimonial may be reviewed and published.' }}</span>
      </label>
      @if (fieldError('publication_consent')) { <small class="wide">{{ fieldError('publication_consent') }}</small> }

      <button class="button-link" type="submit" [disabled]="state.status === 'submitting'">
        {{ state.status === 'submitting' ? (locale === 'ar' ? 'جار الإرسال' : 'Sending') : (locale === 'ar' ? 'إرسال الشهادة' : 'Submit testimonial') }}
      </button>

      @if (state.status !== 'idle') {
        <p class="form-status" aria-live="polite">{{ statusMessage }}</p>
      }
    </form>
  `,
})
export class TestimonialFormComponent implements OnChanges {
  @Input({ required: true }) locale!: AppLocale;
  @Input({ required: true }) state!: SubmissionState;
  @Input() projectSlug?: string | null;
  @Output() readonly submitted = new EventEmitter<TestimonialSubmissionPayload>();

  readonly ratings = [1, 2, 3, 4, 5] as const;
  readonly form = new FormBuilder().nonNullable.group({
    company: [''],
    contact_email: ['', [Validators.required, Validators.email]],
    content: ['', [Validators.required, Validators.minLength(10)]],
    name: ['', [Validators.required, Validators.minLength(2)]],
    position: [''],
    publication_consent: [false, Validators.requiredTrue],
    rating: [null as number | null],
    website: [''],
  });

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['state'] && this.state?.status === 'success') {
      this.form.reset({ publication_consent: false, rating: null });
    }
  }

  submit(): void {
    this.form.markAllAsTouched();
    if (this.form.invalid) {
      return;
    }

    this.submitted.emit({ locale: this.locale, project: this.projectSlug ?? null, ...this.form.getRawValue() });
  }

  fieldError(field: string): string | null {
    const control = this.form.get(field);
    if (control?.invalid && control.touched) {
      return this.locale === 'ar' ? 'يرجى مراجعة هذا الحقل.' : 'Please check this field.';
    }

    return this.state.errors?.[field]?.[0] ?? null;
  }

  get statusMessage(): string {
    if (this.state.message) {
      return this.state.message;
    }

    if (this.state.status === 'submitting') {
      return this.locale === 'ar' ? 'يتم إرسال الشهادة.' : 'Sending your testimonial.';
    }

    if (this.state.status === 'success') {
      return this.locale === 'ar' ? 'تم استلام الشهادة للمراجعة.' : 'Testimonial received for review.';
    }

    return this.locale === 'ar' ? 'تعذر إرسال الشهادة.' : 'The testimonial could not be sent.';
  }
}
