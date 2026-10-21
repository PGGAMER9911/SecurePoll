# Phase 2 Testing Report: Authentication & Security Module

**Note to Developer:** Because the AI sandbox environment does not have XAMPP, PHP, or Composer installed, these test cases must be verified manually on the local development machine where XAMPP is running. Please execute these tests and update the Status column.

| Test Case | Expected Result | Actual Result | Status |
| :--- | :--- | :--- | :--- |
| **XAMPP Environment** | Apache and MySQL run without errors. | | PENDING |
| **Database Migrations** | `schema.sql` imports into MySQL without syntax or foreign key errors. | | PENDING |
| **Registration Flow** | User can register; DB stores `password_hash`; triggers OTP email via PHPMailer. | | PENDING |
| **Email OTP Flow** | Entering the correct OTP verifies the user; OTP is invalidated. | | PENDING |
| **OTP Expiry** | Submitting an OTP after 10 minutes throws an "expired" error. | | PENDING |
| **OTP Attempt Limit** | Entering wrong OTP 3 times blocks further attempts for that OTP record. | | PENDING |
| **Login Flow (Valid)** | Providing correct credentials grants access and creates a `login_sessions` record. | | PENDING |
| **Login Flow (Wrong Pwd)** | Providing wrong password denies access and logs a `LOGIN_FAILED` security event. | | PENDING |
| **Login Flow (Unverified)** | Attempting to login without verifying email first denies access. | | PENDING |
| **Trusted Device Flow** | Login from recognized device logs in immediately. | | PENDING |
| **New Device OTP** | Login from a new IP/Browser triggers OTP. Access is held until OTP passes. | | PENDING |
| **Forgot Password** | Requesting reset generates OTP and sends via PHPMailer. | | PENDING |
| **Password Reset** | Passing OTP grants reset token; updating password invalidates old sessions. | | PENDING |
| **Session Invalidation** | Password change correctly invalidates ALL active `login_sessions` for that user. | | PENDING |
| **Logout** | Logout successfully marks current `login_session` as inactive. | | PENDING |
| **CSRF Protection** | Submitting a POST request without `csrf_token` returns 403 Forbidden. | | PENDING |
| **Rate Limiting** | Exceeding 5 login attempts within 5 minutes returns 429 Too Many Requests. | | PENDING |
