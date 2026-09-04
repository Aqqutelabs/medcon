**# Medcon Edu Website Design System**

**\*\*Brand:\*\*** MedCon / Medcon Educational Services and Consultancy Limited  
**\*\*Domain:\*\*** medconedu.com  
**\*\*Design Reference:\*\*** UK medical school / university department website style  
**\*\*Primary Use:\*\*** Public admissions website now, student/admin/agent platform later

**---**

**## 1. Design Direction**

MedCon should use a **\*\*UK medical school inspired design system\*\***: structured, academic, information-rich, image-led, and highly credible. The site should feel closer to a university medical school or health sciences department than a generic study-abroad agency.

The design should communicate:

\- Academic credibility
\- Medical education authority
\- Clear admissions guidance
\- Parent trust
\- International student support
\- Professional institutional structure

The interface should use strong navigation, page breadcrumbs, clear content blocks, programme cards, tabbed sections, accordions, factual copy, and a dense institutional footer.

Avoid overly decorative landing-page styling, exaggerated gradients, playful illustrations, and generic SaaS-style cards.

**---**

**## 2. Visual Principles**

**### Institutional First**

The site should feel like an official education institution or medical admissions partner. Navigation, page titles, footer, and content hierarchy should be formal and structured.

**### Image-Led Education**

Use real or realistic academic/medical images: students in learning environments, anatomy models, labs, lectures, consultation settings, and campus-style scenes.

**### Dense but Clear**

Pages can contain a lot of information, but content must be broken into tabs, cards, accordions, tables, and clear section headings.

**### Indigo + Blue + Teal Identity**

Use \`#16055D\` as the institutional authority color, \`#2559ED\` as the primary digital/action color, and \`#00BBA0\` as the medical/support accent. These colors come directly from the logo and should anchor the entire site. Orange is retained only as a restrained utility/warning accent.

**### Practical Conversion**

The design should still convert users through Apply Now, Chat on WhatsApp, For Parents, and Agent Registration CTAs, but these actions should feel professional rather than sales-heavy.

**---**

**## 3. Brand Colors**

### Logo Color Analysis

The supplied Medcon Edu logo establishes three dominant brand colors:

| Role | Hex | RGB | Character |
|---|---:|---:|---|
| Primary Indigo | `#16055D` | `22, 5, 93` | Institutional, premium, authoritative |
| Secondary Blue | `#2559ED` | `37, 89, 237` | Digital, energetic, action-oriented |
| Primary Teal | `#00BBA0` | `0, 187, 160` | Medical, supportive, fresh |

The core site update is therefore a brand-color correction rather than a redesign: replace the previous dark green/legacy blue system with the logo indigo and blue, and replace the previous bright green with the logo teal. Layout, typography, spacing, and component structure should remain unchanged unless contrast requires a small adjustment.

The revised Medcon Edu color system is derived directly from the logo: deep indigo `#16055D`, vivid blue `#2559ED`, and teal `#00BBA0`. The site should remain mostly off-white, using indigo for institutional authority, vivid blue for actions, and teal for medical/support accents. Orange is optional and should appear only for rare warning or attention states.

**### Fresh Core Palette**

\| Token | Hex | RGB | Usage |
\|---|---:|---:|---|
\| \`--medcon-dark\` | \`#16055D\` | \`22, 5, 93\` | Primary logo indigo; headings, footer, dark hero sections, serious trust panels |
\| \`--medcon-blue\` | \`#2559ED\` | \`37, 89, 237\` | Secondary logo blue; primary CTA/action color, links, active nav states, form focus, motion highlights |
\| \`--medcon-green\` | \`#00BBA0\` | \`0, 187, 160\` | Primary logo teal; medical/support accent, verified badges, success states, WhatsApp, small highlights |
\| \`--medcon-mist\` | \`#F7FFFE\` | \`247, 255, 254\` | Main soft page background, light panels, calm medical education sections |
\| \`--medcon-orange\` | \`#FC8C08\` | \`252, 140, 8\` | Warm emphasis accent; limited callouts, warning markers, small icon highlights |

**### Recommended Color Ratio**

Use this ratio across the public website:

\| Color Role | Token | Recommended Share |
\|---|---|---:|
\| Soft background / white space | \`--medcon-mist\`, white | 70% |
\| Authority / dark structure | \`--medcon-dark\` | 20% |
\| Actions / links | \`--medcon-blue\` | 7% |
\| Medical/support accent | \`--medcon-green\` | 2% |
\| Warm attention accent | \`--medcon-orange\` | 1% |

**### Neutral Palette**

\| Token | Hex | Usage |
\|---|---:|---|
\| \`--gray-950\` | \`#101828\` | Main body text when primary indigo is too heavy |
\| \`--gray-800\` | \`#1D2939\` | Secondary headings on light backgrounds |
\| \`--gray-700\` | \`#344054\` | Secondary copy |
\| \`--gray-500\` | \`#667085\` | Muted labels and metadata |
\| \`--gray-300\` | \`#D0D5DD\` | Borders |
\| \`--gray-200\` | \`#EAECF0\` | Dividers |
\| \`--gray-100\` | \`#F2F4F7\` | Neutral page background |
\| \`--gray-50\` | \`#F9FAFB\` | Subtle card background |

**### Semantic Palette**

\| Token | Hex | Usage |
\|---|---:|---|
\| \`--success\` | \`#00BBA0\` | Approved, eligible, completed, verified |
\| \`--warning\` | \`#FC8C08\` | Pending, needs review, documents required |
\| \`--error\` | \`#F04438\` | Rejected, failed, invalid |
\| \`--info\` | \`#2559ED\` | Updates, links, neutral information |

**### Color Usage Rules**

\- Use \`#16055D\` for authority: header text, footer, dark hero sections, section headings, trust blocks, and premium institutional surfaces.
\- Use \`#2559ED\` as the main website action color: Apply Now buttons, links, active navigation, form focus states, and important interactive elements.
\- Use \`#00BBA0\` sparingly for medical trust and support: verified badges, WhatsApp, success states, eligibility indicators, and tiny icon details.
\- Use \`#FC8C08\` very sparingly for emphasis: small attention markers, warm callouts, highlighted numbers, or warning states.
\- Use \`#F7FFFE\` as the main soft background for calm, premium medical education pages.
\- Do not make the interface mostly teal; use teal as a supporting brand accent.
\- Do not use orange as a large background color.
\- Do not let indigo, blue, teal, and orange compete in the same section.
\- Avoid heavy gradients across entire sections; use gradients only for icon depth, motion accents, or small decorative highlights.

**### Recommended Pairings**

\| Use Case | Background | Text | Accent |
\|---|---|---|---|
\| Main page background | \`#F7FFFE\` or white | \`#16055D\` | \`#2559ED\` |
\| Dark hero | \`#16055D\` | white | \`#2559ED\` / \`#00BBA0\` |
\| Primary CTA | \`#2559ED\` | white | none |
\| WhatsApp CTA | \`#00BBA0\` | \`#16055D\` or white after contrast check | none |
\| Warning callout | Light orange tint | \`#16055D\` | \`#FC8C08\` |
\| Success/verified badge | Light teal tint | \`#16055D\` | \`#00BBA0\` |
\| Footer | \`#16055D\` | white | \`#2559ED\` / \`#00BBA0\` |

**### Gradient Guidance**

Use gradients only when they create subtle depth.

Recommended gradients:

\`\`\`css
\--gradient-hero: linear-gradient(135deg, *#16055D* 0%, *#201477* 55%, *#2559ED* 120%);
\--gradient-icon-blue: linear-gradient(135deg, *#16055D* 0%, *#2559ED* 100%);
\--gradient-medical: linear-gradient(135deg, *#2559ED* 0%, *#00BBA0* 100%);
\--gradient-warm-accent: linear-gradient(135deg, *#2559ED* 0%, #FC8C08 100%);
\`\`\`

Gradient rules:

\- Use \`--gradient-hero\` only for hero backgrounds or dark CTA sections.
\- Use \`--gradient-icon-blue\` for icon containers and logo-related depth.
\- Use \`--gradient-medical\` only for small highlights, not full-page sections.
\- Use \`--gradient-warm-accent\` rarely, mainly for decorative motion lines or small graphic accents.


**## 4. Typography**

**### Font Direction**

Use a clean academic sans-serif with strong readability.

Recommended:

\- **\*\*Primary:\*\*** Arial, Inter, or Source Sans 3
\- **\*\*Headings:\*\*** Inter or Source Sans 3
\- **\*\*Fallback:\*\*** system-ui, Arial, sans-serif

The reference style uses compact university-style text, so avoid oversized marketing typography except on the homepage hero.

**### Type Scale**

\| Style | Desktop | Mobile | Weight | Usage |
\|---|---:|---:|---:|---|
\| Homepage Display | 44px / 52px | 34px / 42px | 700 | Homepage hero only |
\| Page H1 | 34px / 42px | 28px / 36px | 700 | Inner page titles |
\| H2 | 28px / 36px | 24px / 32px | 700 | Major page sections |
\| H3 | 22px / 30px | 20px / 28px | 700 | Card groups and panels |
\| H4 | 18px / 26px | 17px / 25px | 700 | Card titles |
\| Body | 16px / 26px | 16px / 26px | 400 | Default content |
\| Small | 14px / 22px | 14px / 22px | 400 | Labels, metadata |
\| Caption | 12px / 18px | 12px / 18px | 600 | Badges, breadcrumbs, table headers |

**### Typography Rules**

\- Use strong, simple headings.
\- Keep page titles factual: “Study Medicine in the Philippines”, “Partner Colleges”, “For Parents”.
\- Use short paragraphs and bullet lists.
\- Use uppercase only for small labels, badges, and table headings.
\- Do not use negative letter spacing.
\- Avoid script, decorative, or playful fonts.

**---**

**## 5. Page Structure**

Pages should follow a university department layout pattern.

**### Standard Page Order**

1\. Thin top institutional bar
2\. Main header with logo, search, and primary CTA
3\. Primary navigation
4\. Breadcrumb row
5\. Optional announcement strip
6\. Page title
7\. Hero image or intro panel
8\. Tabbed content or key sections
9\. Programme/card grid
10\. CTA or advice zone
11\. Accordions/FAQs
12\. Contact block
13\. Dense institutional footer

**### Page Width**

\| Token | Value | Usage |
\|---|---:|---|
\| \`--container-main\` | \`1160px\` | Main public content |
\| \`--container-wide\` | \`1280px\` | Dashboard and tables |
\| \`--content-narrow\` | \`760px\` | Long-form text |

**### Spacing**

Use an 8px spacing system.

\| Token | Value |
\|---|---:|
\| \`--space-1\` | \`4px\` |
\| \`--space-2\` | \`8px\` |
\| \`--space-3\` | \`12px\` |
\| \`--space-4\` | \`16px\` |
\| \`--space-5\` | \`20px\` |
\| \`--space-6\` | \`24px\` |
\| \`--space-8\` | \`32px\` |
\| \`--space-10\` | \`40px\` |
\| \`--space-12\` | \`48px\` |
\| \`--space-16\` | \`64px\` |
\| \`--space-20\` | \`80px\` |

**### Layout Rules**

\- Use clean white page backgrounds.
\- Use pale blue panels for contextual information.
\- Keep content centered in a fixed max-width container.
\- Use 3-column card grids on desktop.
\- Use single-column stacked layouts on mobile.
\- Do not put large sections inside floating decorative cards.
\- Do not use large rounded SaaS-style panels.

**---**

**## 6. Header and Navigation**

The header should feel like a university website.

**### Top Bar**

A thin deep-indigo bar at the very top.

Suggested content:

\- “Study Medicine Abroad | Philippines | Admissions Support”
\- Optional links: International Students, Work with Us, Alumni/Success Stories, Staff/Admin

**### Main Header**

Should include:

\- MedCon logo
\- Search input
\- Apply Now button
\- Optional WhatsApp button

**### Primary Navigation**

Recommended navigation:

\- Courses
\- Student Life
\- Partner Colleges
\- For Parents
\- For Agents
\- Resources
\- About
\- Contact

Alternative PRD navigation:

\- Home
\- About
\- Study Medicine in the Philippines
\- Partner Colleges
\- For Parents
\- For Agents
\- Apply Now
\- Contact

**### Navigation Rules**

\- Use dropdown indicators for grouped items.
\- Use compact header spacing.
\- Keep header height practical, not oversized.
\- On mobile, collapse into a drawer menu.
\- Keep Apply Now visually distinct.
\- Search can be included as a small institutional-style search box.

**### Breadcrumbs**

Every inner page should include breadcrumbs.

Example:

\`Home > Partner Colleges > College of Medicine\`

Breadcrumbs should be small, teal-linked, and placed above the page title.

**---**

**## 7. Announcement Strip**

Use a blue or deep-green full-width announcement strip for timely admissions messages.

Examples:

\- “2026 Admissions: Applications are now open”
\- “Speak with an adviser before choosing your college”
\- “Agent registration is open for selected countries”

**### Styling**

\`\`\`css
.announcement-strip {
  background: #2559ED;
  color: #FFFFFF;
  text-align: center;
  font-weight: 700;
  padding: 10px 16px;
}
\`\`\`

**---**

**## 8. Hero Sections**

**### Public Page Hero**

Use a large image-led hero with a text panel overlay or adjacent card.

Hero should include:

\- Page title
\- Short intro copy
\- Relevant image
\- Optional CTA

**### Hero Image Direction**

Use images showing:

\- Medical students
\- Anatomy models
\- Lab/classroom work
\- Consultation with students
\- Campus or lecture settings

Avoid:

\- Generic travel photos
\- Hospital surgery scenes
\- Dark atmospheric stock images
\- Abstract vector illustrations

**### Hero Rules**

\- Page H1 should be outside or above the image hero for clarity.
\- Hero image should have a stable height.
\- Text panel can be white on top of image or beside image.
\- Avoid full-screen marketing heroes on inner pages.

Recommended image sizes:

\`\`\`css
.page-hero-image {
  min-height: 280px;
  max-height: 420px;
  object-fit: cover;
}
\`\`\`

**---**

**## 9. Tabs**

Tabs are important for the UK medical school style and should be used for structured academic content.

**### Tab Use Cases**

\- Curriculum
\- Admissions requirements
\- Fees and costs
\- Accommodation
\- Student support
\- Partner colleges
\- Application process

**### Tab Styling**

\- Active tab: blue background or blue text with dark-green support.
\- Inactive tabs: dark-green background or white with border.
\- Tab content panel: mist/off-white or white with clear padding.

\`\`\`css
.tab-active {
  background: #2559ED;
  color: #FFFFFF;
}

.tab-panel {
  background: #F7FFFE;
  padding: 24px;
}
\`\`\`

**### Tab Rules**

\- Tabs must be keyboard accessible.
\- On mobile, tabs may become a select dropdown or stacked accordion.
\- Do not overload tabs with too many items. Keep to 3-6 tabs.

**---**

**## 10. Cards**

Cards should follow an academic programme-card pattern: image on top, blue title bar, short description below.

**### Programme / College Card**

Each card should include:

\- Image
\- Blue title bar
\- Arrow icon on the right
\- Short description
\- Optional metadata: duration, location, intake, fees

\`\`\`css
.programme-card {
  background: #FFFFFF;
  border: 1px solid #D0D5DD;
  border-radius: 0;
  overflow: hidden;
}

.programme-card img {
  aspect-ratio: 16 / 9;
  width: 100%;
  object-fit: cover;
}

.programme-card-title {
  background: #2559ED;
  color: #FFFFFF;
  padding: 12px 16px;
  font-weight: 700;
}
\`\`\`

**### Card Rules**

\- Prefer square or lightly sharp corners, not heavily rounded corners.
\- Use thin borders.
\- Use minimal shadows.
\- Keep card grids aligned.
\- Use consistent image ratios.
\- Avoid nested cards.

**### Card Types**

\- College card
\- Programme card
\- Advice zone card
\- Parent support card
\- Agent benefit card
\- Dashboard metric card
\- Document status card

**---**

**## 11. Advice Zone / CTA Panel**

Use an image-and-text split panel inspired by university advice sections.

**### Use Cases**

\- Application Advice Zone
\- Parent Guidance
\- Agent Partnership
\- Cost Planning
\- Document Checklist

**### Layout**

Desktop:

\- Left: wide image
\- Right: pale blue content panel with CTA

Mobile:

\- Image stacked above content

**### Styling**

\`\`\`css
.advice-zone {
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: #F7FFFE;
}

.advice-zone-content {
  padding: 48px;
}
\`\`\`

**---**

**## 12. Accordions**

Use accordions for FAQs, document lists, parent concerns, eligibility details, and agent requirements.

**### Styling**

Accordion rows should use pale blue backgrounds with teal icons.

\`\`\`css
.accordion-item {
  background: #F7FFFE;
  border-bottom: 2px solid #FFFFFF;
}
\`\`\`

**### Accordion Rules**

\- Use clear headings.
\- Support keyboard navigation.
\- Keep content concise.
\- Avoid using accordions for critical conversion actions.

**---**

**## 13. Buttons**

Buttons should feel institutional, direct, and modern. The main action color is blue. Teal is reserved for WhatsApp and success/verified actions, while orange is reserved for rare attention states.

**### Primary Button**

Use the logo blue `#2559ED` for the primary button, with white text and square or lightly rounded corners.

\`\`\`css
.btn-primary {
  background: #2559ED;
  color: #FFFFFF;
  border-radius: 0;
  min-height: 44px;
  padding: 0 18px;
  font-weight: 700;
}
\`\`\`

**### Secondary Button**

\`\`\`css
.btn-secondary {
  background: #16055D;
  color: #FFFFFF;
  border-radius: 0;
  min-height: 44px;
  padding: 0 18px;
  font-weight: 700;
}
\`\`\`

**### Outline Button**

\`\`\`css
.btn-outline {
  background: #FFFFFF;
  color: #16055D;
  border: 1px solid #16055D;
  border-radius: 0;
  min-height: 44px;
}
\`\`\`

**### Button Rules**

\- Use rectangular buttons with limited rounding.
\- Include arrow icons for “Find out more”, “View college”, and “Apply now”.
\- Minimum height: \`44px\`.
\- Use clear labels.
\- Avoid multiple competing primary buttons in one area.

Common labels:

\- Find a course
\- Apply Now
\- Chat on WhatsApp
\- View College
\- Find out more
\- Register as an Agent
\- Book a Consultation
\- Submit Application

**---**

**## 14. Forms**

Forms should feel official and structured.

**### Form Types**

\- Contact form
\- Apply Now lead form
\- Agent registration form
\- Parent consultation form
\- Student application form
\- Document upload form

**### Input Styling**

\`\`\`css
.input {
  min-height: 44px;
  border: 1px solid #98A2B3;
  border-radius: 0;
  padding: 10px 12px;
  background: #FFFFFF;
  color: #101828;
}

.input\:focus {
  outline: 3px solid rgba(0, 187, 160, 0.25);
  border-color: #2559ED;
}
\`\`\`

**### Form Rules**

\- Always show visible labels.
\- Group fields into fieldsets.
\- Use short helper text where needed.
\- Mark required fields clearly.
\- Use step indicators for long application flows.
\- Use confirmation panels after submission.
\- Avoid placeholder-only forms.

**### Application Form Sections**

\- Personal details
\- Parent/guardian details
\- Education background
\- Preferred college and course
\- Passport and travel readiness
\- Budget and intake preference
\- Document upload
\- Declaration and consent

**---**

**## 15. Tables**

Tables should be used for admissions, costs, document requirements, applications, and dashboard workflows.

**### Public Tables**

Use for:

\- College comparison
\- Tuition ranges
\- Admission requirements
\- Application timelines

**### Dashboard Tables**

Use for:

\- Applications
\- Students
\- Agents
\- Documents
\- Enquiries
\- Bookings
\- Commissions

**### Table Styling**

\`\`\`css
.table {
  width: 100%;
  border-collapse: collapse;
  background: #FFFFFF;
  border: 1px solid #D0D5DD;
}

.table th {
  background: #16055D;
  color: #FFFFFF;
  text-align: left;
  font-size: 13px;
  font-weight: 700;
  padding: 12px 14px;
}

.table td {
  border-bottom: 1px solid #EAECF0;
  padding: 12px 14px;
}
\`\`\`

**### Table Rules**

\- Use filters above admin tables.
\- Use badges for status fields.
\- Keep row actions compact.
\- Convert complex tables to stacked cards on mobile.
\- Do not use decorative shadows around tables.

**---**

**## 16. Dashboard Patterns**

Future dashboards should retain the same UK academic design language but become more operational.

**### Dashboard Layout**

Desktop:

\- Indigo sidebar
\- Light page background
\- Top utility bar
\- Metric cards
\- Data tables
\- Detail pages

Mobile:

\- Top bar
\- Drawer navigation
\- Stacked metrics
\- Card-based records

**### Dashboard Sidebar**

Sections:

\- Overview
\- Applications
\- Students
\- Documents
\- Agents
\- Commissions
\- Bookings
\- Enquiries
\- Colleges
\- Reports
\- Settings

**### Metric Cards**

Metric cards should be simple and information-first.

Each card includes:

\- Label
\- Number
\- Status note
\- Optional icon

Examples:

\- Total applications
\- Pending review
\- Documents pending
\- Approved admissions
\- New enquiries
\- Active agents

**### Dashboard Rules**

\- Use compact headings.
\- Keep actions close to the table or record they affect.
\- Use teal active states.
\- Avoid marketing-style graphics.
\- Prioritize search, filters, status, and review actions.

**---**

**## 17. Status Badges**

Status badges should be clear, compact, and consistent.

\| Status | Color |
\|---|---|
\| Draft | Gray |
\| Submitted | Blue |
\| Received | Teal |
\| Under Review | Warning |
\| Documents Pending | Warning |
\| Approved | Success |
\| Rejected | Error |
\| Admission Issued | Green |
\| Visa Processing | Blue |
\| Enrolled | Success |

**### Badge Styling**

\`\`\`css
.badge {
  display: inline-flex;
  align-items: center;
  min-height: 24px;
  padding: 2px 8px;
  border-radius: 0;
  font-size: 12px;
  font-weight: 700;
}
\`\`\`

**---**

**## 18. Footer**

The footer should be dense and institutional, similar to university websites.

**### Footer Background**

Use MedCon dark:

\`\`\`css
background: *#16055D*;
color: *#FFFFFF;*
\`\`\`

**### Footer Columns**

Recommended columns:

1\. Schools / Services
2\. Student Information
3\. Admissions Support
4\. Contact Us
5\. Legal

**### Footer Content**

Include:

\- MedCon logo
\- Medcon Educational Services and Consultancy Limited
\- WhatsApp number
\- Email
\- Office address, if available
\- Social media icons
\- Quick links
\- Privacy Policy
\- Terms of Use
\- Accessibility statement
\- Copyright

**### Optional Footer Visual**

A subtle map or location graphic can be used, but it should not distract from contact links.

**---**

**## 19. Accessibility**

The design must meet practical WCAG AA expectations.

**### Requirements**

\- Clear color contrast.
\- Keyboard-accessible menus, tabs, accordions, and forms.
\- Visible focus states.
\- Real labels on form fields.
\- Alt text for informative images.
\- Decorative icons hidden from screen readers.
\- Minimum tap target size of \`44px\`.
\- No text over images unless contrast is controlled.

**### Focus State**

\`\`\`css
\:focus-visible {
  outline: 3px solid rgba(0, 187, 160, 0.45);
  outline-offset: 2px;
}
\`\`\`

**---**

**## 20. Responsive Behavior**

**### Mobile**

\- Header collapses into drawer menu.
\- Search can move into menu.
\- Hero image and intro panel stack.
\- Tabs become accordions or horizontal scroll tabs.
\- Card grids become single column.
\- Tables become stacked cards.
\- CTAs become full-width.
\- Floating WhatsApp button must not cover form submit buttons.

**### Tablet**

\- Use 2-column card grids.
\- Keep tab layouts if labels fit.
\- Allow horizontal scroll for comparison tables only when necessary.

**### Desktop**

\- Use full header navigation.
\- Use 3-column programme grids.
\- Use image-and-panel advice zones.
\- Use wide dashboard tables.

**---**

**## 21. Page-Specific Design Guidance**

**### Home**

Use a strong institutional hero with MedCon’s promise, image-led academic styling, and clear Apply/WhatsApp actions. Include quick cards for study destination, partner colleges, parents, and agents.

**### Study Medicine in the Philippines**

Use a page title, hero image, tabbed sections, requirements panel, cost overview table, and application process steps.

**### Partner Colleges**

Use programme-style cards with image, teal title strip, location, duration, tuition range, and CTA arrow.

**### Individual College Page**

Use:

\- Breadcrumbs
\- Page title
\- Hero image
\- Intro text panel
\- Tabs for Overview, Programmes, Fees, Requirements, Accommodation
\- Programme cards
\- FAQs
\- Apply CTA

**### For Parents**

Use reassuring content panels, FAQs, cost clarity, safety/accommodation information, and consultation CTA.

**### For Agents**

Use structured process steps, requirements, benefits cards, commission explanation, and registration form.

**### Apply Now**

Phase 1 should use an official lead form. Phase 2 should become a full account and application workflow.

**---**

**## 22. Imagery Rules**

**### Use**

\- Medical students studying
\- Students with anatomy models
\- Lab and lecture settings
\- Admissions consultation scenes
\- Group learning
\- College/campus images where available

**### Avoid**

\- Random travel stock photos
\- Surgical operation imagery
\- Abstract gradient-only visuals
\- Cartoon medical illustrations
\- Images with poor lighting or unclear subjects

**### Image Treatment**

\- Use clean crops.
\- Keep people visible and natural.
\- Use consistent ratios.
\- Do not blur primary images.
\- Avoid dark overlays unless necessary for text contrast.

**---**

**## 23. Implementation Guidance**

**### Recommended Stack**

\- Next.js
\- TypeScript
\- Tailwind CSS
\- shadcn/ui
\- Supabase
\- PostgreSQL
\- Supabase Auth
\- Supabase Storage
\- Sanity CMS

**### Tailwind Token Example**

\`\`\`ts
colors: {
  medcon: {
    dark: "#16055D",
    blue: "#2559ED",
    green: "#00BBA0", // legacy token name retained for compatibility
    teal: "#00BBA0",
    mist: "#F7FFFE",
    orange: "#FC8C08",
  },
}
\`\`\`

**### Components to Build**

\- \`TopBar\`
\- \`MainHeader\`
\- \`PrimaryNav\`
\- \`Breadcrumbs\`
\- \`AnnouncementStrip\`
\- \`PageHero\`
\- \`TabbedContent\`
\- \`ProgrammeCard\`
\- \`CollegeCard\`
\- \`AdviceZone\`
\- \`Accordion\`
\- \`ContactBlock\`
\- \`Footer\`
\- \`ApplicationForm\`
\- \`AgentForm\`
\- \`StatusBadge\`
\- \`DashboardShell\`
\- \`MetricCard\`
\- \`DataTable\`
\- \`FileUpload\`
\- \`EmptyState\`

**### Component Rules**

\- Centralize all color tokens.
\- Use semantic HTML.
\- Keep tabs and accordions accessible.
\- Use stable image aspect ratios.
\- Keep public components separate from dashboard components.
\- Use reusable card and table patterns.
\- Avoid hard-coded colors in page files.

**---**

**## 24. First Launch Scope**

The first launch should include:

\- Home
\- About
\- Study Medicine in the Philippines
\- Partner Colleges
\- College Detail Pages
\- For Parents
\- For Agents + registration form
\- Apply Now + lead form
\- Contact
\- FAQs
\- Privacy Policy
\- Terms of Use

The first launch should exclude:

\- Student dashboard
\- Admin dashboard
\- Agent dashboard
\- Full online application workflow
\- Document upload
\- Payment system
\- Partner college portal
\- Complex CRM
\- Blog, unless content is ready
\- Cost calculator, unless tuition data is confirmed
\- Eligibility checker, unless admission rules are confirmed

**---**

**## 24A. Color Migration Rule**

For the current website implementation, this color update should be handled centrally through design tokens rather than by redesigning individual pages.

Replace legacy core values as follows:

```css
/* OLD → NEW */
#001F1C → #16055D
#274DEA → #2559ED
#50D24E → #00BBA0
```

Search the codebase for hard-coded instances of the legacy colors and replace them with the appropriate token. Preserve existing component hierarchy, sizing, spacing, imagery, and interaction patterns. Recheck WCAG contrast after migration, especially for teal buttons or badges on white backgrounds.

---

**## 25. Design Acceptance Criteria**

**### Public Website Card and Form Refinement (August 2026)**

This is a limited component update for the public website only. It does not apply to the student, agent, admin, or other internal application interfaces, and it must not become a broader layout, typography, spacing, or color redesign.

- Public-site cards should use lightly rounded corners (approximately `8px`) with thin borders and restrained or no shadows. Their content hierarchy, imagery, spacing, and existing colors remain unchanged.
- Public-site form controls should use subtle rounding (approximately `7px`), a quiet `1px` neutral outline, normal-weight input text, and the existing brand-colored focus state.
- Form labels should remain visible but use a less forceful weight. Placeholder text should be clearly lighter than entered text while retaining accessible contrast.
- Form containers should follow the same lightly rounded, low-shadow treatment as other public-site cards.
- Buttons, pills, navigation, accordions, tables, page structure, and internal app components are outside the scope of this refinement.

The design implementation is complete when:

\- The website feels like a credible UK medical school or university department site.
\- Indigo, blue, and teal are used consistently according to the logo-derived hierarchy.
\- Header, breadcrumbs, tabs, cards, accordions, and footer follow the defined patterns.
\- Programme and college cards use image + teal title strip styling.
\- Public pages are information-rich but easy to scan.
\- Forms feel official and structured.
\- Mobile layouts are clean and usable.
\- Tables and dashboard patterns are ready for future platform phases.
\- CTAs remain visible without making the site feel overly commercial.
\- Accessibility basics are implemented.

**---**

**## 26. Summary**

Medcon Edu’s design system should present the brand as a serious medical education admissions institution. The visual style should borrow from UK medical school websites: strong indigo header/footer, blue actions, teal academic accents, structured navigation, breadcrumbs, page titles, image-led programme cards, tabbed content, accordions, advice panels, and clear institutional contact areas. This creates a credible foundation for the public website and future admissions dashboards.
