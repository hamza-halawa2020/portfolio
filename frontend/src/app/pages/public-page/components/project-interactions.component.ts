import { Component, EventEmitter, Input, Output } from '@angular/core';
import { ProjectDetail } from '../../../core/api/public-api.models';
import { AppLocale } from '../../../core/i18n/locale.service';
import { ProjectLikeViewState } from '../../../core/interactions/public-interaction.models';

@Component({
  selector: 'app-project-interactions',
  standalone: true,
  template: `
    <section class="interaction-panel" aria-live="polite">
      <div class="interaction-stat">
        <span>{{ locale === 'ar' ? 'المشاهدات' : 'Views' }}</span>
        <strong data-testid="project-view-count">{{ viewCount ?? project.view_count }}</strong>
      </div>
      <div class="interaction-stat">
        <span>{{ locale === 'ar' ? 'الإعجابات' : 'Likes' }}</span>
        <strong data-testid="project-like-count">{{
          likeState?.count ?? project.like_count
        }}</strong>
      </div>
      <button
        type="button"
        class="button-link interaction-button"
        [attr.aria-pressed]="likeState?.liked ?? false"
        [attr.aria-label]="likeLabel"
        [disabled]="likeState?.status === 'submitting' || likeState?.status === 'loading'"
        (click)="likeToggle.emit()"
      >
        {{ likeText }}
      </button>
      @if (likeState?.status === 'failure') {
        <p class="form-status">
          {{
            locale === 'ar'
              ? 'تعذر تحديث الإعجاب. حاول مرة أخرى.'
              : 'The like could not be updated. Please try again.'
          }}
        </p>
      }
    </section>
  `,
})
export class ProjectInteractionsComponent {
  @Input({ required: true }) project!: ProjectDetail;
  @Input({ required: true }) locale!: AppLocale;
  @Input() likeState: ProjectLikeViewState | null = null;
  @Input() viewCount?: number;
  @Output() readonly likeToggle = new EventEmitter<void>();

  get likeText(): string {
    if (this.likeState?.status === 'submitting') {
      return this.locale === 'ar' ? 'جار التحديث' : 'Updating';
    }

    return this.likeState?.liked
      ? this.locale === 'ar'
        ? 'إلغاء الإعجاب'
        : 'Unlike'
      : this.locale === 'ar'
        ? 'إعجاب'
        : 'Like';
  }

  get likeLabel(): string {
    return this.likeState?.liked
      ? this.locale === 'ar'
        ? `إلغاء الإعجاب بمشروع ${this.project.title}`
        : `Unlike ${this.project.title}`
      : this.locale === 'ar'
        ? `الإعجاب بمشروع ${this.project.title}`
        : `Like ${this.project.title}`;
  }
}
