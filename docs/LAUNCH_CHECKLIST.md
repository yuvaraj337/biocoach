# Jerin's Coaching — Release Candidate / Launch Checklist

This is a Laravel source package, not a deployed website. Complete these checks on the target server before public launch.

## Setup
- Install a fresh Laravel 11/12 app and merge this package's `app`, `routes`, `database`, `resources`, `public`, and `config` folders.
- Confirm the standard Laravel users migration exists before migrations.
- Configure MySQL, `APP_KEY`, `APP_URL`, mail credentials, session/cache drivers, and private local storage.
- Run `composer install`, `php artisan key:generate`, and `php artisan migrate` only after validating environment settings.
- Grant administrator access manually to a trusted account. Never add public admin registration.

## Payments
- Keep `RAZORPAY_SANDBOX=true`; use only test keys and test webhook secret.
- Configure the `payment.captured` webhook and test success, failure, duplicate webhook, invalid signature, wrong amount/currency, replay, and retries.
- Do not enable live credentials until reconciliation and refund operations are reviewed.

## Security and privacy
- Verify admin routes reject non-admin users and student routes reject access to other students' resources.
- Verify downloads are served from private storage through authorized controller routes.
- Enable HTTPS, secure cookies, CSRF protection, rate limits for login/registration/quiz start, and production error logging.
- Publish privacy policy, terms, refund policy, contact information, and data retention policy.
- Replace sample testimonials, prices, statistics, temporary teacher imagery, and placeholder links with approved content.

## Functional QA
- Use `php artisan migrate:fresh --seed` only against a disposable test database.
- Run `php artisan test`; add integration tests for checkout, webhook idempotency, enrolment, lessons, downloads, quizzes, certificates, profile updates, and admin authorization.
- Test mobile/tablet/desktop layouts, keyboard navigation, validation errors, and slow network states.
- Test email after configuring a real mail transport; database notifications alone do not send email.
- Test certificate completion eligibility and public verification route.
- Configure daily database backups and test restoration.

## Deployment
- Configure PHP/extensions, production Composer dependencies, DB, writable `storage` and `bootstrap/cache`, and HTTPS.
- Set `APP_ENV=production`, `APP_DEBUG=false`, strong unique secrets, and correct `APP_URL`.
- Deploy to staging first and record a rollback plan before production.
