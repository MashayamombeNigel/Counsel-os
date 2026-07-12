---
name: Lexis Modern
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#45474c'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#75777d'
  outline-variant: '#c5c6cd'
  surface-tint: '#545f73'
  primary: '#091426'
  on-primary: '#ffffff'
  primary-container: '#1e293b'
  on-primary-container: '#8590a6'
  inverse-primary: '#bcc7de'
  secondary: '#4648d4'
  on-secondary: '#ffffff'
  secondary-container: '#6063ee'
  on-secondary-container: '#fffbff'
  tertiary: '#1e1200'
  on-tertiary: '#ffffff'
  tertiary-container: '#35260c'
  on-tertiary-container: '#a38c6a'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d8e3fb'
  primary-fixed-dim: '#bcc7de'
  on-primary-fixed: '#111c2d'
  on-primary-fixed-variant: '#3c475a'
  secondary-fixed: '#e1e0ff'
  secondary-fixed-dim: '#c0c1ff'
  on-secondary-fixed: '#07006c'
  on-secondary-fixed-variant: '#2f2ebe'
  tertiary-fixed: '#fadfb8'
  tertiary-fixed-dim: '#ddc39d'
  on-tertiary-fixed: '#271902'
  on-tertiary-fixed-variant: '#564427'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 30px
    fontWeight: '600'
    lineHeight: 38px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  container-max: 1440px
  gutter: 24px
  margin-x: 32px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 24px
---

## Brand & Style

The design system is engineered for legal and business professionals who require a high-trust, high-efficiency environment. The brand personality is authoritative yet modern, balancing the traditional gravity of legal work with the speed of contemporary SaaS.

The visual style is **Corporate / Modern** with a strong emphasis on **Minimalism**. It utilizes expansive whitespace to reduce cognitive load during complex document reviews or case management. The interface prioritizes clarity and information density through precise alignment and a "Smooth and Modern" aesthetic, characterized by subtle transitions and a refined layering of surfaces.

## Colors

The palette is anchored by **Deep Indigo (Slate 800)** to convey stability and intelligence. A secondary **Vibrant Indigo (600)** is used sparingly for primary actions and interactive states to provide a clear focal point.

- **Primary:** `#1E293B` (Deep Navy) — Used for navigation, headers, and high-emphasis text.
- **Secondary:** `#6366F1` (Indigo) — Used for primary buttons, active links, and progress indicators.
- **Background:** `#F8FAFC` (Slate 50) — A soft, cool gray that reduces eye strain during long sessions.
- **Surface:** `#FFFFFF` (White) — Used for cards and content containers to create clear separation from the background.
- **Border:** `#E2E8F0` (Slate 200) — Low-contrast dividers to maintain structure without visual noise.

## Typography

This design system utilizes **Inter** across all levels to ensure maximum legibility and a systematic, utilitarian feel. 

Headlines use tighter letter spacing and heavier weights to establish a clear hierarchy. Body text is set with generous line heights (`1.5` to `1.6`) to improve readability for long-form legal documents. Labels use a slightly heavier weight and, in some contexts, uppercase tracking to distinguish them from interactive data points.

## Layout & Spacing

The layout follows a **Fixed Grid** model on desktop, centered within a 1440px container, and transitions to a **Fluid Grid** for tablet and mobile devices. 

- **Desktop (1280px+):** 12-column grid, 24px gutters, 32px side margins.
- **Tablet (768px - 1279px):** 8-column grid, 16px gutters, 24px side margins.
- **Mobile (<767px):** 4-column grid, 16px gutters, 16px side margins.

Spacing follows a strict 4px/8px baseline rhythm. Information density is managed through "Comfortable" padding within cards (24px) while using "Compact" padding for data tables (12px) to ensure significant amounts of information remain visible without scrolling.

## Elevation & Depth

Hierarchy is established using **Tonal Layers** and **Ambient Shadows**. This design system avoids heavy blacks, preferring shadows tinted with the primary navy color to maintain a cohesive professional look.

- **Level 0 (Background):** Slate 50. Flat.
- **Level 1 (Cards/Surface):** White. Use `shadow-sm` (subtle 1px-2px blur) to lift cards slightly from the background.
- **Level 2 (Modals/Dropdowns):** White. Use `shadow-lg` (15px-20px blur, 0.05 opacity) to indicate temporary overlay and high priority.
- **Interactive Depth:** On hover, cards may transition from `shadow-sm` to `shadow-md` to provide tactile feedback.

## Shapes

The shape language is defined by **Rounded** corners to soften the professional aesthetic and make the software feel modern and accessible.

- **Standard Components:** Buttons, input fields, and small tags use a `0.5rem` (8px) radius.
- **Large Components:** Cards and main content containers use a `1rem` (16px) radius to create a distinct, modern "app-like" feel.
- **Utility:** Avatars and icon backplates may use `full` (pill) rounding for quick visual identification.

## Components

### Buttons
- **Primary:** Deep Indigo background, white text, 8px radius. High-contrast and solid.
- **Secondary:** White background, Slate 200 border, Deep Navy text. 
- **Ghost:** No background or border, Indigo text. Used for less prominent actions.

### Input Fields
- Inputs feature a Slate 200 border that shifts to Indigo (Secondary) on focus. 
- Use a 12px vertical padding to ensure a "thick," high-quality touch target.
- Labels are always positioned above the field in `label-sm` weight.

### Cards
- White background with an 8px or 16px radius.
- Always include a subtle 1px border (`Slate 100`) in addition to the ambient shadow to ensure definition on high-brightness screens.

### Data Tables
- Header rows are Slate 50 with `label-md` typography.
- Row hover states use a very subtle Slate 50 tint.
- Borders are horizontal only to emphasize the flow of data.

### Chips & Status Indicators
- Statuses (e.g., "Pending," "Signed") use low-saturation background tints with high-saturation text of the same hue (e.g., soft emerald background with dark emerald text).
- Rounded-full (pill) shape only.