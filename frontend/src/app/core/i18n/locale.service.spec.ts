import { TestBed } from '@angular/core/testing';
import { LocaleService } from './locale.service';

describe('LocaleService', () => {
  it('sets lang and dir from localized URLs', () => {
    const service = TestBed.inject(LocaleService);

    expect(service.activateLocaleFromUrl('/ar/projects')).toBe('ar');
    expect(document.documentElement.lang).toBe('ar');
    expect(document.documentElement.dir).toBe('rtl');

    expect(service.activateLocaleFromUrl('/en/blog')).toBe('en');
    expect(document.documentElement.lang).toBe('en');
    expect(document.documentElement.dir).toBe('ltr');
  });
});
