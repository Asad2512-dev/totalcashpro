# Attendance Kiosk & Marketing Update Report

**TotalCashPro v1.0 · August 2026**

---

## Summary

Implemented a dedicated **Attendance Kiosk** — a full-screen, touch-first clock terminal separate from the Business Admin and Staff dashboards. Updated the **marketing website** to reflect current v1.0 capabilities including finance, CRM, HR, security, and the new kiosk.

---

## Completed Features — Attendance Kiosk

| Feature | Status |
|---------|--------|
| Dedicated kiosk layout (no sidebar/nav) | ✅ |
| Welcome screen with org logo, branch, live clock | ✅ |
| 4-digit PIN keypad with Clear / Delete | ✅ |
| Clock In / Out / Start Break / End Break | ✅ |
| View Today's Hours | ✅ |
| Success screen with auto-return | ✅ |
| Failed PIN shake animation + auto-clear | ✅ |
| Staff name + avatar/initials after PIN | ✅ |
| Branch-scoped PIN validation | ✅ |
| Kiosk mode session (launch + lock) | ✅ |
| Exit kiosk with admin password | ✅ |
| Configurable welcome message & timeout | ✅ |
| Activity audit log (open/close/PIN fail/clock events) | ✅ |
| PWA manifest (`kiosk-manifest.webmanifest`) | ✅ |
| Dark mode support | ✅ |
| Responsive / tablet-optimised UI | ✅ |

---

## New Routes

| Method | URI | Name |
|--------|-----|------|
| GET | `/business-admin/kiosk` | `business-admin.kiosk.index` |
| GET | `/business-admin/kiosk/settings` | `business-admin.kiosk.settings` |
| PUT | `/business-admin/kiosk/settings` | `business-admin.kiosk.settings.update` |
| POST | `/business-admin/kiosk/activate` | `business-admin.kiosk.activate` |
| POST | `/business-admin/kiosk/exit` | `business-admin.kiosk.exit` |
| POST | `/business-admin/kiosk/verify` | `business-admin.kiosk.verify` |
| POST | `/business-admin/kiosk/action` | `business-admin.kiosk.action` |
| GET | `/features` | `features` (dedicated page, was redirect) |

All kiosk routes require Business Admin auth + `plan_feature:attendance`. PIN endpoints are throttled (30/min).

---

## Controllers

| File | Purpose |
|------|---------|
| `app/Http/Controllers/BusinessAdmin/KioskController.php` | Launch, terminal, settings, activate/exit, verify/action API |

Existing `ClockInController` retained for in-panel quick clock-in.

---

## Services

| File | Purpose |
|------|---------|
| `app/Services/BusinessAdmin/KioskService.php` | Session management, settings (org JSON), audit logging |
| `app/Services/BusinessAdmin/AttendanceService.php` | Added `verifyPinForBranch()` and `actionForBranch()` |

---

## Middleware

| File | Purpose |
|------|---------|
| `app/Http/Middleware/EnsureKioskLock.php` | When kiosk active, redirects all BA routes to kiosk (except kiosk routes + logout) |

Registered as `kiosk_lock` on the business-admin route group.

---

## Views

| File | Purpose |
|------|---------|
| `resources/views/components/layouts/kiosk.blade.php` | Standalone kiosk shell |
| `resources/views/business-admin/kiosk/index.blade.php` | Full-screen terminal |
| `resources/views/business-admin/kiosk/launch.blade.php` | Branch select + launch (when inactive) |
| `resources/views/business-admin/kiosk/settings.blade.php` | Kiosk configuration |

---

## Frontend

| File | Purpose |
|------|---------|
| `resources/js/app.js` | Alpine `attendanceKiosk` component |
| `resources/css/app.css` | Kiosk keypad, action buttons, shake animation |
| `public/kiosk-manifest.webmanifest` | PWA-ready manifest |

---

## Database Changes

**None.** Kiosk settings stored in existing `organizations.settings` JSON under `kiosk` key:

```json
{
  "welcome_message": "...",
  "session_timeout_minutes": 480,
  "success_display_seconds": 4,
  "show_photos": true
}
```

Audit events written to existing `activity_logs` table via `ActivityLogger`.

---

## Marketing Improvements

| Area | Changes |
|------|---------|
| **Hero** | Highlights restaurant management, attendance kiosk, finance, payroll, inventory, CRM, multi-branch |
| **Features grid** | Added kiosk, finance, CRM, HR, purchase orders, security, notifications |
| **Features page** | `/features` — grouped categories (Kiosk, Operations, Finance, People, Platform) |
| **Employee workflow** | New section: Arrive → PIN → Attendance → Payroll → Finance → Reports |
| **Showcase** | Updated copy + kiosk tablet mockup |
| **Pricing** | Enterprise (Coming Soon) tier + feature comparison table |
| **Why choose** | Rewritten around kiosk, cloud, security, multi-branch, finance |
| **FAQ** | Added kiosk, finance, inventory, payroll, security, branches |

---

## SEO Improvements

| Item | Detail |
|------|--------|
| `config/totalcashpro.php` | Updated title, description, keywords (UK restaurant, attendance kiosk, POS back office) |
| Marketing layout | Added `SoftwareApplication` Schema.org JSON-LD |
| Features page | Dedicated meta title, description, canonical URL |

---

## Responsive & Accessibility

- Touch targets ≥ 64px on keypad buttons
- `touch-action: manipulation`, reduced motion support for shake animation
- Screen reader labels on PIN input and exit modal
- Skip-friendly table captions on pricing comparison
- `prefers-reduced-motion` disables kiosk shake animation

---

## Tests

New file: `tests/Feature/AttendanceKioskTest.php` (6 tests)

- Launch page loads
- Activate kiosk → terminal view
- Kiosk lock redirects dashboard
- Settings page
- PIN verify requires active kiosk
- Features page includes kiosk content

---

## Known Issues

1. **PWA service worker** — Manifest only; full offline SW not yet implemented for kiosk.
2. **QR / NFC clock-in** — Planned future enhancement (not in v1.0).
3. **Org logo upload** — Kiosk falls back to platform logo if `organizations.logo_path` is empty.
4. **Staff photos** — Shown when `users.avatar_path` is set; most seed data has no avatars (initials used).
5. **Legacy clock-in** — `/business-admin/clock-in` still exists inside admin shell for quick access.

---

## Future Enhancements

- Installable PWA with service worker for kiosk
- QR code / NFC staff badge scan
- Kiosk screensaver during idle timeout
- Per-branch kiosk settings
- Push notification when staff clocks in late
- Export kiosk audit log report
- Fullscreen API toggle on launch

---

## How to Use

1. Log in as Business Admin (e.g. `ava@harbourkitchen.test` / `password`)
2. Go to **People → Attendance Kiosk**
3. Select branch → **Launch Kiosk Mode**
4. Staff enter PIN (e.g. `1000` for Jamie)
5. To exit: tap bottom-left corner → enter admin password

Settings: **Business Admin → Attendance Kiosk → Kiosk Settings**

---

*Report generated as part of TotalCashPro v1.0 Attendance Kiosk & Marketing milestone.*
