import { TestBed } from '@angular/core/testing';
import { ThemeService } from './theme.service';

describe('ThemeService', () => {
  beforeEach(() => {
    localStorage.clear();
    document.documentElement.removeAttribute('data-bs-theme');
    document.documentElement.removeAttribute('data-theme-preference');
  });

  it('applies and persists selected theme preference', () => {
    const service = TestBed.inject(ThemeService);

    service.setPreference('dark');

    expect(document.documentElement.dataset['bsTheme']).toBe('dark');
    expect(document.documentElement.dataset['themePreference']).toBe('dark');
    expect(localStorage.getItem('portfolio.theme')).toBe('dark');
  });
});
