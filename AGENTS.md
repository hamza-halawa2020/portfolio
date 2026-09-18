# AGENTS.md

Working rules for Codex and future agents on this repository.

1. Read `docs/PROJECT_REQUIREMENTS.md`, `docs/ARCHITECTURE.md`, and `docs/TASKS.md` before starting work.
2. Inspect the current code before editing.
3. Preserve existing user changes.
4. Update `docs/TASKS.md` before and after implementation.
5. Work on one clear task or tightly related task group at a time.
6. Do not mark tasks complete without verification.
7. Add or update tests for every feature.
8. Run focused tests after each feature.
9. Run broader tests before completing a milestone.
10. Update documentation whenever behavior, architecture, database schema, or APIs change.
11. Do not expose secrets, raw IP addresses, access tokens, or credentials.
12. Do not place business logic in Angular components, Laravel controllers, Filament resources, or Blade templates.
13. Avoid unnecessary dependencies.
14. Follow existing conventions before creating new abstractions.
15. Report blockers clearly and do not claim uncompleted work is finished.
16. Do not perform destructive Git or database operations without explicit approval.
17. Do not create commits unless specifically requested.
18. At the end of every session, update `docs/SESSION_LOG.md`.
19. Always state what was implemented, tested, and still pending.
20. Use English for code, database names, API keys, classes, methods, and technical documentation. User-facing content must support both Arabic and English.
21. Controllers must remain thin. Business logic, Eloquent query construction, filtering, visibility rules, localization, caching, and orchestration belong in application services or dedicated query classes. Controllers may only accept validated input, invoke a service, and return an API Resource or response.
