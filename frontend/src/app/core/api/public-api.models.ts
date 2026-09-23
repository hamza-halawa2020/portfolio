export interface ApiCollection<T> {
  readonly data: readonly T[];
  readonly links?: ApiPaginationLinks;
  readonly meta?: ApiPaginationMeta;
}

export interface ApiItem<T> {
  readonly data: T;
}

export interface ApiPaginationLinks {
  readonly first?: string | null;
  readonly last?: string | null;
  readonly prev?: string | null;
  readonly next?: string | null;
}

export interface ApiPaginationMeta {
  readonly current_page?: number;
  readonly last_page?: number;
  readonly per_page?: number;
  readonly total?: number;
  readonly locale?: string;
}

export interface SitePayload {
  readonly settings: Record<string, string | number | boolean | null>;
  readonly navigation: readonly unknown[];
  readonly services: readonly Service[];
  readonly skills: readonly Skill[];
  readonly social_links: readonly SocialLink[];
}

export interface AboutPayload {
  readonly experience: readonly Experience[];
  readonly skills: readonly Skill[];
  readonly technologies: readonly Technology[];
  readonly social_links: readonly SocialLink[];
}

export interface ProjectSummary {
  readonly slug: string;
  readonly title: string;
  readonly summary: string;
  readonly cover_image_url: string | null;
  readonly category: ProjectCategory | null;
  readonly technologies: readonly Technology[];
  readonly is_featured: boolean;
  readonly published_at: string | null;
  readonly view_count: number;
  readonly like_count: number;
}

export interface ProjectDetail extends ProjectSummary {
  readonly body: string;
  readonly role: string;
  readonly duration: string;
  readonly industry: string;
  readonly challenge: string;
  readonly solution: string;
  readonly features: string;
  readonly development_challenges: string;
  readonly results: string;
  readonly metrics: string;
  readonly localized_slugs?: Partial<Record<'en' | 'ar', string | null>>;
  readonly media: readonly ProjectMedia[];
  readonly related_projects: readonly ProjectSummary[];
  readonly seo: SeoMetadata | null;
}

export interface BlogPostSummary {
  readonly slug: string;
  readonly title: string;
  readonly excerpt: string;
  readonly cover_image_url: string | null;
  readonly category: BlogCategory | null;
  readonly tags: readonly Tag[];
  readonly is_featured: boolean;
  readonly reading_time_minutes: number | null;
  readonly published_at: string | null;
}

export interface BlogPostDetail extends BlogPostSummary {
  readonly body: string;
  readonly localized_slugs?: Partial<Record<'en' | 'ar', string | null>>;
  readonly related_posts: readonly BlogPostSummary[];
  readonly seo: SeoMetadata | null;
}

export interface ProjectMedia {
  readonly type: string;
  readonly url: string | null;
  readonly poster_url: string | null;
  readonly caption: string;
  readonly alt_text: string;
  readonly sort_order: number;
}

export interface ProjectCategory {
  readonly slug: string;
  readonly name: string;
}

export interface BlogCategory {
  readonly slug: string;
  readonly name: string;
}

export interface Technology {
  readonly slug: string;
  readonly name: string;
}

export interface Tag {
  readonly slug: string;
  readonly name: string;
}

export interface Service {
  readonly slug: string;
  readonly title: string;
  readonly description: string;
  readonly icon: string | null;
  readonly sort_order: number;
}

export interface Skill {
  readonly name: string;
  readonly group: string | null;
  readonly level: number | null;
  readonly sort_order: number;
}

export interface Experience {
  readonly title: string;
  readonly company: string;
  readonly location: string;
  readonly description: string;
  readonly starts_at: string | null;
  readonly ends_at: string | null;
  readonly is_current: boolean;
  readonly sort_order: number;
}

export interface Testimonial {
  readonly name: string;
  readonly company: string | null;
  readonly position: string | null;
  readonly profile_image_url: string | null;
  readonly content: string;
  readonly rating: number | null;
  readonly is_featured: boolean;
  readonly project: ProjectSummary | null;
}

export interface SocialLink {
  readonly label: string;
  readonly url: string;
}

export interface SeoMetadata {
  readonly title?: string | null;
  readonly description?: string | null;
  readonly canonical_url?: string | null;
  readonly og_title?: string | null;
  readonly og_description?: string | null;
  readonly og_image_url?: string | null;
  readonly robots?: {
    readonly index?: boolean | null;
    readonly follow?: boolean | null;
  } | null;
  readonly structured_data?: unknown;
  readonly redirect_url?: string | null;
}
