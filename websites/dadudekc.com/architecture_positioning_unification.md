# dadudekc.com – Positioning Unification Architecture

**Date**: 2025-12-17  
**Author**: Agent-2 (Architecture Review)  
**Status**: Architecture Guidance Document

---

## 🎯 Objective

Implement unified positioning line **"I build automation systems that save teams hours every week."** site-wide to create consistent messaging across all pages and touchpoints.

---

## 🏗️ Architecture Approach

### **1. Single Source of Truth (SSOT) Pattern**

**Implementation Strategy**: Create a centralized positioning constant that can be referenced across all WordPress pages, templates, and content.

#### **Option A: WordPress Theme Function (Recommended)**
```php
// In child theme functions.php or custom plugin
function dadudekc_get_positioning_line() {
    return "I build automation systems that save teams hours every week.";
}

function dadudekc_get_positioning_tagline() {
    return "Automation systems that save teams hours every week.";
}
```

**Usage in Templates**:
- `<?php echo dadudekc_get_positioning_line(); ?>` in header/hero sections
- `<?php echo esc_html(dadudekc_get_positioning_tagline()); ?>` in meta descriptions

**Advantages**:
- Single point of update
- Theme-level consistency
- Easy to A/B test variations
- Works with block editor and classic editor

#### **Option B: WordPress Customizer Setting**
- Add to `Appearance → Customize → Site Identity`
- Store in `wp_options` table
- Accessible via `get_theme_mod('dadudekc_positioning_line')`

**Advantages**:
- Non-technical updates possible
- Visible in WordPress admin
- Can be previewed before publishing

---

### **2. Content Injection Points**

#### **Priority 1: High-Visibility Locations**
1. **Homepage Hero Section**
   - Replace current manifesto-style copy with unified positioning
   - Use as primary headline or subheadline
   - **Status**: ✅ Already implemented in CTA section (partial)

2. **Site Header/Tagline**
   - WordPress site tagline (Settings → General)
   - Theme header widget area
   - Meta description (SEO)

3. **About Page**
   - Opening paragraph
   - Bio section introduction

#### **Priority 2: Supporting Locations**
4. **Services Page**
   - Section introduction
   - Each service card description

5. **Contact Page**
   - Form introduction text
   - CTA button context

6. **Blog Post Meta**
   - Author bio snippet
   - Related posts section

---

### **3. Implementation Phases**

#### **Phase 1: Core Pages (Week 1)**
- [ ] Homepage hero section
- [ ] Site tagline (WordPress Settings)
- [ ] About page opening
- [ ] Services page header

**Method**: Direct content updates via WP-CLI or REST API

#### **Phase 2: Template Integration (Week 2)**
- [ ] Add positioning function to child theme `functions.php`
- [ ] Update header.php template
- [ ] Update footer.php template (if applicable)
- [ ] Add to block patterns (Gutenberg)

**Method**: Theme file updates via SFTP/WP-CLI

#### **Phase 3: Content Audit & Update (Week 3)**
- [ ] Scan all pages for conflicting positioning
- [ ] Update blog post excerpts
- [ ] Update meta descriptions
- [ ] Update social media previews

**Method**: Automated script + manual review

---

### **4. Content Strategy**

#### **Unified Positioning Line**
**Primary**: "I build automation systems that save teams hours every week."

**Variations** (for context-specific use):
- **Short**: "Automation systems that save teams hours every week."
- **Question**: "Need automation systems that save teams hours every week?"
- **Action**: "Let's build automation systems that save your team hours every week."

#### **Positioning Hierarchy**
1. **Primary**: Unified positioning line (hero/above fold)
2. **Secondary**: Specific offer/outcome (below fold)
3. **Tertiary**: Proof/social proof (testimonials, case studies)

---

### **5. Technical Implementation**

#### **WordPress Block Editor (Gutenberg)**
- Create reusable block pattern with positioning line
- Store in theme `patterns/` directory
- Make available in block inserter

#### **Classic Editor / Custom HTML**
- Use shortcode: `[dadudekc_positioning]`
- Or PHP function call in template

#### **SEO Integration**
- Update meta descriptions site-wide
- Add to Open Graph tags
- Include in schema.org markup (Person/Service)

---

### **6. Conflict Resolution**

#### **Developer Tools vs Consulting Positioning**
**Strategy**: Separate but unified
- **Consulting pages**: "I build automation systems that save teams hours every week."
- **Developer Tools pages**: "Tools I've built to save teams hours every week."
- **Unified element**: Both emphasize "save teams hours every week"

#### **Navigation Simplification**
- Remove "Developer Tools" from primary nav
- Keep as secondary/labs section
- Redirect `/developer-tools` → `/services` (301) or `/labs`

---

### **7. Measurement & Validation**

#### **Pre-Implementation Audit**
- [ ] Document current positioning on all pages
- [ ] Identify conflicting messaging
- [ ] List all touchpoints (pages, posts, widgets)

#### **Post-Implementation Validation**
- [ ] Verify positioning line appears on all priority pages
- [ ] Check for remaining conflicts
- [ ] Test mobile/desktop rendering
- [ ] Validate SEO meta descriptions

---

## 📋 Implementation Checklist

### **Immediate Actions (Agent-2)**
- [x] Architecture guidance document created
- [ ] Create positioning function in child theme
- [ ] Update homepage hero section
- [ ] Update site tagline (WordPress Settings)

### **Coordination Handoff (Agent-4)**
- [ ] Review architecture approach
- [ ] Implement Phase 1 (Core Pages)
- [ ] Create reusable block pattern
- [ ] Update About/Services pages

### **Integration Checkpoints**
- **Checkpoint 1**: After Phase 1 completion (verify homepage, tagline, About)
- **Checkpoint 2**: After Phase 2 completion (verify template integration)
- **Checkpoint 3**: After Phase 3 completion (verify content audit)

---

## 🎯 Success Criteria

✅ Unified positioning line appears on:
- Homepage hero section
- Site tagline
- About page opening
- Services page header
- Contact page introduction

✅ No conflicting positioning messaging remains

✅ SEO meta descriptions updated site-wide

✅ Navigation simplified (Developer Tools removed from primary nav)

---

## 📝 Notes

- **WordPress Theme**: `accounting-grove` (parent) + child theme
- **Current CTA**: Already includes positioning line in CTA section
- **Next Step**: Implement Phase 1 (Core Pages) via WP-CLI/REST API

---

🐝 **WE. ARE. SWARM. ⚡🔥**




