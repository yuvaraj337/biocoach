# Jerin's Coaching — Laravel Website (Design V2)

This is the first implementation pass for the supplied Jerin's Coaching reference: cream/yellow hero, blue course cards, benefits, about, testimonials, learning steps, free resources, and navy footer. The supplied logo is stored at `public/images/jerins-coaching-logo.jpg`.

## Current implementation
- Responsive Blade homepage and shared navigation/footer.
- Course listing and course details templates.
- Course/category/order/voucher models and database migrations.
- Initial admin dashboard and course CRUD templates/controllers.
- Checkout creates a **pending test order only**. No live payment is taken and no order is marked paid from the browser.
- Temporary SVG illustrations are included where a real, approved teacher/course image has not yet been supplied.

## Important status
This is a source scaffold, not yet a runnable/deployed Laravel installation. It has not been tested end-to-end against a configured MySQL database. Authentication, authorization policies, payment sandbox verification/webhooks, email delivery, voucher application, and full order management still need implementation and testing. Placeholder contact details and testimonials must be replaced with verified content before publication.

## Setup
1. Install PHP 8.2+, Composer, and MySQL 8+.
2. Create a fresh Laravel 11/12 app: `composer create-project laravel/laravel jerins-coaching`.
3. Merge this package's `app`, `routes`, `database`, `resources`, and `public` folders into that app.
4. Install Laravel Breeze for authentication: `composer require laravel/breeze --dev`, then `php artisan breeze:install blade` and `npm install && npm run build`.
5. Configure `.env` from `.env.example`, create the database, and run `php artisan key:generate` and `php artisan migrate`.
6. Before any public deployment, add and test admin-role middleware/policies. An `auth` check alone does not protect admin routes.
7. Keep `PAYMENT_MODE=test`; connect only provider sandbox credentials and verify payment signatures/webhooks server-side before granting course access.

## Design matching
The Blade/CSS is based on the user's reference screenshots. Exact pixel parity requires the actual final image assets, fonts, course copy, contact details, and browser/device comparison; the included SVGs are placeholders.


## Homepage design pass
The homepage hero layout has been refined against the supplied reference screenshot. The cropped hero visual is taken from the design mockup and is only a temporary visual placeholder, not a verified photograph of Dr. Tanjina Jerin. Replace `public/images/hero-reference-crop.png` with an approved founder/teacher image before publishing.


## Course-card design pass (V4)
Added four custom SVG biology thumbnails (DNA, cell biology, plant biology, and human physiology), course ribbons, structured lesson/material metadata, and responsive card styling. When the database has published courses, the cards use those course records; otherwise the homepage displays clearly marked demo cards for layout preview. Demo prices are placeholders and must be replaced before public launch.


## Why Choose Us section design pass (V5)
Refined the six benefit cards with subtle botanical decorations, softer pastel icon circles, centered spacing, hover elevation, and consistent typography. Benefit copy avoids unverified outcome statistics.


## About section design pass (V6)
Refined the About section into a three-column founder layout: photo area, founder-led introduction and mission quote card. Added gentle borders, shadows, typography and responsive spacing. The proposed founder quote should be approved by Dr. Tanjina Jerin before publication.


## Testimonials section design pass (V7)
Polished the testimonials section with a softer pink gradient, three responsive review cards, improved avatar framing, star styling, gentle hover movement, and placeholder labels. These are sample layout cards only—not verified student reviews or ratings. Replace them with approved feedback before public launch.


## How It Works section design pass (V8)
Updated the four step cards with numbered colored circles, light-blue card borders, subtle shadows, hover elevation and desktop connector arrows. The cards remain responsive, and connector arrows are hidden on narrow screens to keep the layout clean.


## Free resources and footer design pass (V9)
Polished the four resource cards with pastel icon tiles, hover elevation, clearer spacing and responsive styling. Refined the footer background and link hover states. Newsletter submission remains a visual placeholder until a backend subscription endpoint and email provider are configured.


## V11 — Database-driven course management
- Public course catalogue and detail pages now use the CourseController routes and published database records.
- Added protected admin course CRUD: create, edit, publish/unpublish, list, and delete courses (deletion is blocked when orders exist).
- Prices are entered in rupees and stored as integer paise to avoid floating-point currency storage.
- Course form includes category, title, slug, summary, description, thumbnail path/URL and SEO fields.
- Admin routes apply the EnsureUserIsAdmin middleware directly, avoiding reliance on a missing middleware alias in this scaffold.
- Before use: install Laravel 11/12, configure database `.env`, ensure the standard Laravel users migration exists, run `php artisan migrate`, and grant admin status only through a trusted database operation. This archive is a source scaffold; a full live database/browser test has not been performed.


## V12 — Orders and student enrolment foundation
- Added orders and enrollments models and migrations.
- Authenticated students can create a pending order for a published course, view their order history, and view active course enrollments.
- The checkout page explicitly displays pending status. No payment is marked successful by the browser and no gateway integration is claimed.
- Payment gateway sandbox credentials, signature verification/webhook handling, idempotency, and verified enrollment activation are still required before paid course access can be enabled.
- Full Laravel/MySQL integration tests remain outstanding; run migrations and test routes on the target Laravel version before deployment.


## V13 — Razorpay sandbox payment integration scaffold
- Added Razorpay order creation, hosted checkout widget, checkout signature verification, payment-attempt tracking, and signed `payment.captured` webhook processing.
- Payment status remains pending after browser checkout; only a valid webhook with matching amount/currency marks an order paid and creates an enrollment.
- Set `RAZORPAY_KEY_ID`, `RAZORPAY_KEY_SECRET`, `RAZORPAY_WEBHOOK_SECRET`, and `RAZORPAY_SANDBOX=true` in `.env`. Use test credentials only.
- The webhook endpoint is excluded from CSRF checks in the Laravel 11/12 bootstrap scaffold and must be protected by the provider HMAC signature.
- Still required before real use: install dependencies, configure provider test dashboard webhook, run full end-to-end tests (success, failure, replay, duplicate IDs, amount mismatch and retries). This archive has not been tested against a live Laravel/MySQL instance. Do not enable live payments.


## V14 — Admin orders and payment review
- Added protected admin order list with search, payment-status filter, counts and recorded paid-order total.
- Added order detail page showing student/course data and payment attempts.
- Added reason-required actions to mark unpaid orders failed after provider review or revoke enrolment access. The panel cannot manually mark an order paid.
- Before production, persist administrator ID, reason, timestamp and before/after values in a dedicated audit table. Full Laravel/MySQL runtime testing is still required.


## V15 — Student management and audit trail
- Added student search/list/detail pages with course enrolments and order history.
- Admins can activate or revoke an enrolment with a required reason.
- Added an admin audit-log table and filtered log viewer with actor, timestamp, action, subject, reason, before/after JSON and IP address.
- Order failure and enrolment-revocation actions now write audit records; enrolment status changes from student management are also logged.
- Admin access remains protected by the admin middleware.
- Full runtime testing, database migration execution, and payment sandbox end-to-end testing are still required. Review data retention and IP logging practices for your jurisdiction before production.


## V25 — Consolidated release candidate
- Added profile update and password-change flow, certificate issuance after completing all published lessons, printable certificate view, and public certificate verification.
- Added `docs/LAUNCH_CHECKLIST.md` with staging, security, payment sandbox, QA, backup, and deployment requirements.
- Removed the duplicate `create_orders_table` migration that would otherwise collide on a fresh migration run; retained the later richer orders schema.
- **Not production-ready yet:** this package still needs to be merged into a fresh Laravel 11/12 installation, environment configured, migrations executed against a disposable MySQL database, and end-to-end tests run. Email delivery and production hosting are not configured. Keep Razorpay in test mode.
