import { Component, EventEmitter, Input, Output, OnChanges, SimpleChanges } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { AppLocale } from '../../../core/i18n/locale.service';
import { ContactSubmissionPayload, SubmissionState } from '../../../core/interactions/public-interaction.models';

@Component({
  imports: [ReactiveFormsModule],
  selector: 'app-contact-form',
  standalone: true,
  template: `
    <form class="interaction-form" [formGroup]="form" (ngSubmit)="submit()">
      <input class="visually-hidden" type="text" formControlName="website" tabindex="-1" autocomplete="off" />

      <label>
        <span>{{ locale === 'ar' ? 'الاسم' : 'Name' }}</span>
        <input type="text" formControlName="name" autocomplete="name" />
        @if (fieldError('name')) { <small>{{ fieldError('name') }}</small> }
      </label>

      <label>
        <span>{{ locale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</span>
        <input type="email" formControlName="email" autocomplete="email" />
        @if (fieldError('email')) { <small>{{ fieldError('email') }}</small> }
      </label>

      <label>
        <span>{{ locale === 'ar' ? 'الهاتف' : 'Phone' }}</span>
        <input type="tel" formControlName="phone" autocomplete="tel" />
      </label>

      <label>
        <span>{{ locale === 'ar' ? 'الشركة' : 'Company' }}</span>
        <input type="text" formControlName="company" autocomplete="organization" />
      </label>

      <label>
        <span>{{ locale === 'ar' ? 'نوع المشروع' : 'Project type' }}</span>
        <input type="text" formControlName="project_type" />
      </label>

      <label>
        <span>{{ locale === 'ar' ? 'نطاق الميزانية' : 'Budget range' }}</span>
        <input type="text" formControlName="budget_range" />
      </label>

      <label class="wide">
        <span>{{ locale === 'ar' ? 'الرسالة' : 'Message' }}</span>
        <textarea rows="6" formControlName="message"></textarea>
        @if (fieldError('message')) { <small>{{ fieldError('message') }}</small> }
      </label>

      <label class="checkbox-label wide">
        <input type="checkbox" formControlName="privacy_consent" />
        <span>{{ locale === 'ar' ? 'أوافق على استخدام بياناتي للرد على هذه الرسالة.' : 'I agree that my details may be used to respond to this message.' }}</span>
      </label>
      @if (fieldError('privacy_consent')) { <small class="wide">{{ fieldError('privacy_consent') }}</small> }

      <button class="button-link" type="submit" [disabled]="state.status === 'submitting'">
        {{ state.status === 'submitting' ? (locale === 'ar' ? 'جار الإرسال' : 'Sending') : (locale === 'ar' ? 'إرسال الرسالة' : 'Send message') }}
      </button>

      @if (state.status !== 'idle') {
        <p class="form-status" aria-live="polite">{{ statusMessage }}</p>
      }
    </form>
  `,
})
export class ContactFormComponent implements OnChanges {
  @Input({ required: true }) locale!: AppLocale;
  @Input({ required: true }) state!: SubmissionState;
  @Output() readonly submitted = new EventEmitter<ContactSubmissionPayload>();

  readonly form = new FormBuilder().nonNullable.group({
    budget_range: [''],
    company: [''],
    email: ['', [Validators.required, Validators.email]],
    message: ['', [Validators.required, Validators.minLength(10)]],
    name: ['', [Validators.required, Validators.minLength(2)]],
    phone: [''],
    privacy_consent: [false, Validators.requiredTrue],
    project_type: [''],
    website: [''],
  });

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['state'] && this.state?.status === 'success') {
      this.form.reset({ privacy_consent: false });
    }
  }

  submit(): void {
    this.form.markAllAsTouched();
    if (this.form.invalid) {
      return;
    }

    this.submitted.emit({ locale: this.locale, ...this.form.getRawValue() });
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
      return this.locale === 'ar' ? 'يتم إرسال الرسالة.' : 'Sending your message.';
    }

    if (this.state.status === 'success') {
      return this.locale === 'ar' ? 'تم استلام الرسالة بنجاح.' : 'Message received successfully.';
    }

    return this.locale === 'ar' ? 'تعذر إرسال الرسالة.' : 'The message could not be sent.';
  }
}
