#!/usr/bin/env python3
"""
Beautiful HTML Formatter for Digital Dreamscape Blog Posts
===========================================================

Converts markdown content to beautiful HTML that works with 
the Digital Dreamscape single.php template's card-based design.

Features:
- Proper code blocks with language classes
- HTML tables with proper structure
- Inline code styling
- Horizontal rules as section dividers
- Numbered and bullet lists

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-24
"""

import re
from typing import List, Tuple


def format_beautiful_html(content: str, include_meta: bool = True) -> str:
    """
    Convert markdown-like content to beautiful HTML for Digital Dreamscape.
    
    Args:
        content: Markdown content to convert
        include_meta: Whether to extract and format Date/Tags metadata
        
    Returns:
        Formatted HTML string
    """
    lines = content.split('\n')
    html_parts = []
    
    i = 0
    in_code_block = False
    code_language = ''
    code_content = []
    in_list = False
    list_type = None
    list_items = []
    in_table = False
    table_rows = []
    
    # First pass: extract metadata if present
    meta_date = None
    meta_tags = None
    content_start = 0
    
    for idx, line in enumerate(lines):
        if line.startswith('**Date:**') or line.startswith('Date:'):
            meta_date = re.sub(r'^\*\*Date:\*\*\s*', '', line).strip()
            meta_date = re.sub(r'^Date:\s*', '', meta_date).strip()
            content_start = max(content_start, idx + 1)
        elif line.startswith('**Tags:**') or line.startswith('Tags:'):
            meta_tags = re.sub(r'^\*\*Tags:\*\*\s*', '', line).strip()
            meta_tags = re.sub(r'^Tags:\s*', '', meta_tags).strip()
            content_start = max(content_start, idx + 1)
        elif line.startswith('**Category:**') or line.startswith('Category:'):
            content_start = max(content_start, idx + 1)
        elif line.strip() == '---' and idx < 5:
            content_start = max(content_start, idx + 1)
        elif line.strip() and content_start == 0:
            break
    
    # Add metadata section if found
    if include_meta and (meta_date or meta_tags):
        meta_html = '<div class="post-metadata">\n'
        if meta_date:
            meta_html += f'<p><strong>Date:</strong> {meta_date}</p>\n'
        if meta_tags:
            # Format tags as badges
            tags = [t.strip() for t in meta_tags.split(',')]
            tags_html = ', '.join(tags)
            meta_html += f'<p><strong>Tags:</strong> {tags_html}</p>\n'
        meta_html += '</div>\n\n<hr>\n\n'
        html_parts.append(meta_html)
    
    # Process content
    while i < len(lines):
        line = lines[i]
        
        # Skip metadata lines we already processed
        if i < content_start and (
            line.startswith('**Date:**') or 
            line.startswith('Date:') or
            line.startswith('**Tags:**') or 
            line.startswith('Tags:') or
            line.startswith('**Category:**') or
            line.startswith('Category:') or
            line.strip() == '---'
        ):
            i += 1
            continue
        
        # Code block handling
        if line.strip().startswith('```'):
            if in_code_block:
                # End code block
                code_text = '\n'.join(code_content)
                lang_class = f' class="language-{code_language}"' if code_language else ''
                html_parts.append(f'<pre><code{lang_class}>{escape_html(code_text)}</code></pre>\n\n')
                in_code_block = False
                code_content = []
                code_language = ''
            else:
                # Start code block
                in_code_block = True
                code_language = line.strip()[3:].strip()
            i += 1
            continue
        
        if in_code_block:
            code_content.append(line)
            i += 1
            continue
        
        # Table handling
        if '|' in line and line.strip().startswith('|'):
            if not in_table:
                in_table = True
                table_rows = []
            table_rows.append(line)
            i += 1
            continue
        elif in_table:
            # End of table
            html_parts.append(format_table(table_rows))
            in_table = False
            table_rows = []
            # Don't increment i, process this line normally
        
        # List handling (bullet points)
        if re.match(r'^[\s]*[-*]\s+', line):
            if not in_list or list_type != 'ul':
                if in_list:
                    html_parts.append(f'</{list_type}>\n\n')
                in_list = True
                list_type = 'ul'
                html_parts.append('<ul>\n')
            item_text = re.sub(r'^[\s]*[-*]\s+', '', line)
            html_parts.append(f'<li>{format_inline(item_text)}</li>\n')
            i += 1
            continue
        
        # List handling (numbered)
        if re.match(r'^[\s]*\d+\.\s+', line):
            if not in_list or list_type != 'ol':
                if in_list:
                    html_parts.append(f'</{list_type}>\n\n')
                in_list = True
                list_type = 'ol'
                html_parts.append('<ol>\n')
            item_text = re.sub(r'^[\s]*\d+\.\s+', '', line)
            html_parts.append(f'<li>{format_inline(item_text)}</li>\n')
            i += 1
            continue
        
        # End list if we hit a non-list line
        if in_list and line.strip():
            html_parts.append(f'</{list_type}>\n\n')
            in_list = False
            list_type = None
        
        # Empty line ends list
        if in_list and not line.strip():
            html_parts.append(f'</{list_type}>\n\n')
            in_list = False
            list_type = None
            i += 1
            continue
        
        # Headings
        if line.startswith('#### '):
            html_parts.append(f'<h4>{format_inline(line[5:])}</h4>\n\n')
            i += 1
            continue
        if line.startswith('### '):
            html_parts.append(f'<h3>{format_inline(line[4:])}</h3>\n\n')
            i += 1
            continue
        if line.startswith('## '):
            html_parts.append(f'<h2>{format_inline(line[3:])}</h2>\n\n')
            i += 1
            continue
        if line.startswith('# '):
            html_parts.append(f'<h1>{format_inline(line[2:])}</h1>\n\n')
            i += 1
            continue
        
        # Horizontal rule
        if line.strip() == '---' or line.strip() == '***' or line.strip() == '___':
            html_parts.append('<hr>\n\n')
            i += 1
            continue
        
        # Empty line
        if not line.strip():
            i += 1
            continue
        
        # Regular paragraph
        html_parts.append(f'<p>{format_inline(line)}</p>\n\n')
        i += 1
    
    # Close any open elements
    if in_list:
        html_parts.append(f'</{list_type}>\n\n')
    if in_table:
        html_parts.append(format_table(table_rows))
    if in_code_block:
        code_text = '\n'.join(code_content)
        html_parts.append(f'<pre><code>{escape_html(code_text)}</code></pre>\n\n')
    
    return ''.join(html_parts)


def escape_html(text: str) -> str:
    """Escape HTML special characters."""
    return (text
            .replace('&', '&amp;')
            .replace('<', '&lt;')
            .replace('>', '&gt;')
            .replace('"', '&quot;')
            .replace("'", '&#39;'))


def format_inline(text: str) -> str:
    """Format inline elements: bold, italic, inline code, links."""
    # Inline code (must be before bold/italic to avoid conflicts)
    text = re.sub(r'`([^`]+)`', r'<code>\1</code>', text)
    
    # Bold
    text = re.sub(r'\*\*([^*]+)\*\*', r'<strong>\1</strong>', text)
    text = re.sub(r'__([^_]+)__', r'<strong>\1</strong>', text)
    
    # Italic
    text = re.sub(r'\*([^*]+)\*', r'<em>\1</em>', text)
    text = re.sub(r'_([^_]+)_', r'<em>\1</em>', text)
    
    # Links
    text = re.sub(r'\[([^\]]+)\]\(([^)]+)\)', r'<a href="\2">\1</a>', text)
    
    return text


def format_table(rows: List[str]) -> str:
    """Convert markdown table rows to HTML table."""
    if not rows:
        return ''
    
    html = '<table>\n'
    
    for idx, row in enumerate(rows):
        cells = [c.strip() for c in row.split('|') if c.strip()]
        
        # Skip separator row (contains only dashes and colons)
        if all(re.match(r'^[-:]+$', cell) for cell in cells):
            continue
        
        if idx == 0:
            # Header row
            html += '<thead>\n<tr>'
            for cell in cells:
                html += f'<th>{format_inline(cell)}</th>'
            html += '</tr>\n</thead>\n<tbody>\n'
        else:
            # Data row
            html += '<tr>'
            for cell in cells:
                html += f'<td>{format_inline(cell)}</td>'
            html += '</tr>\n'
    
    html += '</tbody>\n</table>\n\n'
    return html


def create_blog_post_html(
    title: str,
    content: str,
    date: str = None,
    tags: str = None,
    category: str = "Build in Public"
) -> str:
    """
    Create a complete blog post HTML ready for WordPress.
    
    This formats the content to work beautifully with the 
    Digital Dreamscape single.php template.
    
    Args:
        title: Post title (used for context, not included in body)
        content: Markdown content
        date: Optional date string
        tags: Optional comma-separated tags
        category: Category name (for context)
        
    Returns:
        HTML content ready for wp post create
    """
    # Build metadata string if needed
    meta_parts = []
    if date:
        meta_parts.append(f'**Date:** {date}')
    if tags:
        meta_parts.append(f'**Tags:** {tags}')
    
    if meta_parts:
        meta_block = '\n'.join(meta_parts) + '\n\n---\n\n'
        full_content = meta_block + content
    else:
        full_content = content
    
    # Format to beautiful HTML
    html = format_beautiful_html(full_content, include_meta=True)
    
    return html


# CLI for testing
if __name__ == '__main__':
    import sys
    
    test_content = """
**Date:** December 24, 2025
**Tags:** web-development, css-battles, trading

---

ok so its christmas eve and instead of wrapping presents im wrestling with css.

## Problem

two battles today:

- css wouldnt load
- templates werent mapping correctly
- cache was serving old versions

**battle 1: the website**

wanted the same beautiful dark theme. simple right?

```bash
python tools/clear_digitaldreamscape_cache.py
```

nope.

**battle 2: the market**

down $300 today.

## Steps

1. created beautiful templates
2. added @imports for guaranteed css loading
3. built cache-clearing tool

| Page | Template | Status |
|------|----------|--------|
| `/` | front-page.php | ✅ |
| `/blog/` | page-blog-beautiful.php | ✅ |

## Reflections

both problems came from **stubbornness**.

[END TRANSMISSION]
"""
    
    print("=" * 60)
    print("Beautiful HTML Formatter Test")
    print("=" * 60)
    
    html = format_beautiful_html(test_content)
    print(html)

