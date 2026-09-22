# PT INTI — Production Readiness Checklist

## Status

Static hardening and source-level audit completed for the current working tree.

### Core workflow

- Karyawan → VP Divisi → HR.
- VP approval is restricted to the employee's division.
- HR can verify only VP-approved submissions.
- `VERIFIED_HR` cannot be bypassed by the VP endpoint.
- Invalid/zero recognized overtime cannot be approved or verified.
- Employee consent is required and evidence is stored (timestamp, IP, user-agent, source, version, text).

### Overtime rules

- Normal schedule: 07:30–16:30, Monday–Friday.
- Weekday overtime starts after 16:30.
- Rounding is downward in 30-minute units.
- Weekday minimum: 30 minutes.
- Weekend/national-holiday minimum: 60 minutes.
- General legal weekday limits are separated from company monthly limits.
- Weekly legal accumulation is not applied to weekly-rest/public-holiday overtime because the legal rule treats those periods separately.
- Same-day only; no shift/overnight logic.

### Attendance import

- HR-only access.
- Preview before commit.
- XLSX/XLS/CSV support.
- Employee master import validates NIPEG, name, and division.
- Attendance scan-format files are aggregated to earliest scan / latest scan.
- Existing attendance from another source is not silently overwritten when values differ; it is held for HR review.
- Import mutations are audited.
- Import errors remain reviewable.

### Anomaly and audit

- Anomaly Center detects attendance and overtime integrity issues.
- Anomaly resolution is audited.
- Audit logs are immutable through the Eloquent model (update/delete blocked).
- Audit entries include actor, action, subject, before/after data, IP, user-agent, and request ID.

### Deployment

Run on the deployment server, after database backup:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan production:preflight
php artisan overtime:policy-health
php artisan route:list
```

For application verification, run the full test suite on an environment containing Composer dependencies:

```bash
./vendor/bin/pest
```

The current review environment does not contain Composer, so the full Laravel/Pest runtime suite could not be executed here. PHP syntax validation was completed across the application, migrations, routes, and tests.

## Go-live discipline

1. Back up the production database.
2. Import employee master and verify division mapping.
3. Verify holiday calendar and active overtime policies.
4. Run production preflight.
5. Run a controlled attendance import with a small known dataset.
6. Review import errors and anomalies.
7. Test one employee → VP → HR overtime flow end-to-end.
8. Only after UAT passes, switch from dummy/QA data to production attendance data.
