# TotalCashPro — Production Readiness Report (Milestone 4)

**Date:** 7 August 2026  
**Phase:** Production Readiness (Commercial Audit alignment)  
**Estimated version target:** v1.0.1 (hardening) → v1.1 (finance ledger + MFA TOTP)

---

## Executive summary

This milestone begins **production hardening** against the commercial audit. Work was prioritised in audit order: **Critical → High → Medium → Low**.

**Phase 1 (Critical) — partially completed in this iteration:**

| Area | Status |
|------|--------|
| Tenancy validation (form requests + service checks) | **Started — core paths hardened** |
| Staff PIN security (hashing, no UI exposure) | **Completed** |
| Super Admin impersonation (reason, banner, exit, audit) | **Completed** |
| Demo seeder idempotency | **Completed** (prior fix) |
| Finance ledger / single source of truth | **Not started** |
| Activation links (no emailed passwords) | **Not started** |
| Full policy coverage | **Not started** |
| Responsive / a11y / marketing audit | **Not started** |

---

## Production readiness scores

| Metric | Score | Notes |
|--------|-------|-------|
| **Production readiness** | **62 / 100** | Core tenancy + PIN + impersonation improved; finance ledger and MFA gaps remain |
| **Commercial readiness** | **58 / 100** | Demo-ready; activation/password workflows and marketing truthfulness need work |
| **v1.0 launch readiness** | **Conditional** | OK for controlled pilot with seeded/demo tenants; not yet audit-complete for enterprise |

---

## Phase 1 — Critical findings

### Completed

#### 1. Tenant-scoped validation (server-side)

- Added `App\Support\Tenancy\TenantRules` for organisation-scoped `exists` rules (branches, suppliers, inventory items, staff, bank accounts).
- Added `BusinessAdminFormRequest` base with org context.
- Hardened:
  - `StaffStoreRequest` / `StaffUpdateRequest` — branch FK scoped to organisation; update authorizes org match.
  - `PurchaseOrderStoreRequest` — supplier + inventory line IDs scoped to organisation.
- `StaffService` — validates `branch_id` belongs to admin organisation before create/update.

#### 2. Staff PIN security

- Added `pin_hash` column migration; legacy `pin_code` values migrated to bcrypt hashes.
- Added `App\Support\Security\StaffPinHasher` for hash/verify/lookup.
- Kiosk and attendance PIN lookup uses hashed verification (no plaintext queries).
- Staff UI no longer displays PINs — shows **Configured / Not set** only.
- **Reset PIN** flow generates a new PIN shown once via flash message.
- `pin_hash` hidden from model serialization.

#### 3. Impersonation hardening

- Added `ImpersonationService` with:
  - Required **reason** (min 3 chars)
  - Session metadata (super admin, organisation, start time)
  - **120-minute auto timeout**
  - **Audit log** entries (`impersonation.started` / `impersonation.stopped`)
- Persistent **banner** in Business Admin layout with exit button.
- `POST /impersonation/stop` returns super admin to dashboard.
- Super Admin “Login As Business” form requires reason field.

#### 4. Automated tests added

| Test file | Coverage |
|-----------|----------|
| `tests/Feature/ProductionReadiness/TenantIsolationTest.php` | Foreign branch on staff create/update; foreign supplier on PO |
| `tests/Feature/ProductionReadiness/StaffPinSecurityTest.php` | Hash storage; UI does not expose PIN |
| `tests/Feature/ProductionReadiness/ImpersonationTest.php` | Start/stop flow; reason required; audit trail |

**7 new tests, all passing** (plus existing suite).

---

### Critical — remaining

| Issue | Priority | Notes |
|-------|----------|-------|
| Global Eloquent tenant scopes | Critical | Only manual `where organization_id` today — IDOR risk on direct model binding |
| Route model binding org checks | Critical | Many controllers use implicit binding without policy |
| Policies for all tenant models (~76 models, 5 policies today) | Critical | |
| Staff activation links (no temp password email) | Critical | `StaffInvitationNotification` still emails password |
| Super Admin org create still flashes password | Critical | `OrganizationController@store` |
| Mandatory MFA for Super Admin | Critical | Email OTP exists; TOTP + mandatory enforcement not done |
| Atomic transactions / no partial commits audit | Critical | Needs systematic service review |
| Drop legacy `pin_code` column | High | Kept for MySQL index/FK compatibility; app ignores it |

---

## Phase 2 — High findings (not started)

- Single finance workflow: Attendance → Payroll Run → Ledger → Reports → P&L → Cash Flow → Dashboard
- Posting states: Draft → Approved → Posted → Paid
- Immutable payroll/finance snapshots
- Journal posting layer (no `ledger` tables exist yet)
- Eliminate duplicate calculations in `AccountingService` vs finance modules

---

## Phase 3 — High findings (not started)

- Replace placeholder finance forms with full fields (amount, currency, date, reference, branch, category, VAT, approval, payment state, notes, review screen)

---

## Phase 4 — High findings (not started)

- End-to-end purchase workflow with partial/over/short receipts and backorders (partial PO receive exists; full audit not done)

---

## Phase 5 — High findings (partial)

| Item | Status |
|------|--------|
| Email OTP 2FA | Exists |
| Recovery codes | Exists |
| TOTP | Enum only — not implemented |
| Mandatory MFA (Super Admin) | Not enforced |
| No emailed passwords | **Not done** — staff invite + org create still expose passwords |
| PIN hashing | **Done** |

---

## Phase 6 — Impersonation

| Requirement | Status |
|-------------|--------|
| Persistent banner | Done |
| Super Admin + business shown | Done |
| Start time | Done |
| Reason | Done |
| Exit session | Done |
| Auto timeout | Done (120 min) |
| Audit trail | Done |

---

## Phases 7–12 — Not started

- **7** Error handling / atomic transactions
- **8** Unified report service + drill-down links
- **9** Responsive audit (all pages/modals)
- **10** Accessibility audit (ARIA, focus traps, headings)
- **11** Marketing truthfulness pass
- **12** Broader automated test matrix

---

## Files changed (this iteration)

### New files

- `app/Support/Tenancy/TenantRules.php`
- `app/Support/Security/StaffPinHasher.php`
- `app/Http/Requests/BusinessAdmin/BusinessAdminFormRequest.php`
- `app/Services/SuperAdmin/ImpersonationService.php`
- `app/Http/Controllers/SuperAdmin/ImpersonationController.php`
- `resources/views/components/admin/impersonation-banner.blade.php`
- `database/migrations/2026_08_07_220000_hash_staff_pins_and_tenancy_hardening.php`
- `tests/Feature/ProductionReadiness/TenantIsolationTest.php`
- `tests/Feature/ProductionReadiness/ImpersonationTest.php`
- `tests/Feature/ProductionReadiness/StaffPinSecurityTest.php`

### Modified (key)

- `app/Services/BusinessAdmin/StaffService.php`
- `app/Repositories/Eloquent/StaffRepository.php`
- `app/Models/User.php`
- `app/Http/Requests/BusinessAdmin/StaffStoreRequest.php`
- `app/Http/Requests/BusinessAdmin/StaffUpdateRequest.php`
- `app/Http/Requests/BusinessAdmin/PurchaseOrderStoreRequest.php`
- `app/Http/Controllers/SuperAdmin/OrganizationController.php`
- `app/Http/Controllers/BusinessAdmin/StaffController.php`
- `resources/views/business-admin/staff/*`
- `resources/views/components/layouts/business-admin.blade.php`
- `resources/views/admin/partials/organization-actions.blade.php`
- `routes/web.php`, `routes/business-admin.php`
- Seeders + tests updated for `pin_hash`

---

## Recommended next steps (in audit order)

1. **Tenancy** — `BelongsToOrganization` trait + global scope (with super-admin bypass); bind route models through tenant-aware resolver.
2. **Security** — Replace password emails with signed activation links; enforce MFA for Super Admin.
3. **Finance** — Introduce `finance_journal_entries` + posting service; wire payroll runs to ledger.
4. **Tests** — Expand tenant isolation matrix to finance, inventory, wages, exports, jobs.
5. **Marketing** — Remove placeholder testimonials/social links; align pricing/features with plan gates.

---

## Deploy notes

After pulling this branch on production:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Existing staff PINs are migrated to hashes automatically. Kiosk PINs continue to work after migration.

---

*This report reflects work completed in the Production Readiness Phase kickoff. Items marked “Not started” remain required for full commercial audit closure.*
