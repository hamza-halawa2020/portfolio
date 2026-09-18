import { TestBed } from '@angular/core/testing';
import { ShellNavigationService } from './shell-navigation.service';

describe('ShellNavigationService', () => {
  it('preserves static page location when switching languages', () => {
    const service = TestBed.inject(ShellNavigationService);

    expect(service.switchLocaleUrl('/en/services', 'ar')).toBe('/ar/services');
  });

  it('does not reuse dynamic content slugs across languages', () => {
    const service = TestBed.inject(ShellNavigationService);

    expect(service.switchLocaleUrl('/en/projects/source-slug', 'ar')).toBe('/ar/projects');
    expect(service.switchLocaleUrl('/ar/blog/local-slug', 'en')).toBe('/en/blog');
  });
});
