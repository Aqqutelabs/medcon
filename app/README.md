# Medcon Edu — App module (Auth)

Setup:

1. Create the `farmyksn_medcon` database and import `app/migrations.sql` into it.
2. Update DB credentials in `app/includes/config.php`.
3. Ensure the `app/` folder is served by your webserver (e.g., http://localhost/app/).

Files added:
- includes/config.php, db.php, functions.php, auth.php
- signup.php, login.php, logout.php
- student/, admin/, agent/ dashboards
- assets/css/app.css, assets/js/app.js
- migrations.sql

Notes:
- CSRF tokens and prepared statements are used.
- Passwords stored using `password_hash()`.
- Application enquiries support draft saving, submission, status tracking, and admin review.
- Documents accept validated PDF/JPG/PNG uploads up to 5MB. Local files are stored under `/docs/students/{student}/applications/{application}` and are served through an authenticated download endpoint.
- Document persistence is isolated in `includes/document-storage.php`; replace that adapter when moving files to Supabase Storage.
- Existing installations must run `migrations/20260825_document_uploads.sql` once.
- Existing installations must then run `migrations/20260825_jamb_wassce.sql` to add the JAMB application field and migrate the former academic-result document type to WASSCE.
- Adjust paths and host configuration for your environment.
