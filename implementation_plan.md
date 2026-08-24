# Authentication Bugfix & Cybersecurity Hardening Plan

## Summary
Currently, the application allows any user to log in with arbitrary credentials because the login forms perform direct GET redirects or mock JS timers without authenticating against the database, and routes lack authentication/role middlewares. Furthermore, we need to enforce single active session (One Session), login rate limiting (throttling), Google reCAPTCHA verification, secure HttpOnly cookies, and strict prevention of SQL statement leakage/injection.

---

## User Review Required
> [!IMPORTANT]
> - **Seeded Credentials** (`UserSeeder`):
>   - **Manajer**: `manajer@example.com` / `password123` (Role: `manajer`) &rarr; Access to Web Dashboard.
>   - **Petugas Distribusi**: `distribusi@example.com` / `password123` (Role: `petugas_distribusi`) &rarr; Access to Mobile Distribusi.
>   - **Petugas Pembesaran**: `pembesaran@example.com` / `password123` (Role: `pembesaran`) &rarr; Access to Mobile Pembesaran.
>   - **Petugas Pembibitan**: `pembibitan@example.com` / `password123` (Role: `pembibitan`) &rarr; Access to Mobile Pembibitan.
> - **reCAPTCHA**: Configurable via `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` in `.env`. When keys are empty or set to test mode, validation safely bypasses to allow seamless local development while actively enforcing validation when Google keys are provided.
> - **One Session**: When a user logs in, previous active sessions from other devices/browsers are automatically invalidated.

---

## Proposed Changes

### 1. Security & Protection Layer

#### [NEW] [Recaptcha.php](file:///c:/laragon/www/manajemenikan/app/Rules/Recaptcha.php)
- Custom Laravel validation rule that contacts Google reCAPTCHA verification endpoint.
- Handles graceful fallback for local development if keys are not configured.

#### [NEW] [RoleMiddleware.php](file:///c:/laragon/www/manajemenikan/app/Http/Middleware/RoleMiddleware.php)
- Middleware that checks if `Auth::user()->role` matches the allowed role(s) for the route.
- If unauthorized, redirects or aborts with a 403 Forbidden page without leaking internal details.

#### [NEW] [SecurityHeadersMiddleware.php](file:///c:/laragon/www/manajemenikan/app/Http/Middleware/SecurityHeadersMiddleware.php)
- Appends security headers to all HTTP responses: `X-Frame-Options`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection`, `Referrer-Policy`.

#### [MODIFY] [bootstrap/app.php](file:///c:/laragon/www/manajemenikan/bootstrap/app.php)
- Register `AuthenticateSession` in the web middleware stack for one-session enforcement.
- Register security headers middleware and role middleware alias.
- Configure global exception handling to catch `QueryException` and `PDOException` so SQL queries and database internals are never leaked to client responses or Javascript.

#### [MODIFY] [config/services.php](file:///c:/laragon/www/manajemenikan/config/services.php)
- Add configuration entries for `recaptcha.site_key` and `recaptcha.secret_key`.

#### [MODIFY] [config/session.php](file:///c:/laragon/www/manajemenikan/config/session.php)
- Ensure `http_only` is `true`, `same_site` is `lax`, and cookies cannot be accessed by client-side Javascript.

---

### 2. Authentication Logic

#### [NEW] [AuthController.php](file:///c:/laragon/www/manajemenikan/app/Http/Controllers/AuthController.php)
- **Web Manajer Login**:
  - Validates login identifier (Email or Phone) and password.
  - Rate Limiter check (max 5 attempts/minute with countdown timer).
  - reCAPTCHA validation.
  - Verifies role is `manajer`. Prevents petugas from logging into manajer dashboard.
  - Enforces single session (`Auth::logoutOtherDevices` + session table cleanup).
  - Regenerates session ID.
- **Mobile Petugas Login**:
  - Validates login identifier (Email or Phone) and password.
  - Rate Limiter check & reCAPTCHA validation.
  - Verifies user's role matches selected role tab (`petugas_distribusi`, `pembesaran`, or `pembibitan`).
  - Enforces single session & regenerates session ID.
  - Redirects to the respective mobile dashboard.
- **Logout Action**:
  - Invalidate session, regenerate CSRF token, and redirect to login page.

#### [MODIFY] [app/Http/Controllers/Api/AuthController.php](file:///c:/laragon/www/manajemenikan/app/Http/Controllers/Api/AuthController.php)
- Add rate limiter, single token enforcement (`$user->tokens()->delete()`), role validation, and exception shielding.

---

### 3. Routes & UI Integration

#### [MODIFY] [routes/web.php](file:///c:/laragon/www/manajemenikan/routes/web.php)
- Add POST `/login` and POST `/logout` for web manager.
- Add POST `/mobile-petugas/login` and POST `/mobile-petugas/logout` for mobile petugas.
- Protect `/dashboard`, `/pembibitan`, `/pembesaran`, etc. with `['auth', 'role:manajer']`.
- Protect `/mobile-petugas/pengiriman`, `/riwayat`, `/akun` with `['auth', 'role:petugas_distribusi']`.
- Protect `/petugas-pembibitan/*` with `['auth', 'role:pembibitan']`.
- Protect `/petugas-pembesaran/*` with `['auth', 'role:pembesaran']`.

#### [MODIFY] [resources/views/layouts/auth/login.blade.php](file:///c:/laragon/www/manajemenikan/resources/views/layouts/auth/login.blade.php)
- Convert form to POST `action="{{ route('login.post') }}"` with `@csrf`.
- Display server validation and rate limit error messages safely with alerts.
- Embed Google reCAPTCHA widget.

#### [MODIFY] [resources/views/mobile_web_petugas/login.blade.php](file:///c:/laragon/www/manajemenikan/resources/views/mobile_web_petugas/login.blade.php)
- Replace mock Javascript timeout with real POST form submission to backend authentication.
- Display server-side error messages, toast notifications, and reCAPTCHA widget.

#### [MODIFY] [resources/views/layouts/app.blade.php](file:///c:/laragon/www/manajemenikan/resources/views/layouts/app.blade.php) & Mobile Akun Blades
- Update dynamic user information (`Auth::user()->nama`, `Auth::user()->role`).
- Implement real POST logout forms with CSRF protection.

---

## Verification Plan

### Automated / Manual Verification
1. **Invalid Credentials Test**:
   - Try random username/password &rarr; Verify login fails with safe error message (no SQL leakage).
2. **Role Mismatch Test**:
   - Try logging into Web Manager as `pembesaran@example.com` &rarr; Verify access denied.
   - Try logging into Mobile Pembibitan as `distribusi@example.com` &rarr; Verify access denied.
3. **Valid Credentials Test**:
   - Login as `manajer@example.com` / `password123` &rarr; Verify successful redirect to `/dashboard`.
   - Login as `distribusi@example.com` / `password123` &rarr; Verify redirect to `/mobile-petugas/pengiriman`.
   - Login as `pembesaran@example.com` / `password123` &rarr; Verify redirect to `/petugas-pembesaran/`.
   - Login as `pembibitan@example.com` / `password123` &rarr; Verify redirect to `/petugas-pembibitan/`.
4. **Rate Limiting Test**:
   - Submit wrong password 5 times &rarr; Verify rate limiter blocks next attempt with remaining seconds countdown.
5. **One Session Test**:
   - Login in browser A, then login in browser B &rarr; Verify browser A session is invalidated upon next request.
6. **SQL Injection Test**:
   - Submit malicious payload (`admin' OR 1=1 --`) &rarr; Verify query is parameterized, no SQL exception is displayed.
