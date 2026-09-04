# Medcon Edu App Design System

**Product:** Medcon Edu Application Platform  
**Scope:** Authenticated application areas only  
**Applies to:** Student Dashboard, Admin Dashboard, Agent Dashboard and future authenticated portals  
**Does NOT apply to:** Public website, landing pages, marketing pages, college pages, blog, public admissions content, or other unauthenticated website components.

> **IMPORTANT SCOPE RULE:** This design system is intentionally separate from the Medcon Edu public website design system. App-specific changes to radius, shadows, glass effects, forms, navigation, cards, spacing or component styling must never cascade into or modify public website components.

---

## 1. Design Direction

The Medcon Edu application should retain the institutional credibility and brand identity of the public website while moving toward a **softer, modern dashboard interface**.

The public website is intentionally structured, academic and relatively rigid. The authenticated application should feel more personal, operational and comfortable for prolonged use.

The target visual balance is:

**Institutional structure + modern dashboard + restrained glassmorphism + subtle skeuomorphic depth.**

The application should feel:

- Professional
- Calm
- Spacious
- Trustworthy
- Modern
- Tactile
- Easy to navigate
- Appropriate for handling important applications and documents

Do not turn the application into a generic SaaS dashboard.

Medcon's academic identity must remain visible.

---

# 2. Relationship With the Public Design System

The application inherits the **brand**, not the entire public UI language.

The existing public design system establishes Medcon Edu's primary colors as `#16055D`, `#2559ED`, and `#00BBA0`.

### Public Website

More:

- Rectangular
- Institutional
- Content-heavy
- Academic
- Structured
- Sharp
- University-inspired

### Application

More:

- Rounded
- Spacious
- Interactive
- Layered
- Dashboard-oriented
- Soft
- Tactile
- Personalized

The distinction should be noticeable without making the two products feel like separate brands.

---

# 3. App-Only CSS Boundary

All application styles must exist within an app-specific stylesheet or root namespace.

Recommended:

```text
/assets/css/
    website.css
    app.css
```

or:

```html
<body class="medcon-app">
```

App components should be scoped beneath:

```css
.medcon-app {}
```

Never globally redefine:

```css
button {}
input {}
.card {}
table {}
```

for app-specific styling.

Instead use:

```css
.medcon-app .app-button {}
.medcon-app .app-input {}
.medcon-app .app-card {}
.medcon-app .app-table {}
```

This rule is mandatory.

---

# 4. Core Brand Palette

Use the same Medcon Edu brand colors.

| Role | Color | Usage |
|---|---|---|
| Institutional Indigo | `#16055D` | Navigation, important headings, high-authority elements |
| Primary Blue | `#2559ED` | Main actions, active states, links, progress |
| Medical Teal | `#00BBA0` | Success, completion, verification, support |
| Mist | `#F7FFFE` | Soft surfaces |
| Orange | `#FC8C08` | Warnings and attention states only |
| Error | `#F04438` | Errors, failed actions, rejected states |

The app should use **considerably more white and neutral space** than the public site.

---

# 5. App Surface Palette

Introduce application-specific surfaces.

```css
:root {
    --app-indigo: #16055D;
    --app-blue: #2559ED;
    --app-teal: #00BBA0;

    --app-bg: #F6F8FC;
    --app-surface: #FFFFFF;
    --app-surface-soft: #F9FAFC;
    --app-surface-blue: #F5F7FF;
    --app-surface-teal: #F2FCFA;

    --app-text: #101828;
    --app-text-secondary: #475467;
    --app-text-muted: #667085;

    --app-border: #EAECF0;
    --app-border-strong: #D0D5DD;
}
```

The overall application background should be a very light cool gray rather than pure white.

Cards then sit on top as white surfaces.

This small difference creates visual depth without requiring excessive shadows.

---

# 6. Color Distribution

Approximate dashboard distribution:

| Role | Share |
|---|---:|
| White / near-white | 75–80% |
| Light neutral surfaces | 10–15% |
| Indigo | 4–6% |
| Blue | 3–5% |
| Teal | 1–3% |
| Orange / red | <1% |

Large areas of saturated blue, teal or indigo should be uncommon.

Color should communicate hierarchy and state rather than decorate every component.

---

# 7. Glassmorphism

Use **restrained glass effects**.

Glass should provide hierarchy rather than become the dominant aesthetic.

Good applications:

- Top navigation
- Floating contextual panels
- Dropdown menus
- Modal dialogs
- Notification panel
- User menu
- Selected dashboard highlight cards
- Floating action areas

Example:

```css
.app-glass {
    background: rgba(255, 255, 255, 0.78);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);

    border: 1px solid rgba(255, 255, 255, 0.75);

    box-shadow:
        0 8px 32px rgba(22, 5, 93, 0.06);
}
```

Do not make every card transparent.

Most application surfaces should remain solid white.

---

# 8. Skeuomorphic Depth

Use subtle skeuomorphic cues to make interactive elements feel tactile.

This does **not** mean recreating old iOS skeuomorphism.

Use:

- Gentle inner highlights
- Small shadows
- Slight elevation
- Layered surfaces
- Tactile buttons
- Recessed upload areas
- Physical-looking switches
- Progress indicators with depth

Example:

```css
.app-card {
    background: #FFFFFF;

    box-shadow:
        0 1px 2px rgba(16, 24, 40, 0.03),
        0 8px 24px rgba(16, 24, 40, 0.04);
}
```

Depth should be barely noticeable.

---

# 9. Border Radius System

The application should be noticeably softer than the website.

```css
--app-radius-xs: 6px;
--app-radius-sm: 8px;
--app-radius-md: 12px;
--app-radius-lg: 16px;
--app-radius-xl: 20px;
--app-radius-pill: 999px;
```

### Recommended Usage

| Component | Radius |
|---|---:|
| Small badges | Pill |
| Inputs | 10–12px |
| Buttons | 10–12px |
| Table container | 12–16px |
| Standard card | 14–16px |
| Metric card | 16px |
| Modal | 18–20px |
| Large dashboard panel | 16–20px |

Do not use extreme rounding everywhere.

The application should still contain some straighter structural lines.

---

# 10. Preserve Rigid Structure

Softness should come primarily from:

- Radius
- Spacing
- Shadows
- Surface layering
- Interaction
- Typography
- Subtle gradients

Not from abandoning structure.

Large operational elements such as:

- Tables
- Application timelines
- Document lists
- Data grids
- Sidebar groups
- Form sections
- Record detail layouts

should retain strong alignment and clear rectangular geometry.

Think:

**Rigid grid, soft surfaces.**

---

# 11. Typography

Continue using:

**Inter / Source Sans 3 / system-ui**

The application should use slightly smaller headings than marketing pages.

```text
Dashboard H1       30px / 38px / 700
Page H1            26px / 34px / 700
Section H2         20px / 28px / 700
Card Heading       16px / 24px / 600
Body               15px / 24px / 400
Small              14px / 20px / 400
Metadata           13px / 18px / 500
Micro              12px / 16px / 600
```

Use whitespace rather than oversized typography to establish hierarchy.

---

# 12. Dashboard Shell

Desktop application layout:

```text
┌─────────────┬──────────────────────────────────────────┐
│             │              TOP BAR                     │
│             ├──────────────────────────────────────────┤
│             │                                          │
│   SIDEBAR   │              PAGE                        │
│             │                                          │
│             │              CONTENT                     │
│             │                                          │
└─────────────┴──────────────────────────────────────────┘
```

Recommended:

```css
--sidebar-width: 250px;
--topbar-height: 72px;
--app-content-max: 1440px;
```

Do not constrain dashboard content to the narrower public website container.

---

# 13. Sidebar

The sidebar provides the strongest institutional anchor.

Prefer:

- White or extremely pale surface
- Medcon logo at top
- Indigo text
- Soft active navigation state
- Small icons
- Clear grouping
- User/account area toward bottom

Example student navigation:

```text
Overview

My Application
Documents
Admission
Visa

Messages
Payments

Profile
Support
```

Admin navigation may include:

```text
Overview

Applications
Students
Documents
Colleges
Agents

Communications
Payments
Reports

Users
Settings
```

---

# 14. Sidebar Active State

Do not use a giant solid-blue navigation block.

Use something softer:

```css
.app-nav-item.active {
    background: rgba(37, 89, 237, 0.08);
    color: #2559ED;
    border-radius: 10px;
}
```

A small blue indicator may appear on the left.

Icons can become blue while inactive icons remain neutral.

---

# 15. Top Bar

The top utility bar should feel lightweight.

Include where appropriate:

- Search
- Notifications
- Help
- User avatar
- User name
- Role
- Dropdown

Use whitespace rather than heavy borders to separate it from the page.

A subtle glass treatment is permitted.

---

# 16. Page Header

Standard authenticated page:

```text
Applications

Review and manage student applications.

                                    + New Application
```

Page headers should contain:

- Breadcrumb where useful
- H1
- Short supporting text
- Primary contextual action

Do not use large marketing hero sections inside the app.

---

# 17. Cards

Cards become softer than public website cards.

```css
.app-card {
    background: #FFFFFF;
    border: 1px solid rgba(208, 213, 221, 0.65);
    border-radius: 16px;
    padding: 24px;

    box-shadow:
        0 2px 4px rgba(16, 24, 40, 0.02),
        0 8px 24px rgba(16, 24, 40, 0.035);
}
```

Cards should feel like surfaces placed gently on the dashboard background.

---

# 18. Metric Cards

Metric cards may use slightly stronger visual styling.

Example:

```text
TOTAL APPLICATIONS

128

+14 this month
```

Other examples:

- Application Progress
- Documents Uploaded
- Admission Status
- Visa Status
- Outstanding Actions
- New Applications
- Pending Reviews
- Active Students

Use icon containers, not giant decorative icons.

---

# 19. Featured Gradient Card

One or occasionally two dashboard cards may use a stronger branded treatment.

Recommended:

```css
background:
    linear-gradient(
        135deg,
        #2559ED 0%,
        #5579F3 45%,
        #65D8D0 130%
    );
```

This can be used for:

- Overall application progress
- Current admission status
- Important dashboard summary

Do not create an entire grid of gradient cards.

---

# 20. Forms

Forms should be substantially softer than the public website.

This is especially important because students will spend significant time completing applications.

```css
.app-input {
    width: 100%;
    min-height: 48px;

    background: #FFFFFF;

    border: 1px solid #D0D5DD;
    border-radius: 11px;

    padding: 11px 14px;

    color: #101828;

    transition:
        border-color 150ms ease,
        box-shadow 150ms ease,
        background 150ms ease;
}
```

Focus:

```css
.app-input:focus {
    border-color: #2559ED;

    box-shadow:
        0 0 0 4px rgba(37, 89, 237, 0.10);

    outline: none;
}
```

---

# 21. Form Layout

Use generous whitespace.

Desktop:

```text
Personal Information

First Name                  Last Name
[                         ] [                         ]

Email                       Phone
[                         ] [                         ]


Education Information

School
[                                                     ]
```

Recommended:

```css
.form-section {
    margin-bottom: 40px;
}

.form-grid {
    gap: 20px 24px;
}
```

Avoid creating one giant uninterrupted form.

---

# 22. Form Sections

Long application forms should be divided into logical sections:

1. Personal Information
2. Contact Information
3. Parent / Guardian
4. Education
5. Programme Selection
6. Passport Information
7. Documents
8. Financial / Sponsorship Information
9. Declaration
10. Review & Submit

Each section should feel like a distinct task.

---

# 23. Multi-Step Application

Long applications should use a persistent progress system.

Example:

```text
Application

● Personal
● Education
● Programme
○ Documents
○ Review
```

Or:

```text
Application progress                         60%

██████████████████░░░░░░░
```

Blue represents active progress.

Teal represents completed steps.

---

# 24. Selects and Dropdowns

Dropdowns should follow input styling.

Menus should use:

- White surface
- 12–14px radius
- Soft shadow
- Clear selected state
- Generous item height
- Optional search for large datasets

Avoid browser-default styling where practical.

---

# 25. Checkboxes and Radio Controls

Controls should feel tactile.

Selected:

- Blue fill
- White check
- Soft blue focus ring

Completed/verified states may use teal.

Do not replace familiar controls with unconventional UI.

---

# 26. Buttons

App buttons should be softer than website buttons.

### Primary

```css
.app-btn-primary {
    min-height: 44px;

    padding: 0 18px;

    background: #2559ED;
    color: #FFFFFF;

    border: 1px solid #2559ED;
    border-radius: 10px;

    box-shadow:
        0 2px 4px rgba(37, 89, 237, 0.15);
}
```

### Secondary

White surface with subtle border.

### Tertiary

Text/icon button.

### Destructive

Red only for genuinely destructive actions.

---

# 27. Button Interaction

Buttons may use slight physical movement.

```css
.app-button:active {
    transform: translateY(1px);
}
```

Hover elevation should remain subtle.

Avoid excessive animation.

---

# 28. Tables

Tables remain one of the more rigid components.

They should preserve strong alignment while living inside softer containers.

```css
.app-table-container {
    background: #FFFFFF;

    border: 1px solid #EAECF0;
    border-radius: 16px;

    overflow: hidden;
}
```

Unlike the public website, avoid solid indigo table headers by default.

Prefer:

```css
.app-table th {
    background: #F9FAFB;
    color: #475467;
}
```

Use indigo or blue for important labels and interactions.

---

# 29. Table Rows

Rows should have:

- More vertical breathing room
- Soft hover state
- Clear status badges
- Compact contextual actions

Example hover:

```css
.app-table tbody tr:hover {
    background: #F8FAFF;
}
```

---

# 30. Status Badges

Use pill badges inside the application.

Examples:

```text
● Draft
● Submitted
● Under Review
● Documents Required
● Approved
● Visa Processing
● Enrolled
```

Suggested semantics:

| Status | Treatment |
|---|---|
| Draft | Gray |
| Submitted | Blue |
| Received | Blue/Teal |
| Under Review | Orange |
| Documents Required | Orange |
| Approved | Teal |
| Admission Issued | Teal |
| Visa Processing | Blue |
| Rejected | Red |
| Enrolled | Teal |

Badges should use pale backgrounds rather than fully saturated pills.

---

# 31. Application Status

Application status is one of the most important student-facing components.

Use a visual journey:

```text
Application
    ✓

Documents
    ✓

College Review
    ●

Admission
    ○

Visa
    ○

Enrollment
    ○
```

Completed = teal.

Current = blue.

Future = neutral gray.

Problems = orange/red.

---

# 32. Document Upload

Document upload areas should feel slightly recessed.

```css
.document-dropzone {
    background: #FAFBFC;

    border: 1.5px dashed #D0D5DD;
    border-radius: 14px;

    padding: 32px;
}
```

Hover/drag:

```css
background: #F5F7FF;
border-color: #2559ED;
```

Show uploaded documents as individual records rather than leaving them inside the dropzone.

---

# 33. Document Record

Example:

```text
┌─────────────────────────────────────────────┐
│ PDF   WAEC Certificate.pdf                  │
│       1.8 MB                                │
│                                             │
│       ✓ Verified                    ⋮       │
└─────────────────────────────────────────────┘
```

Allow:

- Preview
- Replace
- Download where permitted
- Delete where permitted
- Verification state

---

# 34. Empty States

Empty states should be calm and useful.

Example:

```text
No documents uploaded yet

Upload your passport and academic documents
to continue your application.

[ Upload document ]
```

Use minimal illustration/iconography.

Do not use childish cartoons.

---

# 35. Notifications

Notifications should appear as:

- Small top-bar indicator
- Notification drawer
- Optional toast for immediate feedback
- Persistent alert card when action is required

Use severity appropriately.

Do not show every system event as a notification.

---

# 36. Alert Cards

Alerts may use lightly tinted surfaces.

### Information

Pale blue.

### Success

Pale teal.

### Warning

Pale orange.

### Error

Pale red.

Avoid saturated full-width alert bars except for serious system issues.

---

# 37. Modals

Modals should use the strongest glass/surface depth in the application.

```css
.app-modal {
    background: rgba(255,255,255,0.94);

    backdrop-filter: blur(20px);

    border: 1px solid rgba(255,255,255,0.8);
    border-radius: 20px;

    box-shadow:
        0 24px 80px rgba(16,24,40,0.16);
}
```

Use modals for focused actions, not full workflows.

---

# 38. Search

Search inputs should be softer and may use a subtle tinted background.

Example:

```text
⌕  Search applications...
```

Use rounded 10–12px containers.

Search should not visually compete with primary actions.

---

# 39. Filters

Filters should appear as compact controls above data.

Example:

```text
[ Search students... ]   [ Status ▾ ] [ College ▾ ] [ Intake ▾ ]
```

Active filters may become removable pills.

---

# 40. User Profile

User profile surfaces may use slightly more personality.

Include:

- Avatar
- Full name
- Role
- Contact information
- Application ID where relevant
- Profile completion
- Account settings

Avoid turning profile pages into social-network profiles.

---

# 41. Student Dashboard

The student dashboard should answer immediately:

1. Where is my application?
2. What do I need to do next?
3. Is anything missing?
4. Have I received admission?
5. Are there new messages?
6. What deadlines matter?

Suggested hierarchy:

```text
Good morning, Student

Your Application
[ Main progress/status card ]

Next Action
[ Upload Passport ]

Application Progress
[ Timeline ]

Documents                Messages
[ 8/10 Complete ]         [ 2 New ]

Admission / Visa status

Recent Activity
```

---

# 42. Admin Dashboard

The admin dashboard should prioritize operations.

Suggested top-level metrics:

- New Applications
- Pending Review
- Documents Pending
- Approved
- Admissions Issued
- Visa Processing

Then:

```text
Applications requiring attention

Recent applications

Application pipeline

Recent activity
```

Admin dashboards may be denser than student dashboards.

---

# 43. Agent Dashboard

Agent dashboards should emphasize:

- Students referred
- Applications
- Application status
- Outstanding documents
- Admissions
- Commission status
- Messages

The agent should quickly understand which students require intervention.

---

# 44. Whitespace

Whitespace is a major part of the application aesthetic.

Recommended page padding:

```css
Desktop: 32–40px
Tablet: 24px
Mobile: 16–20px
```

Card padding:

```css
Standard: 24px
Large: 28–32px
Compact: 16–20px
```

Avoid cramming information simply because dashboard space is available.

---

# 45. Iconography

Use one consistent modern icon family.

Recommended style:

- Simple outline
- Rounded line endings
- 1.5–2px stroke
- Minimal detail

Icons should primarily help scanning.

Do not use icons as decoration everywhere.

---

# 46. Imagery

Photography should be rare inside operational screens.

Appropriate locations:

- Welcome/dashboard banner
- Empty states where useful
- Student profile
- College selection
- Admission milestone

Do not place stock photographs inside every dashboard card.

---

# 47. Motion

Motion should be restrained.

Recommended:

```css
--app-transition-fast: 120ms;
--app-transition: 180ms;
--app-transition-slow: 260ms;
```

Use for:

- Hover
- Focus
- Dropdowns
- Modal entry
- Sidebar state
- Progress updates
- Toasts

Avoid decorative continuous animation.

---

# 48. Responsive Behaviour

### Desktop

Persistent sidebar.

Wide content area.

Multi-column metrics.

Full tables.

### Tablet

Collapsible sidebar.

2-column cards.

Reduced page padding.

Tables may horizontally scroll where unavoidable.

### Mobile

Sidebar becomes drawer.

Cards stack.

Forms become single-column.

Tables become responsive records/cards where appropriate.

Top bar simplifies.

Primary actions remain easy to reach.

---

# 49. Mobile Forms

Mobile inputs should remain at least:

```css
min-height: 48px;
```

Avoid placing two important text fields beside each other on narrow screens.

Long applications should preserve progress when users leave and return.

---

# 50. Accessibility

Maintain WCAG AA expectations.

Requirements:

- Visible labels
- Keyboard navigation
- Clear focus states
- Semantic form groups
- Accessible modals
- Accessible dropdowns
- Minimum 44px targets
- Sufficient contrast
- Status never communicated by color alone
- Proper table headers
- Screen-reader-friendly validation

---

# 51. App Component Library

Build app-specific reusable components:

```text
AppShell
AppSidebar
AppTopbar
AppPageHeader

AppCard
MetricCard
FeaturedMetricCard

AppButton
AppInput
AppSelect
AppTextarea
AppCheckbox
AppRadio
AppDatePicker

FormSection
FormProgress
ApplicationStepper

AppTable
TableFilters
Pagination

StatusBadge
ApplicationStatus
ProgressBar

DocumentUploader
DocumentCard

NotificationDrawer
Alert
Toast

AppModal
Dropdown

EmptyState
LoadingState
Skeleton

UserMenu
ProfileCard
```

These should be separate from public website components where their visual behaviour differs.

---

# 52. Design Token Separation

Recommended architecture:

```css
/* Global brand values */
--medcon-indigo: #16055D;
--medcon-blue: #2559ED;
--medcon-teal: #00BBA0;

/* Website-only */
--website-card-radius: 0px;
--website-button-radius: 0px;

/* App-only */
--app-card-radius: 16px;
--app-button-radius: 10px;
--app-input-radius: 11px;
```

Brand values can be shared.

Component styling cannot.

---

# 53. Do Not

Do not:

- Apply app radius globally.
- Restyle public website forms.
- Convert the public website into glassmorphism.
- Turn every dashboard card into glass.
- Use gradients everywhere.
- Use giant SaaS metrics.
- Use excessive shadows.
- Make everything pill-shaped.
- Remove tables simply to appear modern.
- Overuse photographs.
- Use playful illustrations.
- Use neon colors.
- Sacrifice information density for aesthetics.
- Allow dashboard styles to leak into landing pages.

---

# 54. Visual Formula

The Medcon Edu application should approximately combine:

**40% structured institutional dashboard**

Strong grids, tables, alignment, navigation and clear information hierarchy.

**30% modern soft UI**

Rounded controls, generous spacing, light surfaces and restrained shadows.

**20% tactile / skeuomorphic depth**

Subtle elevation, recessed upload areas, physical interaction cues.

**10% glass**

Navigation overlays, menus, modals and selected premium surfaces.

Glassmorphism should therefore be an **accent**, not the entire design language.

---

# 55. Reference Feeling

The intended visual direction is similar to a premium contemporary dashboard:

- Light cool-gray application canvas
- Large white working area
- Soft sidebar
- Rounded dashboard modules
- Spacious forms
- Clear metrics
- Restrained gradient highlights
- Floating/glass utility elements
- Strong alignment underneath the softer surfaces

The reference dashboard image should influence **softness, spacing, hierarchy and surface treatment**, not be copied literally.

Medcon Edu must retain its own institutional identity.

---

# 56. Implementation Rule for Codex / Developers

When implementing authenticated application pages:

> **Use this App Design System as the authoritative UI specification. Do not use the public website component styling for authenticated dashboard UI except for shared brand tokens, typography and accessibility conventions.**

Before modifying a component, determine whether it belongs to:

```text
PUBLIC WEBSITE
or
AUTHENTICATED APP
```

If authenticated:

Use `app-*` components and app design tokens.

If public:

Use the existing website design system.

Never modify public component styling merely to make an app screen match this document.

---

# 57. Acceptance Criteria

The app design is correct when:

- It unmistakably belongs to Medcon Edu.
- It uses the existing indigo, blue and teal identity.
- It feels softer than the public website.
- Forms are rounded, spacious and comfortable.
- Dashboard surfaces use restrained depth.
- Glass effects appear selectively.
- Cards have modest rounding rather than extreme curves.
- Tables remain structured and operational.
- White space is generous.
- Important information is immediately scannable.
- Student workflows feel simpler than the underlying process.
- Admin workflows can remain information-dense.
- Mobile layouts remain usable.
- The UI feels contemporary without becoming generic SaaS.
- Public website styling remains completely unaffected.

---

# 58. Summary

The **Medcon Edu App** is the softer operational counterpart to the institutional public website.

Keep the existing brand foundation:

**Indigo `#16055D` + Blue `#2559ED` + Teal `#00BBA0`.**

Then introduce:

**more white space + softer corners + subtle shadows + restrained glass + tactile depth + modern dashboard patterns.**

The central design principle is:

> **Rigid grid. Soft surfaces. Institutional trust. Modern interaction.**

This design system applies **only after the user enters the authenticated Medcon Edu application environment** and must never automatically alter the public website or landing-page design system.