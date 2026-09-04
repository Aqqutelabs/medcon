# Medcon Phase 1 notifications

## Current workflow audit

The application uses PHP, PDO/MySQL, session authentication, and four roles: `student`, `agent`, `admin`, and `super_admin`. Account state is stored in `users.status`; application state is stored in `applications.status`; document review state is stored in `documents.status`; and agent approval state is stored in `agents.approval_status`.

The site already has database-backed, one-way dashboard notifications in `messages`. It did not have a complete outbound email service before this phase. The pre-existing SendByte webhook is outside Phase 1 and remains unused by this implementation.

### Forms and workflows

| Surface | Persistence | Phase 1 email behaviour |
|---|---|---|
| Student sign-up (`app/signup.php`) | Creates `users` + `students` | Account-created verification email; affiliate email when a valid agent is attached |
| Agent registration (`for-agents.php`) | Creates `users` + `agents` | Account-created verification email |
| Agent registers student (`app/agent/submit-student.php`) | Creates `users` + `students` | Student account-created verification email and affiliate new-student email; passwords are never emailed |
| Forgot password (`app/forgot-password.php`) | Previously WhatsApp-only | Generic response and password-reset email for existing active accounts |
| Student application (`app/student/application.php`) | Draft/submitted application | No email for drafts; completion email is sent once documents make the application `received` |
| Student documents (`app/student/documents.php`) | Uploads and moves a complete submission to `received` | `application.completed`, once per application |
| Admin application status (`app/admin/application.php`) | Changes application status | Status email only when the value changes; admission and visa statuses use their specific templates |
| Admin document review (`app/admin/application.php`) | Changes document status | Action-required email for rejected/resubmission-required documents |
| Student referral profile (`app/student/profile.php`) | May attach an approved agent | Affiliate email only when the stored relationship changes from no agent to an agent |
| Eligibility checker (`eligibility-checker.php`) | Writes the legacy CSV and a durable `eligibility_assessments` record | Emails the result; new recipients receive a one-time verified account-completion link while existing users are directed to sign in |
| Home, Apply, Contact inquiry forms | Shared `inquiries` table | Ordinary submission sends `inquiry.received`; WhatsApp continuation saves without email |
| Fee configurator | Browser-only calculator | No email; sharing and PDF are client-side |
| Payment/commission actions | Database-backed | Explicitly excluded from Phase 1 |

## Architecture

- `scripts/mail/config.php`: resolves private configuration in this order: `MEDCON_SENDBYTE_CONFIG`, application config value, safe sibling private directory. It never prints secrets.
- `scripts/mail/MailTransport.php`: SendByte HTTP adapter. It accepts the configured `sk_test_` or `sk_live_` credential and rejects missing or unrecognized credentials.
- `scripts/mail/TemplateRenderer.php`: shared escaped HTML shell and plain-language transactional templates.
- `scripts/mail/NotificationService.php`: event API, recipient lookup, deduplication, send attempt logging, and non-fatal error handling.
- `scripts/mail/TokenService.php`: cryptographically random, single-use, expiring email-verification and password-reset tokens. Only SHA-256 token hashes are stored.
- `email_logs`: durable audit and idempotency ledger. It stores event/recipient/entity/status/provider ID and a sanitized error; it never stores an API key, reset token, verification token, or password.

## Events

| Event key | Recipient | Trigger | Idempotency key |
|---|---|---|---|
| `account.created` | New student/agent | Account transaction committed or deliberate resend | `account.created:user:{user_id}:verification:{token_id}` |
| `account.verified` | User | Verification token consumed | `account.verified:user:{user_id}` |
| `password.reset_requested` | Existing user | Valid forgot-password request | Random request ID; rate limited |
| `password.reset_completed` | User | Password changed | `password.reset_completed:reset:{reset_id}` |
| `application.completed` | Student | Application first reaches `received` after required documents | `application.completed:application:{application_id}` |
| `application.status_changed` | Student | Admin changes status | `application.status_changed:application:{id}:{old}:{new}` |
| `application.document_action_required` | Student | Document rejected or requires resubmission | `application.document_action_required:document:{id}:{status}:{updated_at}` |
| `application.admission_update` | Student | Status changes to `admission_issued` | `application.admission_update:application:{id}:admission_issued` |
| `application.visa_update` | Student | Status changes to `visa_processing` | `application.visa_update:application:{id}:visa_processing` |
| `affiliate.new_student` | Agent | New durable student-agent relationship | `affiliate.new_student:agent:{agent_id}:student:{student_id}` |
| `inquiry.received` | Public inquirer | Normal inquiry form submission | `inquiry.received:inquiry:{inquiry_id}` |
| `eligibility.result` | Eligibility applicant | Assessment saved successfully | `eligibility.result:assessment:{assessment_id}` |

## Security and operational rules

- The configured SendByte credential may be a test or live key. A live key delivers real transactional email, while missing or unrecognized credentials fail safely.
- Registration/application success does not depend on email delivery. Email is attempted after the database transaction commits and failure is logged.
- Verification and reset links use raw URL-safe tokens only in the email URL. The database stores hashes; tokens expire, are single-use, and are invalidated when superseded or consumed.
- Eligibility account links follow the same rule: the raw token appears only in the email, its SHA-256 hash is stored, it expires after 24 hours, and successful use creates an email-verified student account.
- Forgot-password always shows the same response, whether or not the address exists.
- Resend/reset requests are limited per account and IP/time window.
- No password is ever included in an email or `email_logs`.
- Provider errors are reduced to a status/category and truncated sanitized message. API keys and request authorization headers are never logged.

## Deferred work

Queues, retries/cron, delivery webhooks, marketing/bulk mail, payment emails, administrator mail dashboards, analytics, and customer inquiry emails are explicitly deferred.
