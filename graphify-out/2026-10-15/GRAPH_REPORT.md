# Graph Report - eva_salon  (2026-10-15)

## Corpus Check
- 266 files · ~266,304 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 75 file(s) not represented in the graph (top: .csv 53, (none) 17, .ico 2)

## Summary
- 3168 nodes · 4754 edges · 210 communities (148 shown, 62 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 139 edges (avg confidence: 0.86)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- ShadcnInstaller
- scripts/core.py
- Illuminate\Database\Schema\Blueprint
- gray
- Illuminate\Foundation\Http\FormRequest
- User
- composer.json
- Tz
- slide_search_core.py
- Tailwind CSS Utility Reference
- design_system.py
- Illuminate\Database\Eloquent\Model
- spacing
- BookingService.php
- json
- validate_data.py
- icon/generate.py
- StaffController
- html-token-validator.py
- Brand Guidelines v1.0
- Booking
- Design
- package.json
- Canvas Design System
- unittest
- read_rows
- test_data_contracts.py
- Illuminate\Contracts\View\View
- Staff
- CustomerController.php
- generate-slide.py
- _style_is_dark_primary
- argparse
- TestTailwindConfigGenerator
- fetch-background.py
- color
- agent.md: Panduan untuk AI Coding Agent
- test_core_data_quality.py
- cip/generate.py
- fontSize
- TailwindConfigGenerator
- SalonProfile
- extract-colors.cjs
- csv
- DesignSystemGenerator
- CatalogRefreshTest
- Prerequisites
- validate-asset.cjs
- TestWebStackFreshness
- design-tokens-starter.json
- .generate_config_string
- Form & Input Components
- TestDomainDetection
- test_core.py
- Tailwind CSS Responsive Design
- embed-tokens.cjs
- validate-tokens.cjs
- card
- BookingStatus
- Typography Specifications
- StatusController
- inject-brand-context.cjs
- design.md: Panduan Desain Eva Salon
- primitive
- test_native_desktop_stack_freshness.py
- sync-brand-to-tokens.cjs
- generate-tokens.cjs
- button
- main
- parse_decision_rules
- Logo Usage Rules
- Component Specifications
- shadcn/ui Accessibility Patterns
- input
- radius
- Asset Approval Checklist
- test_relevance_evaluator.py
- shadow
- Logo AI Prompt Engineering
- bootstrap/app.php
- Color Palette Management
- $type
- radius
- lg
- _filter_anti_patterns_for_mode
- scripts/search.py
- padding-y
- xl
- TestGeneratedCatalogContract
- none
- destructive
- destructive-foreground
- muted
- primary-foreground
- ring
- secondary-foreground
- components/layouts/admin.blade.php
- views/layouts/admin.blade.php
- CIP Deliverable Guide
- States and Variants
- UI Styling Skill
- Workflow
- Design System
- Tailwind CSS Customization
- C. Migration
- Routing by Task Type
- shadcn/ui Theming & Customization
- Asset Organization Guide
- Primary Color Meanings
- Core Logo Types
- Brand Consistency Checklist
- CIP Mockup Prompt Engineering
- Color Semantics
- Design Principles
- Design Principles
- CIP Design Reference
- Icon Design Reference
- Copywriting Formulas
- Copywriting Formulas
- Banner Design - Multi-Format Creative Banner System
- Messaging Framework
- Brand Voice Framework
- Layout Patterns
- Tailwind Integration
- Layout Patterns
- PRD: Sistem Booking Salon Kecantikan "Eva Salon" (Satu Cabang)
- update.md
- Logo Design Reference
- Token Architecture
- Primitive Tokens
- Core Visual Elements
- CIP Design Style Guide
- md
- Wireframe Sistem Booking Salon
- Brand
- Slide Strategies
- Component Tokens
- Slide Strategies
- 6.5 Dashboard Admin (kasir dan owner)
- test_design_system_mode.py
- Ponytail Help
- Slides Reference
- HTML Slide Template
- HTML Slide Template
- render-html.py
- 6. Aturan Bisnis Inti (Jangan Diubah Tanpa Persetujuan)
- Slides
- test_style_taxonomy.py
- 7. Aturan Bisnis
- Brand Guidelines Template
- ponytail-audit/SKILL.md
- Ponytail Gain
- ponytail-review/SKILL.md
- Ponytail
- 13. Fase Pengembangan
- 6. Kebutuhan Fungsional
- 10. Aturan Kerja untuk Agent
- 7. Standar Kode
- Ponytail, lazy senior dev mode
- Ponytail, lazy senior dev mode
- ponytail-debt/SKILL.md
- BM25
- BM25
- 9. Arsitektur Teknis
- 5. Ruang Lingkup
- slides-create.md
- create.md

## God Nodes (most connected - your core abstractions)
1. `TailwindConfigGenerator` - 58 edges
2. `Booking` - 52 edges
3. `User` - 52 edges
4. `Tz` - 47 edges
5. `Staff` - 40 edges
6. `BookingStatus` - 37 edges
7. `TestTailwindConfigGenerator` - 35 edges
8. `DesignSystemGenerator` - 35 edges
9. `ShadcnInstaller` - 34 edges
10. `SalonProfile` - 32 edges

## Surprising Connections (you probably didn't know these)
- `BookingFlowTest` --references--> `Service`  [EXTRACTED]
  tests/Feature/BookingFlowTest.php → app/Models/Service.php
- `BookingFlowTest` --references--> `Staff`  [EXTRACTED]
  tests/Feature/BookingFlowTest.php → app/Models/Staff.php
- `BookingServiceTest` --references--> `AvailabilityService`  [EXTRACTED]
  tests/Unit/BookingServiceTest.php → app/Services/AvailabilityService.php
- `BookingServiceTest` --references--> `BookingService`  [EXTRACTED]
  tests/Unit/BookingServiceTest.php → app/Services/BookingService.php
- `TestGeneratedConfigIsValidJs` --uses--> `TailwindConfigGenerator`  [INFERRED]
  .agents/skills/ui-styling/scripts/tests/test_tailwind_config_gen.py → .agents/skills/ui-styling/scripts/tailwind_config_gen.py

## Import Cycles
- None detected.

## Communities (210 total, 62 thin omitted)

### Community 0 - "ShadcnInstaller"
Cohesion: 0.05
Nodes (3): main(), ShadcnInstaller, TestShadcnInstaller

### Community 1 - "scripts/core.py"
Cohesion: 0.06
Nodes (31): BM25, _contains_phrase(), detect_domain(), _domain_keywords(), _exact_match_diagnostic(), _exact_row_identity(), _exact_stack_identifier(), _file_signature() (+23 more)

### Community 2 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.05
Nodes (15): {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}() (+7 more)

### Community 3 - "gray"
Cohesion: 0.05
Nodes (53): $type, $value, $type, $value, $type, $value, $type, $value (+45 more)

### Community 4 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.06
Nodes (9): AuthController, ChangePasswordRequest, CustomerRequest, ExceptionRequest, GalleryRequest, LoginRequest, PaymentRequest, ProfileRequest (+1 more)

### Community 5 - "User"
Cohesion: 0.05
Nodes (11): Role, Kasir, Owner, CashierController, CashierRequest, User, BookingPolicy, CustomerPolicy (+3 more)

### Community 6 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, files, psr-4, config (+40 more)

### Community 7 - "Tz"
Cohesion: 0.08
Nodes (11): NormalizesPhone, HoldRequest, StoreBookingRequest, WalkInRequest, AvailabilityService, {closure#2}(), jam(), local() (+3 more)

### Community 8 - "slide_search_core.py"
Cohesion: 0.08
Nodes (17): format_context(), format_result(), main(), BM25, calculate_pattern_break(), detect_domain(), get_background_config(), get_color_for_emotion() (+9 more)

### Community 9 - "Tailwind CSS Utility Reference"
Cohesion: 0.05
Nodes (43): Arbitrary Values, Aspect Ratio, Background Colors, Border Color, Border Radius, Border Style, Border Width, Borders (+35 more)

### Community 10 - "design_system.py"
Cohesion: 0.07
Nodes (15): ansi_ljust(), _detect_page_type(), format_ascii_box(), format_markdown(), format_master_md(), format_page_override_md(), generate_design_system(), _generate_intelligent_overrides() (+7 more)

### Community 11 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.06
Nodes (9): ExceptionType, SalonClosed, StaffLeave, BookingItem, BookingStatusLog, Undeletable, Payment, ScheduleException (+1 more)

### Community 12 - "spacing"
Cohesion: 0.06
Nodes (34): $type, $value, $type, $value, $type, $value, $type, $value (+26 more)

### Community 13 - "BookingService.php"
Cohesion: 0.09
Nodes (15): ActorType, Customer, Kasir, Owner, System, PaymentKind, Correction, Payment (+7 more)

### Community 14 - "json"
Cohesion: 0.13
Nodes (5): test_sync_parses_bundled_starter_template(), main(), _run(), test_flags_hardcoded_hex_sharing_line_with_token(), test_token_only_line_reports_no_violation()

### Community 15 - "validate_data.py"
Cohesion: 0.12
Nodes (31): _catalog_date(), _check_app_interface_contract(), _check_catalog_contract(), _check_catalog_summary(), _check_color_contract(), _check_core_data_contract(), _check_file(), _check_font_catalog() (+23 more)

### Community 16 - "icon/generate.py"
Cohesion: 0.10
Nodes (13): apply_color(), apply_viewbox_size(), extract_svgs(), generate_batch(), generate_icon(), generate_sizes(), load_env(), main() (+5 more)

### Community 17 - "StaffController"
Cohesion: 0.18
Nodes (4): {closure#1}(), {closure#2}(), StaffController, StaffRequest

### Community 18 - "html-token-validator.py"
Cohesion: 0.13
Nodes (12): get_context(), is_allowed_exception(), is_allowed_rgba(), is_inside_block(), load_css_variables(), main(), print_result(), print_summary() (+4 more)

### Community 19 - "Brand Guidelines v1.0"
Cohesion: 0.05
Nodes (37): 1. Color Palette, 2. Typography, 3. Logo Usage, 4. Voice & Tone, 5. Imagery Guidelines, 6. Design Components, Accessibility, AI Image Generation (+29 more)

### Community 20 - "Booking"
Cohesion: 0.13
Nodes (12): PaymentMethod, Cash, Qris, Transfer, BookingController, {closure#1}(), {closure#2}(), {closure#4}() (+4 more)

### Community 21 - "Design"
Cohesion: 0.06
Nodes (35): Banner Design (Built-in), Banner: Design Rules, Banner: Quick Size Reference, Banner: Top Art Styles, Banner: Workflow, CIP Design (Built-in), CIP: Generate Brief, CIP: Generate Mockups (+27 more)

### Community 22 - "package.json"
Cohesion: 0.08
Nodes (24): devDependencies, concurrently, @fontsource/cormorant-garamond, @fontsource-variable/plus-jakarta-sans, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+16 more)

### Community 23 - "Canvas Design System"
Cohesion: 0.06
Nodes (35): 1. Visual Communication First, 2. Minimal Text Integration, 3. Expert Craftsmanship, 4. Systematic Patterns, Analog Meditation, Approach, Canvas Boundaries, Canvas Design System (+27 more)

### Community 24 - "unittest"
Cohesion: 0.17
Nodes (3): read_rows(), TestTextLayoutDataContracts, TestTextLayoutRetrieval

### Community 25 - "read_rows"
Cohesion: 0.10
Nodes (3): read_rows(), TestLandingAndStackContract, TestReasoningContract

### Community 26 - "test_data_contracts.py"
Cohesion: 0.29
Nodes (3): split_values(), style_identities(), TestStyleIdentityContract

### Community 27 - "Illuminate\Contracts\View\View"
Cohesion: 0.06
Nodes (12): TodayController, CatalogController, Controller, HomeController, {closure#1}(), ProfileController, ServiceController, CategoryRequest (+4 more)

### Community 28 - "Staff"
Cohesion: 0.11
Nodes (7): BusinessHour, ServiceCategory, Staff, StaffSchedule, DatabaseSeeder, TestCase, BookingServiceTest

### Community 29 - "CustomerController.php"
Cohesion: 0.10
Nodes (4): {closure#2}(), CustomerController, Customer, Phone

### Community 30 - "generate-slide.py"
Cohesion: 0.13
Nodes (11): _e(), generate_chart_slide(), generate_cta_slide(), generate_deck(), generate_metrics_slide(), generate_problem_slide(), generate_solution_slide(), generate_testimonial_slide() (+3 more)

### Community 31 - "_style_is_dark_primary"
Cohesion: 0.21
Nodes (4): _query_wants_dark(), _resolve_color_mode(), _style_is_dark_primary(), TestModeResolution

### Community 32 - "argparse"
Cohesion: 0.15
Nodes (5): format_brief(), format_results(), main(), format_output(), generate_design_brief()

### Community 34 - "fetch-background.py"
Cohesion: 0.16
Nodes (9): generate_css_for_background(), get_background_image(), get_curated_images(), get_overlay_css(), get_pexels_search_url(), load_backgrounds_config(), load_brand_colors(), main() (+1 more)

### Community 35 - "color"
Cohesion: 0.11
Nodes (19): $type, $value, background, foreground, muted-foreground, primary, primary-hover, secondary (+11 more)

### Community 36 - "agent.md: Panduan untuk AI Coding Agent"
Cohesion: 0.15
Nodes (12): 11. Urutan Pengerjaan yang Disarankan, 12. Pertanyaan Terbuka yang Masih Perlu Diverifikasi, 1. Konteks Proyek, 2. Stack Teknologi, 3. Lingkungan dan Konfigurasi, 4. Peran dan Otorisasi, 5. Model Data dan Konvensi, 6A. Aturan Layanan Sensitif (+4 more)

### Community 37 - "test_core_data_quality.py"
Cohesion: 0.13
Nodes (12): read_rows(), TestAccessibilityGuidance, TestChartsTypographyAndIcons, TestCurrentReactGuidance, TestSemanticColors, _check_chart_contract(), _check_typography_contract(), _configured_font_names() (+4 more)

### Community 38 - "cip/generate.py"
Cohesion: 0.19
Nodes (7): build_cip_prompt(), check_logo_required(), generate_cip_set(), generate_with_nano_banana(), load_env(), load_logo_image(), main()

### Community 39 - "fontSize"
Cohesion: 0.12
Nodes (16): $type, $value, $type, $value, $type, $value, $type, $value (+8 more)

### Community 41 - "SalonProfile"
Cohesion: 0.11
Nodes (4): BookingController, PanelAccess, SalonProfile, Wa

### Community 42 - "extract-colors.cjs"
Cohesion: 0.20
Nodes (11): calculateCompliance(), colorDistance(), displayPalette(), extractHexColors(), findNearestBrandColor(), fs, generateImageMagickCommand(), hexToRgb() (+3 more)

### Community 43 - "csv"
Cohesion: 0.10
Nodes (11): detect_domain(), get_cip_brief(), _load_csv(), search(), search_all(), _search_csv(), detect_domain(), _load_csv() (+3 more)

### Community 44 - "DesignSystemGenerator"
Cohesion: 0.15
Nodes (3): DesignSystemGenerator, TestReasoningMatch, TestEndToEndCoherence

### Community 46 - "Prerequisites"
Cohesion: 0.06
Nodes (34): Accessibility, Available Domains, Available Stacks, Common Rules for Professional UI, Common Sticking Points, Example Workflow, How to Use This Skill, Icons & Visual Elements (+26 more)

### Community 47 - "validate-asset.cjs"
Cohesion: 0.25
Nodes (13): checkManifest(), formatBytes(), formatOutput(), fs, main(), parseFilename(), path, RULES (+5 more)

### Community 50 - "design-tokens-starter.json"
Cohesion: 0.15
Nodes (12): component, $type, $value, dark, semantic, $schema, $type, $value (+4 more)

### Community 52 - "Form & Input Components"
Cohesion: 0.06
Nodes (32): Accordion, Alert, Alert Dialog, Avatar, Badge, Button, Card, Checkbox (+24 more)

### Community 54 - "test_core.py"
Cohesion: 0.06
Nodes (4): TestBm25CoreBehavior, TestDiagnosticsContracts, TestSearchDomains, TestTokenizer

### Community 55 - "Tailwind CSS Responsive Design"
Cohesion: 0.06
Nodes (32): 1. Mobile-First Design, 2. Consistent Breakpoint Usage, 3. Test at Breakpoint Boundaries, 4. Use Container for Content Width, 5. Progressive Enhancement, 6. Avoid Too Many Breakpoints, Best Practices, Breakpoint System (+24 more)

### Community 56 - "embed-tokens.cjs"
Cohesion: 0.17
Nodes (8): args, fs, minimal, MINIMAL_TOKENS, path, projectRoot, tokensPath, wrapStyle

### Community 57 - "validate-tokens.cjs"
Cohesion: 0.24
Nodes (11): extensions, formatReport(), fs, getFiles(), main(), parseArgs(), path, patterns (+3 more)

### Community 58 - "card"
Cohesion: 0.20
Nodes (12): $type, $value, bg, bg, padding, shadow, card, bg (+4 more)

### Community 59 - "BookingStatus"
Cohesion: 0.05
Nodes (20): BookingSource, Online, PhoneHold, WalkIn, BookingStatus, Cancelled, Completed, Confirmed (+12 more)

### Community 60 - "Typography Specifications"
Cohesion: 0.06
Nodes (30): Accessibility, Base System, Best Practices, Clean & Modern, Common Font Pairings, Contrast Requirements, CSS Implementation, Editorial (+22 more)

### Community 62 - "inject-brand-context.cjs"
Cohesion: 0.31
Nodes (10): extractColorsFromTable(), extractCoreAttributes(), extractHexColors(), extractImageStyle(), extractTypography(), extractVoice(), fs, generatePromptAddition() (+2 more)

### Community 63 - "design.md: Panduan Desain Eva Salon"
Cohesion: 0.07
Nodes (29): 10. Data Layanan Awal (seed), 11. Pertimbangan Konten Sensitif, 12. Panduan Gambar, 13. Aksesibilitas dan Performa, 14. Token Tailwind (acuan implementasi), 15. Hal yang Perlu Dikonfirmasi ke Owner, 1. Identitas dan Karakter Brand, 2. Analisis Foto Interior (sumber inspirasi visual) (+21 more)

### Community 64 - "primitive"
Cohesion: 0.18
Nodes (11): fast, normal, slow, $type, $value, $type, $value, primitive (+3 more)

### Community 66 - "sync-brand-to-tokens.cjs"
Cohesion: 0.29
Nodes (8): adjustBrightness(), { execFileSync }, extractColorsFromMarkdown(), fs, generateColorScale(), main(), path, updateDesignTokens()

### Community 67 - "generate-tokens.cjs"
Cohesion: 0.36
Nodes (9): flattenTokens(), fs, generateCSS(), generateTailwind(), main(), parseArgs(), path, resolveReference() (+1 more)

### Community 68 - "button"
Cohesion: 0.20
Nodes (10): fg, font-size, hover-bg, button, $type, $value, $type, $value (+2 more)

### Community 71 - "parse_decision_rules"
Cohesion: 0.31
Nodes (4): apply_decision_rules(), _object_without_duplicates(), parse_decision_rules(), _validate_action()

### Community 72 - "Logo Usage Rules"
Cohesion: 0.07
Nodes (28): Absolute Don'ts, Approved Backgrounds, Before Using Logo, Clear Space, Co-branding, Color Rules, Color Usage, Color Variants (+20 more)

### Community 73 - "Component Specifications"
Cohesion: 0.07
Nodes (28): Alert, Anatomy, Anatomy, Anatomy, Anatomy, Anatomy, Badge, Button (+20 more)

### Community 74 - "shadcn/ui Accessibility Patterns"
Cohesion: 0.07
Nodes (28): Accordion, Alert, ARIA Labels, Checkbox and Radio, Color Contrast, Command Palette Navigation, Component-Specific Patterns, Dialog/Modal Navigation (+20 more)

### Community 75 - "input"
Cohesion: 0.29
Nodes (8): padding-x, input, $type, $value, focus-ring, padding-x, $type, $value

### Community 76 - "radius"
Cohesion: 0.29
Nodes (8): $type, $value, $type, $value, radius, default, full, default

### Community 79 - "Asset Approval Checklist"
Cohesion: 0.08
Nodes (25): Accessibility, Archival, Asset Approval Checklist, Automation Support, Color Compliance, Common Issues & Fixes, Content Accessibility, Content Quality (+17 more)

### Community 81 - "test_relevance_evaluator.py"
Cohesion: 0.12
Nodes (3): TestFixtureValidation, TestMetricMath, TestThresholdGate

### Community 82 - "shadow"
Cohesion: 0.47
Nodes (6): sm, shadow, sm, sm, $type, $value

### Community 83 - "Logo AI Prompt Engineering"
Cohesion: 0.08
Nodes (25): Common Pitfalls, Core Prompt Structure, Detailed Brief, Eco/Sustainable, Effective Keywords by Style, Fashion Brand, Healthcare, Industry-Specific Prompts (+17 more)

### Community 84 - "bootstrap/app.php"
Cohesion: 0.24
Nodes (3): {closure#1}(), {closure#4}(), {closure#5}()

### Community 85 - "Color Palette Management"
Cohesion: 0.08
Nodes (24): Accessibility Requirements, Brand Compliance Validation, Checking Contrast, Color Documentation Format, Color Extraction, Color Palette Examples, Color Palette Management, Color System Structure (+16 more)

### Community 86 - "$type"
Cohesion: 0.60
Nodes (5): $type, $value, border, border, border

### Community 87 - "radius"
Cohesion: 0.60
Nodes (5): radius, radius, radius, $type, $value

### Community 88 - "lg"
Cohesion: 0.60
Nodes (5): lg, $type, $value, lg, lg

### Community 92 - "padding-y"
Cohesion: 0.67
Nodes (4): padding-y, padding-y, $type, $value

### Community 93 - "xl"
Cohesion: 0.67
Nodes (4): xl, xl, $type, $value

### Community 95 - "none"
Cohesion: 0.67
Nodes (4): $type, $value, none, none

### Community 97 - "destructive"
Cohesion: 0.67
Nodes (3): destructive, $type, $value

### Community 98 - "destructive-foreground"
Cohesion: 0.67
Nodes (3): destructive-foreground, $type, $value

### Community 99 - "muted"
Cohesion: 0.67
Nodes (3): muted, $type, $value

### Community 100 - "primary-foreground"
Cohesion: 0.67
Nodes (3): primary-foreground, $type, $value

### Community 101 - "ring"
Cohesion: 0.67
Nodes (3): ring, $type, $value

### Community 102 - "secondary-foreground"
Cohesion: 0.67
Nodes (3): secondary-foreground, $type, $value

### Community 140 - "CIP Deliverable Guide"
Cohesion: 0.08
Nodes (24): Apparel, Business Card, Car/Sedan, CIP Deliverable Guide, Core Identity, Digital Assets, Email Signature, Envelope (+16 more)

### Community 141 - "States and Variants"
Cohesion: 0.08
Nodes (24): Accessibility, Accessibility Requirements, ARIA States, Color Contrast, Color Variants, Disabled States, Error Messages, Error States (+16 more)

### Community 142 - "UI Styling Skill"
Cohesion: 0.08
Nodes (24): Accessibility Patterns, Alternative: Tailwind-Only Setup, Best Practices, Common Patterns, Component Layer: shadcn/ui, Component Library Guide, Component + Styling Setup, Core Stack (+16 more)

### Community 143 - "Workflow"
Cohesion: 0.08
Nodes (23): Art Direction Styles (Reuse from Banner), Color & Contrast, Design Best Practices, HTML Design Rules, HTML Template Structure, Option A: Chrome Headless CLI (Recommended — zero dependencies), Option B: chrome-devtools skill, Option C: Playwright script (+15 more)

### Community 144 - "Design System"
Cohesion: 0.09
Nodes (22): Best Practices, Chart.js Integration, Command, Component Spec Pattern, Contextual Decision Flow, Decision System CSVs, Design System, Integration (+14 more)

### Community 145 - "Tailwind CSS Customization"
Cohesion: 0.09
Nodes (22): @apply Directive, Best Practices, Color Customization, Complete Tailwind Config, Configuration Examples, Content Configuration, Custom Color Palette, Custom Font Sizes (+14 more)

### Community 146 - "C. Migration"
Cohesion: 0.09
Nodes (21): 10. `2026_10_15_000010_create_service_staff_table.php`, 11. `2026_10_15_000011_create_bookings_table.php`, 12. `2026_10_15_000012_create_booking_items_table.php`, 13. `2026_10_15_000013_create_booking_status_logs_table.php`, 14. `2026_10_15_000014_create_payments_table.php`, 15. `2026_10_15_000015_create_gallery_images_table.php`, 1. `2026_10_15_000001_create_users_table.php`, 2. `2026_10_15_000002_create_salon_profiles_table.php` (+13 more)

### Community 147 - "Routing by Task Type"
Cohesion: 0.10
Nodes (19): Banner Design Tasks, Brand Identity Tasks, Component Creation, Corporate Identity Program Tasks, Design Routing Guide, Design System Migration, Icon Design Tasks, Implementation Tasks (+11 more)

### Community 148 - "shadcn/ui Theming & Customization"
Cohesion: 0.10
Nodes (19): Base Color Presets, Best Practices, Color Customization, Color Format, Component Customization, CSS Variable System, Customize Styles, Customize Variants (+11 more)

### Community 149 - "Asset Organization Guide"
Cohesion: 0.11
Nodes (18): Asset Entry (manifest.json), Asset Organization Guide, By Campaign, By Status, By Type, Cleanup Workflow, Components, Directory Structure (+10 more)

### Community 150 - "Primary Color Meanings"
Cohesion: 0.11
Nodes (18): Accessibility Considerations, Analogous, Black, Blue, Color Combinations by Industry, Color Harmony Types, Complementary, Green (+10 more)

### Community 151 - "Core Logo Types"
Cohesion: 0.11
Nodes (18): 1. Wordmark (Logotype), 2. Lettermark (Monogram), 3. Pictorial Mark (Brand Mark), 4. Abstract Mark, 5. Mascot, 6. Emblem, 7. Combination Mark, Aesthetic Styles (+10 more)

### Community 152 - "Brand Consistency Checklist"
Cohesion: 0.11
Nodes (17): Audit Frequency, Brand Consistency Checklist, Channel Audit, Collateral, Colors, Common Issues, Email, Imagery (+9 more)

### Community 153 - "CIP Mockup Prompt Engineering"
Cohesion: 0.11
Nodes (17): Apparel (Polo/T-Shirt), Base Prompt Structure, Business Card, CIP Mockup Prompt Engineering, Context Modifiers, Corporate Minimal, Deliverable-Specific Modifiers, Letterhead (+9 more)

### Community 154 - "Color Semantics"
Cohesion: 0.11
Nodes (17): Accent, Applying Semantic Tokens, Background & Foreground, Border & Ring, Color Semantics, Dark Mode Overrides, Destructive, Interactive States (+9 more)

### Community 155 - "Design Principles"
Cohesion: 0.12
Nodes (15): 22 Art Direction Styles, Banner Sizes & Art Direction Styles Reference, Complete Banner Sizes, CTA Rules, Design Principles, Pinterest Research Queries, Print, Print Specs (+7 more)

### Community 156 - "Design Principles"
Cohesion: 0.12
Nodes (15): 22 Art Direction Styles, Banner Sizes & Art Direction Styles Reference, Complete Banner Sizes, CTA Rules, Design Principles, Pinterest Research Queries, Print, Print Specs (+7 more)

### Community 157 - "CIP Design Reference"
Cohesion: 0.13
Nodes (14): CIP Brief (Start Here), CIP Design Reference, Commands, Deliverable Categories, Design Styles, Detailed References, Generate Mockups, HTML Presentation Features (+6 more)

### Community 158 - "Icon Design Reference"
Cohesion: 0.13
Nodes (14): Available Styles, CLI Options, Commands, Generate Batch Variations, Generate Multiple Sizes, Generate Single Icon, Icon Categories, Icon Design Reference (+6 more)

### Community 159 - "Copywriting Formulas"
Cohesion: 0.13
Nodes (14): AIDA (Attention-Interest-Desire-Action), Before-After-Bridge, Contrast Patterns, Copywriting Formulas, Core Formulas, Cost of Inaction, FAB (Features-Advantages-Benefits), Formula-to-Slide Mapping (+6 more)

### Community 160 - "Copywriting Formulas"
Cohesion: 0.13
Nodes (14): AIDA (Attention-Interest-Desire-Action), Before-After-Bridge, Contrast Patterns, Copywriting Formulas, Core Formulas, Cost of Inaction, FAB (Features-Advantages-Benefits), Formula-to-Slide Mapping (+6 more)

### Community 161 - "Banner Design - Multi-Format Creative Banner System"
Cohesion: 0.14
Nodes (13): Art Direction Styles (Top 10), Banner Design - Multi-Format Creative Banner System, Banner Size Quick Reference, Design Rules, Prerequisites, Security, Step 1: Gather Requirements (AskUserQuestion), Step 2: Research & Art Direction (+5 more)

### Community 162 - "Messaging Framework"
Cohesion: 0.14
Nodes (13): Core Statements, Elevator Pitches, Framework Structure, Message Architecture, Message by Audience, Message Testing, Messaging Framework, Mission Statement (+5 more)

### Community 163 - "Brand Voice Framework"
Cohesion: 0.14
Nodes (13): Brand Voice Framework, Character Spectrum, Emotion Spectrum, Language Spectrum, Step 1: Define Personality Traits, Step 2: Create Voice Chart, Step 3: Context Adaptation, Tone Spectrum (+5 more)

### Community 164 - "Layout Patterns"
Cohesion: 0.14
Nodes (13): Card Styles, Component Variants, CSS Structures, Feature Grid (3 columns), Layout Decision Flow, Layout Patterns, Layout Selection by Use Case, Metric Styles (+5 more)

### Community 165 - "Tailwind Integration"
Cohesion: 0.14
Nodes (13): Animation Tokens, Base Layer, Button Example, Component Classes, CSS Variables Setup, Dark Mode Toggle, HSL Format Benefits, shadcn/ui Alignment (+5 more)

### Community 166 - "Layout Patterns"
Cohesion: 0.14
Nodes (13): Card Styles, Component Variants, CSS Structures, Feature Grid (3 columns), Layout Decision Flow, Layout Patterns, Layout Selection by Use Case, Metric Styles (+5 more)

### Community 167 - "PRD: Sistem Booking Salon Kecantikan "Eva Salon" (Satu Cabang)"
Cohesion: 0.14
Nodes (13): 10. Notifikasi WhatsApp (Tahap Awal), 11. Analisis Risiko, 12. Pertanyaan Terbuka (perlu dijawab owner), 14. Definisi Istilah, 1. Ringkasan, 2. Latar Belakang dan Masalah, 3. Tujuan dan Metrik Keberhasilan, 4. Pengguna dan Peran (+5 more)

### Community 168 - "update.md"
Cohesion: 0.15
Nodes (12): Color Presets, Examples, Files Modified, Important, Overview, Skills Used, Step 1: Gather Brand Input, Step 2: Update Brand Guidelines (+4 more)

### Community 169 - "Logo Design Reference"
Cohesion: 0.15
Nodes (12): Available Styles, Color Psychology, Commands, Design Brief (Start Here), Detailed References, Generate Logo, Industry Defaults, Logo Design Reference (+4 more)

### Community 170 - "Token Architecture"
Cohesion: 0.15
Nodes (12): Categories, Dark Mode, File Organization, Layer 1: Primitive Tokens, Layer 2: Semantic Tokens, Layer 3: Component Tokens, Layer Overview, Migration from Flat Tokens (+4 more)

### Community 171 - "Primitive Tokens"
Cohesion: 0.17
Nodes (11): Border Radius, Color Scales, Gray Scale, Motion / Duration, Primary Colors (Blue), Primitive Tokens, Shadows, Spacing Scale (+3 more)

### Community 172 - "Core Visual Elements"
Cohesion: 0.18
Nodes (10): Color Palette, Colors, Core Visual Elements, Logo, Logo, Quick Checks, Typography, Typography (+2 more)

### Community 173 - "CIP Design Style Guide"
Cohesion: 0.18
Nodes (10): Bold Dynamic, CIP Design Style Guide, Classic Traditional, Color Psychology, Corporate Minimal, Fresh Modern, Luxury Premium, Modern Tech (+2 more)

### Community 174 - "md"
Cohesion: 0.67
Nodes (4): $type, $value, md, md

### Community 175 - "Wireframe Sistem Booking Salon"
Cohesion: 0.18
Nodes (10): Alur yang perlu dipastikan, B. Wireframe 1: Walk-in (prioritas tertinggi), C. Wireframe 2: Tahan Slot via Telepon, Catatan desain, Catatan desain, Catatan desain, D. Wireframe 3: Kalender Harian Kasir, E. Wireframe 4: Form Booking Publik (+2 more)

### Community 176 - "Brand"
Cohesion: 0.20
Nodes (9): Brand, Brand Sync Workflow, Quick Start, References, Routing, Scripts, Subcommands, Templates (+1 more)

### Community 177 - "Slide Strategies"
Cohesion: 0.20
Nodes (9): Common Structures, Duarte Sparkline Pattern, Matching Strategy to Context, Product Demo (6 slides), Sales Pitch (9 slides), Search Commands, Slide Strategies, Strategy Selection (+1 more)

### Community 178 - "Component Tokens"
Cohesion: 0.20
Nodes (9): Alert Tokens, Badge Tokens, Button Tokens, Card Tokens, Component Tokens, Dialog/Modal Tokens, Input Tokens, Table Tokens (+1 more)

### Community 179 - "Slide Strategies"
Cohesion: 0.20
Nodes (9): Common Structures, Duarte Sparkline Pattern, Matching Strategy to Context, Product Demo (6 slides), Sales Pitch (9 slides), Search Commands, Slide Strategies, Strategy Selection (+1 more)

### Community 180 - "6.5 Dashboard Admin (kasir dan owner)"
Cohesion: 0.20
Nodes (10): 6.5 Dashboard Admin (kasir dan owner), a. Kalender dan daftar booking, b. Walk-in (prioritas tertinggi), c. Konfirmasi dan status, d. Pembayaran sederhana, e. Kelola layanan (owner), f. Kelola staf dan jadwal (owner), g. Data pelanggan (+2 more)

### Community 182 - "test_design_system_mode.py"
Cohesion: 0.15
Nodes (7): _contrast_ratio(), _derive_dark_palette(), _palette_is_dark(), _relative_luminance(), _select_palette_for_mode(), TestLuminance, TestPaletteSelection

### Community 183 - "Ponytail Help"
Cohesion: 0.25
Nodes (7): Configure Default Mode, Deactivate, Levels, More, Ponytail Help, Skills, Update

### Community 184 - "Slides Reference"
Cohesion: 0.29
Nodes (6): Key Features, Knowledge Base, Slides Reference, Usage, When to Use, Workflow

### Community 185 - "HTML Slide Template"
Cohesion: 0.29
Nodes (6): Animation Classes, Background Images, Base Structure, Chart.js Integration, CSS Variables Reference, HTML Slide Template

### Community 186 - "HTML Slide Template"
Cohesion: 0.29
Nodes (6): Animation Classes, Background Images, Base Structure, Chart.js Integration, CSS Variables Reference, HTML Slide Template

### Community 187 - "render-html.py"
Cohesion: 0.23
Nodes (4): generate_html(), get_deliverable_info(), get_image_base64(), main()

### Community 188 - "6. Aturan Bisnis Inti (Jangan Diubah Tanpa Persetujuan)"
Cohesion: 0.33
Nodes (6): 6.1 Status booking, 6.2 Ketersediaan slot, 6.3 Hold, 6.4 Pembatalan oleh pelanggan, 6.5 Pelanggan dan privasi, 6. Aturan Bisnis Inti (Jangan Diubah Tanpa Persetujuan)

### Community 189 - "Slides"
Cohesion: 0.33
Nodes (5): References (Knowledge Base), Routing, Slides, Subcommands, When to Use

### Community 191 - "7. Aturan Bisnis"
Cohesion: 0.33
Nodes (6): 7.1 Ketersediaan slot, 7.2 Aturan "Siapa saja", 7.3 Pembatalan, 7.4 Harga dan durasi, 7.5 Zona waktu, 7. Aturan Bisnis

### Community 192 - "Brand Guidelines Template"
Cohesion: 0.40
Nodes (4): Brand Guidelines Template, Document Structure, Extractable Fields, Usage

### Community 193 - "ponytail-audit/SKILL.md"
Cohesion: 0.40
Nodes (4): 1. Map first, 2. Look for, 3. Check before you report, 4. Output

### Community 194 - "Ponytail Gain"
Cohesion: 0.40
Nodes (4): Boundaries, Honesty boundary, Ponytail Gain, Scoreboard

### Community 195 - "ponytail-review/SKILL.md"
Cohesion: 0.40
Nodes (4): 1. Understand first, 2. Look for, 3. Check before you report, 4. Output

### Community 196 - "Ponytail"
Cohesion: 0.40
Nodes (4): Before you write, Levels, Ponytail, The smallest complete change

### Community 197 - "13. Fase Pengembangan"
Cohesion: 0.40
Nodes (5): 13. Fase Pengembangan, Fase 0: Validasi (1–2 minggu), Fase 1: MVP (target 6–8 minggu), Fase 2: Penyempurnaan (berdasarkan hasil pilot), Fase 3: Pertimbangan (jika owner setuju dan skala tumbuh)

### Community 198 - "6. Kebutuhan Fungsional"
Cohesion: 0.40
Nodes (5): 6.1 Halaman Utama (publik), 6.2 Katalog Layanan (publik), 6.3 Booking Online (publik), 6.4 Cek Status Booking (publik), 6. Kebutuhan Fungsional

### Community 199 - "10. Aturan Kerja untuk Agent"
Cohesion: 0.50
Nodes (4): 10. Aturan Kerja untuk Agent, Saat tidak yakin, Yang harus dilakukan, Yang tidak boleh dilakukan

### Community 200 - "7. Standar Kode"
Cohesion: 0.50
Nodes (4): 7. Standar Kode, Frontend, Keamanan, Umum

### Community 201 - "Ponytail, lazy senior dev mode"
Cohesion: 0.50
Nodes (3): Before you write, Ponytail, lazy senior dev mode, The smallest complete change

### Community 202 - "Ponytail, lazy senior dev mode"
Cohesion: 0.50
Nodes (3): Before you write, Ponytail, lazy senior dev mode, The smallest complete change

### Community 203 - "ponytail-debt/SKILL.md"
Cohesion: 0.50
Nodes (3): Boundaries, Output, Scan

### Community 206 - "9. Arsitektur Teknis"
Cohesion: 0.50
Nodes (4): 9.1 Stack, 9.2 Lingkungan, 9.3 Batasan hosting dan dampaknya, 9. Arsitektur Teknis

### Community 207 - "5. Ruang Lingkup"
Cohesion: 0.67
Nodes (3): 5. Ruang Lingkup, Dalam cakupan (MVP), Di luar cakupan (MVP)

## Knowledge Gaps
- **31 isolated node(s):** `Customer`, `Kasir`, `Owner`, `System`, `Online` (+26 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 1783 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **62 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `ShadcnInstaller` connect `ShadcnInstaller` to `json`?**
  _High betweenness centrality (0.013) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `TailwindConfigGenerator` (e.g. with `TestGeneratedConfigIsValidJs` and `TestTailwindConfigGenerator`) actually correct?**
  _`TailwindConfigGenerator` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Customer`, `Kasir`, `Owner` to the rest of the system?**
  _31 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `ShadcnInstaller` be split into smaller, more focused modules?**
  _Cohesion score 0.053555750658472345 - nodes in this community are weakly interconnected._
- **Why does `Booking` connect `Booking` to `agent.md: Panduan untuk AI Coding Agent`, `Tz`, `SalonProfile`, `BookingStatus`, `Illuminate\Database\Eloquent\Model`, `BookingService.php`, `Illuminate\Contracts\View\View`, `Staff`, `StatusController`?**
  _High betweenness centrality (0.011) - this node is a cross-community bridge._
- **Should `scripts/core.py` be split into smaller, more focused modules?**
  _Cohesion score 0.05563093622795115 - nodes in this community are weakly interconnected._
- **Why does `TailwindConfigGenerator` connect `TailwindConfigGenerator` to `TestTailwindConfigGenerator`, `._base_config`, `main`, `.write_config`, `json`, `.validate_config`, `.add_plugins`, `.recommend_plugins`, `.add_colors`, `.generate_config_string`?**
  _High betweenness centrality (0.008) - this node is a cross-community bridge._