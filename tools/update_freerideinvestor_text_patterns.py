#!/usr/bin/env python3
"""
Update freerideinvestor.com Text Rendering Patterns
===================================================

Expands the PHP content filter with more comprehensive text rendering patterns.

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

def update_text_patterns(functions_file: Path) -> bool:
    """Update the fix_text_rendering_issues function with more patterns."""
    if not functions_file.exists():
        print(f"⚠️  Functions file not found: {functions_file}")
        return False
    
    content = functions_file.read_text(encoding="utf-8", errors="ignore")
    
    # Find the function and replace it with expanded version
    pattern_start = r'function fix_text_rendering_issues\([^)]+\)\s*\{'
    pattern_end = r'^\s*return \$content;\s*\}'
    
    new_function = """function fix_text_rendering_issues($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Common broken patterns - comprehensive list
    $patterns = [
        // Specific broken words/phrases
        '/\\bfreerideinve\\s+tor\\.com\\b/i' => 'freerideinvestor.com',
        '/\\bfreeride\\s+inve\\s+tor\\b/i' => 'freerideinvestor',
        '/\\bFreeRide\\s+Inve\\s+tor\\b/i' => 'FreeRide Investor',
        '/\\bDi\\s+cord\\b/i' => 'Discord',
        '/\\bInve\\s+tor\\b/i' => 'Investor',
        '/\\bNo-non\\s+en\\s+e\\b/i' => 'No-nonsense',
        '/\\bDi\\s+cipline\\b/i' => 'Discipline',
        '/\\bdi\\s+cipline\\b/i' => 'discipline',
        '/\\bPhil\\s+os\\s+ophy\\b/i' => 'Philosophy',
        '/\\bPhilo\\s+ophy\\b/i' => 'Philosophy',
        '/\\by\\s+tem\\b/i' => 'system',
        '/\\bY\\s+tem\\b/i' => 'System',
        '/\\bSy\\s+tem\\b/i' => 'System',
        '/\\bri\\s+k\\b/i' => 'risk',
        '/\\bRi\\s+k\\b/i' => 'Risk',
        '/\\bPo\\s+ition\\b/i' => 'Position',
        '/\\bUp\\s+ide\\b/i' => 'Upside',
        '/\\bdown\\s+ide\\b/i' => 'downside',
        '/\\bLo\\s+e\\s+\\b/i' => 'Losses ',
        '/\\bcon\\s+i\\s+tent\\b/i' => 'consistent',
        '/\\bcon\\s+i\\s+\\btent\\b/i' => 'consistent',
        '/\\bPo\\s+t-trade\\b/i' => 'Post-trade',
        '/\\bawarene\\s+\\b/i' => 'awareness ',
        '/\\bweakne\\s+\\.\\b/i' => 'weakness.',
        '/\\bcome\\s+\\b/i' => 'comes ',
        '/\\bi\\s+\\bproven\\b/i' => 'is proven',
        '/\\bi\\s+\\b/i' => 'is ',
        '/\\ble\\s+on\\b/i' => 'lesson',
        '/\\bLe\\s+on\\b/i' => 'Lesson',
        '/\\bp\\s+ychology\\b/i' => 'psychology',
        '/\\bP\\s+ychology\\b/i' => 'Psychology',
        '/\\bPlaybook\\s+\\b/i' => 'Playbooks ',
        '/\\bpromi\\s+e\\s+\\.\\b/i' => 'promises.',
        '/\\bpre\\s+ure\\s+\\b/i' => 'pressure ',
        '/\\bexi\\s+t\\b/i' => 'exist',
        '/\\bwor\\s+hiping\\b/i' => 'worshipping',
        '/\\bhu\\s+tle\\b/i' => 'hustle',
        '/\\bHu\\s+tle\\b/i' => 'Hustle',
        '/\\ben\\s+laved\\b/i' => 'enslaved',
        '/\\bEn\\s+laved\\b/i' => 'Enslaved',
        '/\\bFir\\s+t\\b/i' => 'First',
        '/\\btart\\s+\\b/i' => 'starts ',
        '/\\bmall\\b/i' => 'small',
        '/\\bterm\\s+\\.\\b/i' => 'terms.',
        '/\\by\\s+metric\\b/i' => 'asymmetric',
        '/\\ba\\s+ymmetric\\b/i' => 'asymmetric',
        '/\\blibrarie\\s+\\b/i' => 'libraries ',
        '/\\btool\\s+\\b/i' => 'tools ',
        '/\\bin\\s+tead\\b/i' => 'instead',
        '/\\blife\\s+tyle\\b/i' => 'lifestyle',
        '/\\bJu\\s+t\\b/i' => 'Just',
        '/\\bo\\s+\\bclarity\\b/i' => 'of clarity',
        
        // Fix generic patterns: single letter + space + rest of word (more aggressive)
        '/\\b([a-z])\\s+([a-z]{2,})\\b/i' => '$1$2', // "i tem" -> "item", "y tem" -> "ytem" (will be caught by specific patterns above)
        
        // Fix double/triple spaces
        '/\\s{2,}/' => ' ',
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    return $content;
}"""
    
    # Replace the function using regex
    # Find the function definition and replace entire function body
    old_function_pattern = r'(function fix_text_rendering_issues\([^)]+\)\s*\{)[\s\S]*?(\s*return \$content;\s*\})'
    
    if re.search(old_function_pattern, content):
        content = re.sub(old_function_pattern, new_function, content)
        functions_file.write_text(content, encoding="utf-8")
        print(f"✅ Updated text rendering function with expanded patterns")
        return True
    else:
        print(f"⚠️  Function pattern not found - may already be updated")
        return False

def main():
    project_root = Path(__file__).parent.parent
    theme_path = project_root / "websites" / "freerideinvestor.com" / "wp" / "wp-content" / "themes" / "freerideinvestor-modern"
    
    functions_file = theme_path / "functions.php"
    
    if update_text_patterns(functions_file):
        print(f"\n✅ Patterns updated successfully")
        print(f"📋 Next: Deploy updated functions.php")
    else:
        print(f"\n⚠️  Pattern update may have failed")

if __name__ == "__main__":
    sys.exit(0 if main() else 1)

