# 🎭 Devlog Promotion Guide

## How Devlogs Become Episodes in the World Archive

### The Process

1. **Write devlog** in Markdown with optional frontmatter
2. **Run promotion command** to transform it into a WordPress post
3. **Episode appears** in World Archive with full metadata
4. **Questline updates** automatically
5. **Archive stats refresh** to show new artifact

### Example Devlog Structure

```markdown
# Episode Title Here

**Date**: 2026-01-03
**Questline**: technical-debt
**Status**: completed

---

## Section 1

Content here...

## Section 2

More content...

---

**Technical Notes**
- Point 1
- Point 2

---

*Part of the questline-name questline*
*Agent-X operation log*
```

### Promotion Command

```bash
# From your WordPress root directory
php promote_artifacts.php devlog path/to/devlog.md
```

### What Gets Created

#### WordPress Post
- **Title**: Extracted from first `#` heading
- **Content**: Full Markdown content
- **Excerpt**: First paragraph after title
- **Status**: Published
- **Type**: Post with custom fields

#### Custom Field Metadata
```php
[
    'artifact_type' => 'episode',
    'questline' => 'technical-debt',        // From frontmatter or content
    'artifact_state' => 'active',           // Default for episodes
    'era' => '2026',                        // From date
    'source_system' => 'devlog',            // Identifies source
    'internal_source' => 'path/to/file.md', // For deduplication
    'canon_weight' => 1,                    // Episodes start at 1
    'canonical' => 'false'                  // Episodes aren't canon by default
]
```

### Archive Display

The episode appears in the World Archive as:

```
🎭 EP-145: Episode Title Here
   Questline: technical-debt
   State: active
   Era: 2026
   Updated: 2 hours ago
```

### Questline Impact

**Before promotion:**
```
Questline: technical-debt
Progress: 1/4 artifacts resolved
```

**After promotion:**
```
Questline: technical-debt
Progress: 2/5 artifacts resolved
Latest: EP-145 "Episode Title Here"
```

### Homepage Updates

Live stats automatically update:
- Episode count: +1
- Active artifacts: +1
- Questline progress bars refresh

### Filtering & Discovery

The episode becomes discoverable through:
- **Type filter**: `?type=episode`
- **Questline filter**: `?questline=technical-debt`
- **State filter**: `?state=active`
- **Search**: Full-text search across content

### URL Structure

```
/blog/episode-title-here
```

With automatic redirects and SEO metadata.

---

## 🚀 Ready to Promote Devlogs?

The system is live and ready. Every devlog you write can become part of the living Digital Dreamscape narrative.

**The world grows with every entry.** 🌌⚡🤖