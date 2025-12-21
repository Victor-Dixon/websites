# Standardized Blog Post Template

**Based on**: Analysis of published blog posts on dadudekc.com  
**Last Updated**: 2025-12-13  
**Author**: Agent-7 (Web Development Specialist)

## Template Structure

This template standardizes all blog posts with consistent styling, structure, and formatting.

---

## Full Template

```markdown
# [Blog Post Title]

<div style="max-width: 800px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.7; color: #333;">

<!-- HERO SECTION -->
<div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 3rem 2rem; border-radius: 12px; color: white; margin: 2rem 0; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
<h1 style="color: white; margin: 0 0 1rem 0; font-size: 2.5em; font-weight: 700; line-height: 1.2;">[Main Title with Emoji]</h1>
<p style="font-size: 1.3em; margin: 0; opacity: 0.95; font-weight: 300;">[Compelling subtitle that explains what this post is about]</p>
</div>

<!-- INTRODUCTION -->
[Your introduction paragraph goes here. This should hook the reader and provide context for what they're about to read. Use regular markdown formatting.]

## Section Heading

[Regular content paragraphs. Keep them focused and well-structured. Use markdown for formatting.]

<!-- HIGHLIGHTED SECTION -->
<div style="background: #f8f9fa; border-left: 5px solid #2a5298; padding: 2rem; margin: 2rem 0; border-radius: 8px;">
<h2 style="color: #2a5298; margin-top: 0; font-size: 1.75em;">Section Title</h2>
<p style="font-size: 1.1em; margin-bottom: 0; line-height: 1.8; color: #2d3748;">Important content that needs emphasis. This style is perfect for key concepts, philosophies, or core ideas.</p>
</div>

## Card Grid Section

[Introduction text before the cards]

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin: 2.5rem 0;">
  
<div style="background: white; border: 2px solid #667eea; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
<h3 style="color: #667eea; margin-top: 0; font-size: 1.3em;">[Card Title with Emoji]</h3>
<p style="margin: 0.5rem 0; font-weight: 600; color: #2d3748;">[Subtitle or Category]</p>
<p style="margin: 0; color: #4a5568; font-size: 0.95em;">[Card description or content]</p>
</div>

<div style="background: white; border: 2px solid #764ba2; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
<h3 style="color: #764ba2; margin-top: 0; font-size: 1.3em;">[Card Title with Emoji]</h3>
<p style="margin: 0.5rem 0; font-weight: 600; color: #2d3748;">[Subtitle or Category]</p>
<p style="margin: 0; color: #4a5568; font-size: 0.95em;">[Card description or content]</p>
</div>

<div style="background: white; border: 2px solid #f093fb; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
<h3 style="color: #f093fb; margin-top: 0; font-size: 1.3em;">[Card Title with Emoji]</h3>
<p style="margin: 0.5rem 0; font-weight: 600; color: #2d3748;">[Subtitle or Category]</p>
<p style="margin: 0; color: #4a5568; font-size: 0.95em;">[Card description or content]</p>
</div>

</div>

## Callout Section

<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 12px; color: white; margin: 2.5rem 0;">
<h2 style="color: white; margin-top: 0; font-size: 1.75em;">Important Information</h2>
<p style="font-size: 1.1em; margin-bottom: 1rem; line-height: 1.8; opacity: 0.95;">Use this style for important callouts, key takeaways, or highlighted information.</p>
<ul style="font-size: 1.05em; line-height: 1.8; margin: 0;">
<li><strong>Key Point 1</strong>: Description of the first key point</li>
<li><strong>Key Point 2</strong>: Description of the second key point</li>
<li><strong>Key Point 3</strong>: Description of the third key point</li>
</ul>
</div>

## Regular Content Section

[Use standard markdown for regular content sections. Headings, paragraphs, lists, code blocks, etc.]

### Subsection

[Subsection content with regular markdown formatting]

## Conclusion

<div style="background: #f7fafc; border-left: 5px solid #2a5298; padding: 2rem; margin: 2.5rem 0; border-radius: 8px;">
<p style="font-size: 1.1em; margin: 0; line-height: 1.8; color: #2d3748;">Your conclusion paragraph. Summarize key points and provide a clear takeaway for readers.</p>
</div>

</div>
```

---

## Component Reference

### 1. Hero Section
**Use for**: Main title and subtitle at the top of the post

```html
<div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 3rem 2rem; border-radius: 12px; color: white; margin: 2rem 0; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
<h1 style="color: white; margin: 0 0 1rem 0; font-size: 2.5em; font-weight: 700; line-height: 1.2;">[Title]</h1>
<p style="font-size: 1.3em; margin: 0; opacity: 0.95; font-weight: 300;">[Subtitle]</p>
</div>
```

### 2. Highlighted Section
**Use for**: Important concepts, philosophies, or key information

```html
<div style="background: #f8f9fa; border-left: 5px solid #2a5298; padding: 2rem; margin: 2rem 0; border-radius: 8px;">
<h2 style="color: #2a5298; margin-top: 0; font-size: 1.75em;">[Section Title]</h2>
<p style="font-size: 1.1em; margin-bottom: 0; line-height: 1.8; color: #2d3748;">[Content]</p>
</div>
```

### 3. Card Grid
**Use for**: Multiple related items (features, agents, points, etc.)

```html
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin: 2.5rem 0;">
<div style="background: white; border: 2px solid #667eea; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
<h3 style="color: #667eea; margin-top: 0; font-size: 1.3em;">[Title]</h3>
<p style="margin: 0.5rem 0; font-weight: 600; color: #2d3748;">[Subtitle]</p>
<p style="margin: 0; color: #4a5568; font-size: 0.95em;">[Description]</p>
</div>
</div>
```

**Card Border Colors** (use different colors for visual variety):
- `#667eea` - Purple
- `#764ba2` - Dark Purple
- `#f093fb` - Pink
- `#4facfe` - Blue
- `#43e97b` - Green
- `#fa709a` - Coral
- `#f59e0b` - Amber/Orange (better contrast than yellow)
- `#30cfd0` - Cyan

### 4. Callout Box
**Use for**: Important announcements, key takeaways, or highlighted lists

```html
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 12px; color: white; margin: 2.5rem 0;">
<h2 style="color: white; margin-top: 0; font-size: 1.75em;">[Title]</h2>
<p style="font-size: 1.1em; margin-bottom: 1rem; line-height: 1.8; opacity: 0.95;">[Content]</p>
<ul style="font-size: 1.05em; line-height: 1.8; margin: 0;">
<li><strong>[Item]</strong>: [Description]</li>
</ul>
</div>
```

### 5. Conclusion Box
**Use for**: Final summary or call-to-action

```html
<div style="background: #f7fafc; border-left: 5px solid #2a5298; padding: 2rem; margin: 2.5rem 0; border-radius: 8px;">
<p style="font-size: 1.1em; margin: 0; line-height: 1.8; color: #2d3748;">[Conclusion text]</p>
</div>
```

---

## Color Palette

### Primary Colors
- **Primary Blue**: `#2a5298` (headings, accents, borders)
- **Hero Gradient Start**: `#1e3c72`
- **Hero Gradient End**: `#2a5298`
- **Callout Gradient Start**: `#667eea`
- **Callout Gradient End**: `#764ba2`

### Text Colors
- **Body Text**: `#333` or `#2d3748`
- **Secondary Text**: `#4a5568`
- **White Text**: `white` (for gradients)

### Background Colors
- **Section Background**: `#f8f9fa`
- **Conclusion Background**: `#f7fafc`
- **Card Background**: `white`

### Border Colors
- **Primary Border**: `#2a5298`
- **Card Borders**: Various (see Card Grid section)
- **Subtle Border**: `#e2e8f0`

---

## Typography Scale

- **H1 (Hero)**: `2.5em` (40px) - `font-weight: 700`
- **H2**: `1.75em` (28px) - `color: #2a5298`
- **H3**: `1.3em` (21px) - Card titles
- **Body Large**: `1.1em` (18px) - Highlighted content
- **Body**: `1em` (16px) - Base text
- **Body Small**: `0.95em` (15px) - Card descriptions
- **Subtitle**: `1.3em` (21px) - Hero subtitle

---

## Spacing Guidelines

- **Section Margin**: `2rem` to `2.5rem` (32px to 40px)
- **Card Padding**: `1.5rem` to `2rem` (24px to 32px)
- **Card Gap**: `1.5rem` (24px)
- **Hero Padding**: `3rem 2rem` (48px vertical, 32px horizontal)
- **Border Radius**: `8px` to `12px`

---

## Best Practices

1. **Always start with the wrapper div** with max-width and font settings
2. **Use Hero Section** for the main title and subtitle
3. **Mix regular markdown** with styled HTML components
4. **Use Card Grids** for 3+ related items
5. **Use Highlighted Sections** for important concepts
6. **Use Callout Boxes** for key takeaways or lists
7. **End with Conclusion Box** for final summary
8. **Maintain consistent spacing** between sections
9. **Use emojis sparingly** but consistently (especially in titles)
10. **Test on mobile** - grid layouts are responsive

---

## Quick Start Checklist

- [ ] Add wrapper div with max-width and font settings
- [ ] Create Hero Section with title and subtitle
- [ ] Write introduction paragraph
- [ ] Add main content sections (use markdown)
- [ ] Use Highlighted Sections for key concepts
- [ ] Use Card Grids for multiple items
- [ ] Use Callout Boxes for important information
- [ ] Add Conclusion Box at the end
- [ ] Review spacing and consistency
- [ ] Test on mobile viewport

---

## Example Usage

See `docs/blog/introducing_the_swarm.md` for a complete example using this template.

---

**Template Version**: 1.0  
**Last Validated**: 2025-12-13  
**Based on**: Analysis of published blog posts on dadudekc.com

