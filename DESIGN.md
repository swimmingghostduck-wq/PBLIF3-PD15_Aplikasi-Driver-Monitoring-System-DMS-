---
name: Driver Monitoring System (DMS)
description: Cockpit-grade real-time driver vigilance telemetry and alert interface
colors:
  primary: "#1E40AF"
  primary-hover: "#2563EB"
  neutral-bg: "#0F172A"
  neutral-surface: "#1E293B"
  neutral-surface-subtle: "#334155"
  neutral-text: "#F8FAFC"
  neutral-text-muted: "#94A3B8"
  status-safe: "#10B981"
  status-warning: "#F59E0B"
  status-danger: "#EF4444"
  status-info: "#A855F7"
typography:
  display:
    fontFamily: "ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.875rem"
    fontWeight: 900
    lineHeight: 1.2
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.25
    letterSpacing: "-0.01em"
  title:
    fontFamily: "ui-sans-serif, system-ui, sans-serif"
    fontSize: "1rem"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "normal"
  body:
    fontFamily: "ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  label:
    fontFamily: "ui-monospace, monospace"
    fontSize: "0.75rem"
    fontWeight: 700
    lineHeight: 1
    letterSpacing: "0.05em"
rounded:
  sm: "6px"
  md: "8px"
  lg: "12px"
  xl: "16px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.neutral-text}"
    rounded: "{rounded.lg}"
    padding: "10px 16px"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
  button-danger:
    backgroundColor: "{colors.status-danger}"
    textColor: "{colors.neutral-text}"
    rounded: "{rounded.lg}"
    padding: "10px 16px"
  badge-safe:
    backgroundColor: "rgba(16, 185, 129, 0.2)"
    textColor: "{colors.status-safe}"
    rounded: "{rounded.full}"
    padding: "4px 10px"
  badge-danger:
    backgroundColor: "rgba(239, 68, 68, 0.2)"
    textColor: "{colors.status-danger}"
    rounded: "{rounded.full}"
    padding: "4px 10px"
---

# Design System: Driver Monitoring System (DMS)

## Overview

**Creative North Star: "The Avionics Cockpit"**

The visual language of the Driver Monitoring System is modeled after high-reliability modern flight deck telemetry: dark-mode by default, high-contrast, razor-sharp hierarchy, and designed for sub-second glanceable comprehension. When a vehicle is moving at highway speed, the driver's interface must not behave like a standard consumer dashboard; it must communicate status unequivocally without demanding cognitive load or causing optical fatigue under day and night lighting.

Surfaces inhabit a deep night palette dominated by Slate-900 (`#0F172A`) and Slate-800 (`#1E293B`), establishing an unobtrusive operational background. Critical states are mapped to unambiguous international aviation and automotive safety colors: Vigilant Emerald (`#10B981`) for normal operation, Cautionary Amber (`#F59E0B`) for early drift or distraction warnings, and Emergency Crimson (`#EF4444`) for critical alarms accompanied by acoustic beacons.

**Key Characteristics:**
- **Zero Ambiguity Glancability:** Telemetry metrics (EAR, MAR, Yaw, Pitch, Gaze) feature prominent numerical values paired with analog micro-bars.
- **Tonal Elevation Hierarchy:** Depth is created via layered dark slate tones with subtle luminous glowing borders on alert states, rather than heavy drop shadows.
- **Calm at Rest, Assertive on Alarm:** The UI remains subdued and non-distracting during safe driving, converting into a pulsing beacon only when safety thresholds are breached.

## Colors

The palette is engineered for high-contrast dark cockpit environments, pairing a neutral slate substrate with tactical chromatic indicators.

### Primary
- **Cobalt Cockpit Blue** (`#1E40AF`): The primary brand and action tone, used for primary navigation marks, active camera toggles, and neutral action triggers.
- **Electric Cobalt Hover** (`#2563EB`): The elevated state for interactive controls and primary highlights.

### Status Accents
- **Vigilant Emerald** (`#10B981`): Indicates safe driver condition (`TERJAGA`), verified forward gaze, and active tracking bounding boxes.
- **Cautionary Amber** (`#F59E0B`): Used for warning phases (`TIDAK_FOKUS`, `MENGUAP`, camera reconnection) where immediate correction is urged before full sirens sound.
- **Hazard Crimson** (`#EF4444`): High-alert beacon for verified drowsiness (`KANTUK`), prolonged distraction ($> 3.0$ seconds), and lost driver presence (`WAJAH_HILANG`).
- **Telemetry Purple** (`#A855F7`): Reserved for geometric head pose orientation, model metadata chips, and AI system indicators.

### Neutral
- **Deep Cockpit Obsidian** (`#0F172A`): Global canvas substrate (Slate-900). Absorbs glare and minimizes cabin reflection.
- **Instrument Panel Surface** (`#1E293B`): Card and container background (Slate-800). Provides structural contrast against the canvas.
- **Instrument Divider** (`#334155`): Subtle borders and metric separators (Slate-700).
- **Crisp Data White** (`#F8FAFC`): Primary numerical readouts, titles, and active statuses (Slate-50).
- **Telemetry Muted** (`#94A3B8`): Field labels, threshold benchmarks, and secondary metadata (Slate-400).

### Named Rules
**The Rarity of Red Rule.** The Hazard Crimson (`#EF4444`) is strictly prohibited in decorative, branding, or non-hazardous contexts. Its appearance is a direct signal of active safety intervention.
**The Ghost Border Fallback.** Surfaces separate through thin `1px border-slate-700/60` lines rather than heavy background contrast, maintaining low cockpit luminescence.

## Typography

**Display Font:** `ui-sans-serif, system-ui, -apple-system, sans-serif`
**Body Font:** `ui-sans-serif, system-ui, -apple-system, sans-serif`
**Telemetry/Mono Font:** `ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace`

**Character:** Modern, clean, and utilitarian. Proportional typography handles navigation and status text, while monospaced figures prevent jitter and shifting on real-time streaming numerical telemetry.

### Hierarchy
- **Display** (Bold 900, `1.875rem` / `30px`, line-height `1.2`): Used for large alert badges and hero KPI statistics.
- **Headline** (Bold 700, `1.5rem` / `24px`, line-height `1.25`): Card titles and primary dashboard headers.
- **Title** (Semibold 600, `1rem` / `16px`, line-height `1.4`): Section dividers and secondary modal titles.
- **Body** (Regular 400, `0.875rem` / `14px`, line-height `1.5`): Explanatory guidance, status subtitles, and helper instructions.
- **Label / Metric Mono** (Bold 700, `0.75rem` / `12px`, letter-spacing `0.05em`, uppercase): Real-time numerical coordinates (EAR, MAR, Yaw, Pitch) and uppercase section headers.

### Named Rules
**The Tabular Metric Rule.** Every real-time floating-point number (EAR, MAR, Yaw, Pitch) MUST use monospaced figures (`font-mono`) to guarantee zero layout shifts during continuous inference rendering.

## Layout

The spatial model relies on a dense, utilitarian grid with fixed proportions tailored for in-vehicle 16:9 displays and standard 640x480 webcam feeds.

- **Main Cockpit View (`driver_monitor.html`):** Split layout featuring a dominant 4:3 video canvas on the left (640px base) coupled with a high-density 320px telemetry stack on the right.
- **Admin Dashboard (`admin_dashboard.html`):** Responsive 4-column metric grid (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`) collapsing cleanly on smaller tablet displays.
- **Spacing Rhythm:** Built on an 8px base rhythm (`gap-4`, `gap-5`, `p-5`, `space-y-4`). Compact padding ensures that all critical instrumentation fits in a single viewport without vertical scrolling.

## Elevation & Depth

The system rejects simulated physical light sources or heavy dropshadows in favor of **Tonal Layering with Active Luminescence**. Depth is conveyed through luminescence stepping: `#0F172A` (lowest) $\to$ `#1E293B` (rest surface) $\to$ `#334155` (interactive border).

### Shadow Vocabulary
- **Ambient Panel Shadow** (`box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3)`): Grounding shadow for instrument cards (`shadow-xl`).
- **Emergency Beacon Glow** (`box-shadow: 0 0 25px rgba(239, 68, 68, 0.45)`): Active only during critical hazard alarm state (`is_alarm_active`), pulsing rhythmically to grab peripheral attention.
- **Cobalt Interactive Cast** (`box-shadow: 0 4px 14px rgba(30, 64, 175, 0.3)`): Subtle focus and hover illumination for operational buttons.

### Named Rules
**The Signal-Responsive Glow Rule.** Glowing colored shadows are never persistent; they exist solely as dynamic state reactions to active warnings or primary touch interactions.

## Shapes

Shapes feature precision engineered rounded corners that balance rugged technical utility with modern automotive software aesthetics.

- **Instrument Cards:** Large rounded corners (`rounded-2xl` / 16px) with distinct `1px border-slate-700` rims.
- **Buttons and Inputs:** Medium rounded corners (`rounded-xl` / 12px) for comfortable tactile and mouse interaction.
- **Chips, Pills, & Progress Bars:** Fully pill-shaped (`rounded-full` / 9999px) for continuous analog data meters and status pills.

## Components

### Buttons
- **Shape:** Rounded rectangle (`rounded-xl`, 12px).
- **Primary Action:** Cobalt Cockpit Blue (`#1E40AF`), text white, padding `10px 16px`, subtle blue hover glow.
- **Hazard Action:** Emergency Crimson (`#EF4444`), text white, used for emergency acknowledgment or session terminations.
- **Ghost / Tooling:** Slate-700 background (`#334155`), hover `bg-slate-600`, text slate-200.

### Telemetry Cards / Containers
- **Corner Style:** Rounded-2xl (16px), border `1px solid rgba(51, 65, 85, 0.8)`.
- **Background:** Deep slate (`#1E293B`), subtle dark backdrop blur.
- **Internal Padding:** `p-5` (20px) uniform.

### Progress Meters (EAR, MAR, Drowsiness, Distraction)
- **Track:** Deep obsidian gutter (`#0F172A`), height `8px` (`h-2` / `h-2.5`), `rounded-full`, overflow hidden.
- **Fill:** Smooth transitional bar (`transition-all duration-300`), dynamically colored: Emerald ($< 30\%$) $\to$ Amber ($30\% - 70\%$) $\to$ Crimson ($> 70\%$).

### Status Pill Badges
- **Safe:** `bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 rounded-2xl`.
- **Warning:** `bg-amber-500/20 text-amber-400 border border-amber-500/40 rounded-2xl`.
- **Danger:** `bg-red-500/20 text-red-500 border border-red-500/40 rounded-2xl` with pulsing animation.

### Top Alarm Banner
- **Format:** Full-width high-visibility horizontal beacon anchored directly above the video canvas.
- **Style:** `bg-red-600 text-white font-black px-4 py-3 flex items-center gap-3`, accompanied by dual-tone synthesized audio siren.

## Do's and Don'ts

### Do:
- **Do** maintain dark cockpit palette (`#0F172A` background) across all authenticated screens to preserve nighttime driver night-vision.
- **Do** format real-time metric floats with exactly three decimal places (e.g., `0.245`) inside `font-mono` containers.
- **Do** pair color changes with iconography (e.g., 🚨, 👀, 🥱, 👁️) so colorblind drivers never miss an alert state.
- **Do** ensure critical alarm sirens can be toggled or tested on-demand via audio context activation.

### Don't:
- **Don't** introduce bright white background containers in the driver cockpit interface.
- **Don't** use Red (`#EF4444`) for non-emergency UI decoration, illustrations, or secondary buttons.
- **Don't** let live video or telemetry canvas elements shift layout when values transition between two-digit and three-digit percentages.
- **Don't** use decorative serif fonts or novelty typefaces anywhere in the operational monitoring flow.

