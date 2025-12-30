"""
HTML Formatter for Autoblogger
===============================

Converts markdown content to beautiful HTML that works with 
WordPress themes, especially the Digital Dreamscape template.

Features:
- Proper code blocks with language classes
- HTML tables with proper structure  
- Inline code styling
- Horizontal rules as section dividers
- Numbered and bullet lists
- Strips frontmatter before conversion
"""

from __future__ import annotations

import re
from typing import List


def markdown_to_beautiful_html(markdown: str) -> str:
    """
    Convert markdown to beautiful HTML for WordPress.
    
    Handles frontmatter stripping and proper element conversion.
    
    Args:
        markdown: Raw markdown content (may include frontmatter)
        
    Returns:
        HTML string ready for WordPress
    """
    # Strip frontmatter if present
    content = _strip_frontmatter(markdown)
    
    # Convert to HTML
    return _convert_markdown_to_html(content)


def _strip_frontmatter(text: str) -> str:
    """Remove YAML frontmatter from markdown."""
    if not text.startswith("---"):
        return text
    
    parts = text.split("---", 2)
    if len(parts) >= 3:
        return parts[2].strip()
    return text


def _convert_markdown_to_html(content: str) -> str:
    """Convert markdown content to HTML."""
    lines = content.split('\n')
    html_parts = []
    
    i = 0
    in_code_block = False
    code_language = ''
    code_content = []
    in_list = False
    list_type = None
    in_table = False
    table_rows = []
    
    while i < len(lines):
        line = lines[i]
        
        # Code block handling
        if line.strip().startswith('```'):
            if in_code_block:
                # End code block
                code_text = '\n'.join(code_content)
                lang_class = f' class="language-{code_language}"' if code_language else ''
                html_parts.append(f'<pre><code{lang_class}>{_escape_html(code_text)}</code></pre>\n\n')
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
            html_parts.append(_format_table(table_rows))
            in_table = False
            table_rows = []
        
        # List handling (bullet points)
        if re.match(r'^[\s]*[-*]\s+', line):
            if not in_list or list_type != 'ul':
                if in_list:
                    html_parts.append(f'</{list_type}>\n\n')
                in_list = True
                list_type = 'ul'
                html_parts.append('<ul>\n')
            item_text = re.sub(r'^[\s]*[-*]\s+', '', line)
            html_parts.append(f'<li>{_format_inline(item_text)}</li>\n')
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
            html_parts.append(f'<li>{_format_inline(item_text)}</li>\n')
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
            html_parts.append(f'<h4>{_format_inline(line[5:])}</h4>\n\n')
            i += 1
            continue
        if line.startswith('### '):
            html_parts.append(f'<h3>{_format_inline(line[4:])}</h3>\n\n')
            i += 1
            continue
        if line.startswith('## '):
            html_parts.append(f'<h2>{_format_inline(line[3:])}</h2>\n\n')
            i += 1
            continue
        if line.startswith('# '):
            # Skip H1 as WordPress uses post title
            i += 1
            continue
        
        # Horizontal rule
        if line.strip() in ('---', '***', '___'):
            html_parts.append('<hr>\n\n')
            i += 1
            continue
        
        # Empty line
        if not line.strip():
            i += 1
            continue
        
        # Regular paragraph
        html_parts.append(f'<p>{_format_inline(line)}</p>\n\n')
        i += 1
    
    # Close any open elements
    if in_list:
        html_parts.append(f'</{list_type}>\n\n')
    if in_table:
        html_parts.append(_format_table(table_rows))
    if in_code_block:
        code_text = '\n'.join(code_content)
        html_parts.append(f'<pre><code>{_escape_html(code_text)}</code></pre>\n\n')
    
    return ''.join(html_parts)


def _escape_html(text: str) -> str:
    """Escape HTML special characters."""
    return (text
            .replace('&', '&amp;')
            .replace('<', '&lt;')
            .replace('>', '&gt;')
            .replace('"', '&quot;')
            .replace("'", '&#39;'))


def _format_inline(text: str) -> str:
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


def _format_table(rows: List[str]) -> str:
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
                html += f'<th>{_format_inline(cell)}</th>'
            html += '</tr>\n</thead>\n<tbody>\n'
        else:
            # Data row
            html += '<tr>'
            for cell in cells:
                html += f'<td>{_format_inline(cell)}</td>'
            html += '</tr>\n'
    
    html += '</tbody>\n</table>\n\n'
    return html

