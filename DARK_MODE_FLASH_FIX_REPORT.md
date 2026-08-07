# Dark Mode Flash Fix Report

**Date:** August 7, 2026  
**Issue:** White flash (FOUC) when navigating with Dark Mode enabled  
**Panels:** Super Admin, Business Admin, Staff, Kiosk, Marketing, Auth, Error pages

---

## Root Cause

Dark Mode was applied **only after Alpine.js initialized** via:

```html
<html x-data="adminShell" :class="{ 'dark': dark }">
```

Until `app.js` loaded and Alpine ran, the `<html>` element had **no `dark` class**. Tailwind `dark:` variants did not apply, so:

- `body` rendered `bg-gray-50` (light gray / white appearance)
- Topbar, sidebar, and cards showed light backgrounds
- `admin-fade-in` animated **opacity from 0 → 1**, briefly revealing the light page background underneath

This caused a visible white flash on every full-page navigation (browser refresh, link click, module change).

**Secondary factors:**

| Factor | Impact |
|--------|--------|
| No inline theme script in `<head>` | Theme applied too late |
| `admin-fade-in` opacity animation | Exposed light background during transition |
| `body` base `bg-paper` without `dark:` fallback | White root when `dark` class missing |
| Kiosk / marketing layouts | No theme bootstrap at all |
| Theme read only `=== 'dark'` | Ignored system preference when unset |

No HTMX, Turbo, or SPA navigation is used — issue was classic **FOUC on full page loads**.

---

## Fix Summary

1. **Blocking theme script** in `<head>` (before CSS/JS) reads `localStorage` + `prefers-color-scheme` and sets `html.dark` immediately.
2. **Critical inline CSS** sets `html` / `body` background colors before Tailwind bundle loads.
3. **Shared `applyThemeClass()`** in `app.js` keeps Alpine toggle, `meta theme-color`, and `color-scheme` in sync.
4. **Removed Alpine `:class` binding on `<html>`** — theme managed via `classList` API (no hydration race).
5. **`admin-fade-in`** changed from opacity fade to subtle **translateY** only (no transparent frames).
6. **Dark-aware base styles** on `body`, marketing nav, and layout shells.

---

## Files Modified

| File | Change |
|------|--------|
| `resources/views/components/theme-init.blade.php` | **New** — blocking script + critical CSS |
| `resources/js/app.js` | `prefersDarkMode()`, `applyThemeClass()`, adminShell sync |
| `resources/css/app.css` | Body dark fallback, selection, nav-solid dark, fade animation |
| `resources/views/components/layouts/admin.blade.php` | `<x-theme-init />`, remove `:class` on html |
| `resources/views/components/layouts/business-admin.blade.php` | Same |
| `resources/views/components/layouts/staff.blade.php` | Same |
| `resources/views/components/layouts/kiosk.blade.php` | `<x-theme-init />` |
| `resources/views/components/layouts/marketing.blade.php` | `<x-theme-init />`, dark body classes |
| `resources/views/components/layouts/auth.blade.php` | `<x-theme-init />`, dark body classes |
| `resources/views/components/layouts/error.blade.php` | `<x-theme-init />`, `admin-panel` class |

---

## Blade Layouts Updated

All 7 shared layouts now include `<x-theme-init />` immediately after `<meta charset>`:

- Super Admin (`layouts/admin`)
- Business Admin (`layouts/business-admin`)
- Staff (`layouts/staff`)
- Attendance Kiosk (`layouts/kiosk`)
- Marketing (`layouts/marketing`)
- Auth (`layouts/auth`)
- Error (`layouts/error`)

---

## CSS Changes

```css
/* Inline in theme-init (before bundle) */
html { background-color: #f9fafb; color-scheme: light; }
html.dark { background-color: #030712; color-scheme: dark; }

/* app.css */
body { dark:bg-gray-950 dark:text-gray-100 }
html.dark ::selection { ... }
.nav-solid { dark:bg-gray-950/90 dark:border-gray-800 }

/* admin-fade-in: opacity removed */
@keyframes admin-fade-in {
  from { transform: translateY(4px); }
  to   { transform: translateY(0); }
}
```

---

## JavaScript Changes

**Storage key:** `tcp-admin-theme` (`dark` | `light` | unset → system)

**`prefersDarkMode()`**

- `dark` → true
- `light` → false
- unset → `matchMedia('(prefers-color-scheme: dark)')`

**`applyThemeClass(isDark)`**

- Toggles `document.documentElement.classList`
- Sets `color-scheme`
- Updates `<meta name="theme-color">` (`#030712` dark / `#16A34A` light)

**`adminShell`**

- Initializes `dark` via `prefersDarkMode()`
- Calls `applyThemeClass()` on init and on toggle
- Listens for `prefers-color-scheme` changes (when preference is system)
- Listens for `storage` events (cross-tab sync)

---

## Theme Initialization Flow

```
1. Browser parses <head>
2. theme-init inline <script> runs (synchronous, before paint)
3. html.dark + color-scheme + theme-color set
4. Inline <style> paints correct background
5. Vite CSS loads (Tailwind dark: variants work)
6. Alpine boots → adminShell confirms same state (no flip)
7. User toggles theme → localStorage + applyThemeClass()
```

---

## Pages Tested

| Panel | Desktop | Mobile | Dark | Light | Hard refresh |
|-------|---------|--------|------|-------|--------------|
| Super Admin dashboard | ✓ | ✓ | ✓ | ✓ | ✓ |
| Business Admin dashboard | ✓ | ✓ | ✓ | ✓ | ✓ |
| Staff dashboard | ✓ | ✓ | ✓ | ✓ | ✓ |
| Finance / Rota / Cash Up | ✓ | ✓ | ✓ | ✓ | ✓ |
| Smart Kiosk | ✓ | ✓ | ✓ | ✓ | ✓ |
| Marketing home | ✓ | ✓ | ✓ | ✓ | ✓ |
| Login / Auth | ✓ | ✓ | ✓ | ✓ | ✓ |
| Error 404 | ✓ | — | ✓ | ✓ | ✓ |

**Automated:** `php artisan test --filter=BusinessAdmin` — all passing.

---

## Performance Impact

| Metric | Impact |
|--------|--------|
| Inline script size | ~450 bytes (negligible) |
| Inline CSS | ~120 bytes |
| Layout shift (CLS) | None — background set before paint |
| JS bundle | +~40 lines theme helpers |
| Runtime overhead | One `localStorage` read per page load |

No additional network requests. No render-blocking beyond intended synchronous theme bootstrap (industry standard — GitHub, Stripe, etc.).

---

## Remaining Theme Issues

| Issue | Severity | Notes |
|-------|----------|-------|
| Marketing content sections | Low | Hero/pricing sections are light-designed; dark mode sets page background but some sections remain light-themed by design |
| Email templates | N/A | HTML emails unchanged |
| PDF / print exports | N/A | Out of scope |
| Explicit “System” theme UI | Low | System preference respected when no stored value; no three-way toggle in settings yet |
| `prefers-color-scheme` only on first visit | Info | First toggle writes explicit `dark` or `light` |

---

## Verification Checklist

1. Enable Dark Mode in Business Admin
2. Navigate rapidly: Dashboard → Staff → Finance → Rota → Cash Up
3. Hard refresh (Cmd+Shift+R) on any page
4. Open new tab to same panel
5. Confirm **no white frame** before dark UI appears

Theme should appear correctly from the **first painted frame**.
