# Digital Dreamscape Visual Grammar

> "You're not browsing a blog. You're **entering a world state**."

## Core Philosophy

The Digital Dreamscape is a living system. The design must reflect this through:

- **Dark-first**: A night world that rewards deep exploration
- **Living elements**: Systems that pulse, glow, and respond
- **Layered transparency**: Looking through multiple realities
- **Artifact permanence**: Nothing disappears, everything becomes terrain

---

## Color Palette: The Dreamscape Spectrum

### Primary World Colors
```css
/* The void - where everything begins */
--void-black: #0a0a0f;          /* Deep space black */
--void-dark: #1a1a2e;           /* Layered darkness */
--void-surface: #2a2a4e;        /* Emergent surfaces */

/* Energy spectrum - what powers the world */
--energy-primary: #6366f1;      /* System blue - agents & logic */
--energy-secondary: #8b5cf6;    /* Reality purple - architecture */
--energy-tertiary: #ec4899;     /* Will pink - sovereignty */

/* State indicators - world conditions */
--state-alive: #10b981;         /* Living systems */
--state-warning: #f59e0b;       /* Unstable processes */
--state-danger: #ef4444;        /* Breaking systems */
--state-ruins: #6b7280;         /* Failed experiments */
```

### Layer-Specific Colors

#### Layer 1: Surface (What Users See)
- Background: `--void-black` to `--void-dark` gradients
- Accents: `--energy-primary` glows
- Text: High contrast for readability

#### Layer 2: Systems (The Machines)
- Background: `--void-dark` with `--energy-primary` accents
- Borders: `--energy-primary` with glow effects
- Indicators: Pulsing `--state-alive` for active systems

#### Layer 3: Archive (The Ruins)
- Background: `--void-surface` with `--state-ruins` tints
- Accents: Subdued `--energy-secondary`
- States: `--state-warning` for deprecated, `--state-danger` for broken

#### Layer 4: Will (The Engine)
- Background: Deep `--void-black` with `--energy-tertiary` highlights
- Accents: `--energy-tertiary` for sovereign elements
- Effects: Subtle pulsing for "pressure" and "intent"

---

## Typography: Terminal Consciousness

### Primary Typeface Stack
```css
--font-terminal: 'JetBrains Mono', 'Fira Code', 'SF Mono', monospace;
--font-narrative: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
--font-display: 'Space Grotesk', 'Inter', sans-serif;
```

### Typographic Hierarchy

#### Terminal (Interface Elements)
- Navigation, labels, status indicators
- `font-family: var(--font-terminal)`
- `letter-spacing: 0.05em`
- `text-transform: uppercase`

#### Narrative (Content)
- Body text, descriptions, lore
- `font-family: var(--font-narrative)`
- `line-height: 1.6`
- Natural letter spacing

#### Display (World Elements)
- Headlines, artifact names, quest titles
- `font-family: var(--font-display)`
- `font-weight: 700-900`
- Dramatic letter spacing

### Text States
- **Active**: Full opacity, energy color accents
- **Inactive**: 60% opacity, void colors
- **Sacred**: Energy glow effects, full contrast
- **Ruined**: 40% opacity, ruins color tint

---

## Sacred Elements: What Demands Attention

### Primary Sacred Objects
1. **The Sovereign Avatar** - Always glowing, central positioning
2. **Active Quest Indicators** - Pulsing with world energy
3. **System Status** - "SYSTEM ACTIVE" badge
4. **Layer Transitions** - Borders between realities

### Sacred Interactions
- Hover states reveal hidden layers
- Click reveals deeper system information
- Sacred elements have persistent glow
- They respond to "will" - user intent changes their state

### Sacred Animations
```css
/* Sovereign presence - always slightly animated */
.sovereign-element {
    animation: sovereign-pulse 4s ease-in-out infinite;
}

/* System breathing - alive but not distracting */
.system-element {
    animation: system-breathe 8s ease-in-out infinite;
}

/* Quest calling - demands attention */
.active-quest {
    animation: quest-call 3s ease-in-out infinite;
}
```

---

## Noise vs Signal: World Information Hierarchy

### Signal (What Matters)
- **Quest status changes**
- **System state transitions**
- **New artifacts created**
- **Sovereign decisions**
- **Agent actions completed**

### Noise (Background Activity)
- Routine system operations
- Minor agent coordination
- Archive entries without active quests
- Deprecated systems (unless revived)

### Visual Noise Reduction
- **Opacity layers**: Signal at 100%, noise at 40-60%
- **Color saturation**: Signal fully saturated, noise desaturated
- **Animation intensity**: Signal has dramatic effects, noise subtle
- **Size hierarchy**: Signal elements larger, noise smaller

---

## Surface Patterns: The World Texture

### Primary Patterns
```css
/* Grid of possibility - where new things emerge */
.grid-pattern {
    background-image:
        linear-gradient(rgba(var(--energy-primary-rgb), 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(var(--energy-primary-rgb), 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

/* Circuit traces - showing system connections */
.circuit-pattern {
    background-image: url('data:image/svg+xml,<svg...>');
    opacity: 0.3;
}

/* Data flow - active information streams */
.data-flow {
    background: linear-gradient(45deg,
        transparent 30%,
        rgba(var(--energy-primary-rgb), 0.1) 50%,
        transparent 70%);
    animation: data-stream 3s linear infinite;
}
```

### Layer-Specific Textures

#### Surface Layer
- Clean grids, subtle circuits
- High contrast, readable

#### System Layer
- Active data flows, pulsing circuits
- Medium contrast, functional

#### Archive Layer
- Faded patterns, dust effects
- Low contrast, mysterious

#### Will Layer
- Minimal patterns, pure energy
- High contrast, commanding

---

## Component Library: World Building Blocks

### Artifact Cards (Posts/Episodes)
```css
.artifact-card {
    /* Looks like a physical object in the world */
    background: var(--void-surface);
    border: 1px solid rgba(var(--energy-primary-rgb), 0.3);
    border-radius: 8px;
    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.3),
        0 0 20px rgba(var(--energy-primary-rgb), 0.1);

    /* Artifact feels physical */
    transform-style: preserve-3d;
    transition: transform 0.3s ease;

    &:hover {
        transform: translateY(-4px) rotateX(5deg);
        box-shadow:
            0 8px 32px rgba(0, 0, 0, 0.4),
            0 0 40px rgba(var(--energy-primary-rgb), 0.2);
    }
}
```

### Navigation Portals (Links/Categories)
```css
.navigation-portal {
    /* Feels like entering different world areas */
    background: linear-gradient(135deg,
        rgba(var(--energy-primary-rgb), 0.1),
        rgba(var(--energy-secondary-rgb), 0.1));
    border: 2px solid transparent;
    border-radius: 12px;

    /* Portal activation */
    &:hover {
        border-color: var(--energy-primary);
        box-shadow: 0 0 30px rgba(var(--energy-primary-rgb), 0.3);
        transform: scale(1.02);
    }

    /* Active portal state */
    &.active {
        background: linear-gradient(135deg,
            rgba(var(--energy-primary-rgb), 0.2),
            rgba(var(--energy-secondary-rgb), 0.2));
        border-color: var(--energy-primary);
        box-shadow: 0 0 40px rgba(var(--energy-primary-rgb), 0.4);
    }
}
```

### Status Indicators (System Health)
```css
.status-indicator {
    /* Living system indicators */
    &.alive {
        background: var(--state-alive);
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        animation: pulse 2s infinite;
    }

    &.warning {
        background: var(--state-warning);
        box-shadow: 0 0 20px rgba(245, 158, 11, 0.4);
        animation: warning-flash 3s infinite;
    }

    &.danger {
        background: var(--state-danger);
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.4);
        animation: danger-pulse 1s infinite;
    }

    &.ruins {
        background: var(--state-ruins);
        opacity: 0.6;
        filter: grayscale(0.8);
    }
}
```

---

## Interaction States: World Responsiveness

### Hover States
- **Surface elements**: Subtle lift and glow
- **System elements**: Circuit activation animation
- **Archive elements**: Dust clearing effect
- **Will elements**: Energy surge response

### Active States
- **Selected portals**: Full energy activation
- **Active quests**: Persistent glow and animation
- **System interactions**: Circuit flow animations

### Sacred Interactions
- **Sovereign elements**: Always responsive, always glowing
- **Quest creation**: Dramatic energy release
- **System stabilization**: Satisfying resolution animation

---

## Responsive Design: World Across Devices

### Mobile (Surface Layer Focus)
- Single column, stacked layers
- Touch interactions feel like "pressing" world objects
- Simplified navigation portals

### Tablet (System Layer Access)
- Two-column layouts reveal system connections
- Swipe gestures navigate between layers
- Compact but functional artifact cards

### Desktop (Full World Immersion)
- Multi-column grids show world scale
- Hover reveals layer depths
- Complex navigation shows all portals

---

## Implementation Priority

1. **Colors & Typography** - Foundation of the world
2. **Sacred Elements** - What demands attention
3. **Component Library** - Reusable world objects
4. **Layer Transitions** - Moving between realities
5. **Responsive Adaptation** - World works everywhere

---

## Testing the Grammar

Every design decision should answer:
- Does this feel like a **living system** or a static website?
- Does this reward **consistent builders** or casual visitors?
- Does this make **effort permanent** or easily forgotten?
- Does this feel like **entering a world** or browsing content?

If the answer is "static website", "casual visitors", "easily forgotten", or "browsing content" - it's not Dreamscape enough.