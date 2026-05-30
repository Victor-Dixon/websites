# FreeRideInvestor Content Engine Audit 004

plugin: `/data/data/com.termux/files/home/projects/websites/_hostinger_build/plugins/freerideinvestor-content-engine`
source_review: `/data/data/com.termux/files/home/projects/websites/_hostinger_build/plugins/freerideinvestor-content-engine/source_review`
total_files: 8

## Files

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/Auto_blogger/main.py`

- suffix: `.py`
- bytes: 11731
- requires_wordpress: `False`
- classes: `['BlogGeneratorThread', 'AutobloggerApp']`
- functions: `[]`
- shortcodes: `[]`
- post_types: `[]`
- taxonomies: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/Auto_blogger/ui/generate_blog.py`

- suffix: `.py`
- bytes: 18545
- requires_wordpress: `False`
- classes: `[]`
- functions: `[]`
- shortcodes: `[]`
- post_types: `[]`
- taxonomies: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/custom-shortcodes.php`

- suffix: `.php`
- bytes: 5245
- requires_wordpress: `True`
- classes: `['exists', 'not', 'for']`
- functions: `['simplifiedtheme_cheat_sheet_shortcode', 'simplifiedtheme_current_year_shortcode', 'simplifiedtheme_custom_message_shortcode', 'simplifiedtheme_tbow_tactics_shortcode']`
- shortcodes: `['cheat_sheet', 'current_year', 'custom_message', 'tbow_tactics']`
- post_types: `[]`
- taxonomies: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/post-types/cheat-sheet.php`

- suffix: `.php`
- bytes: 2498
- requires_wordpress: `True`
- classes: `[]`
- functions: `['simplifiedtheme_register_cheat_sheet']`
- shortcodes: `[]`
- post_types: `['cheat_sheet']`
- taxonomies: `[]`
- actions: `['init']`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/post-types/free-investor.php`

- suffix: `.php`
- bytes: 2537
- requires_wordpress: `True`
- classes: `[]`
- functions: `['simplifiedtheme_register_free_investor']`
- shortcodes: `[]`
- post_types: `['free_investor']`
- taxonomies: `[]`
- actions: `['init']`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/post-types/tbow-tactics.php`

- suffix: `.php`
- bytes: 2518
- requires_wordpress: `True`
- classes: `[]`
- functions: `['simplifiedtheme_register_tbow_tactics']`
- shortcodes: `[]`
- post_types: `['tbow_tactics']`
- taxonomies: `[]`
- actions: `['init']`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-archive-tools.php`

- suffix: `.php`
- bytes: 18554
- requires_wordpress: `False`
- classes: `[]`
- functions: `[]`
- shortcodes: `[]`
- post_types: `[]`
- taxonomies: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-education.php`

- suffix: `.php`
- bytes: 9487
- requires_wordpress: `False`
- classes: `[]`
- functions: `[]`
- shortcodes: `[]`
- post_types: `[]`
- taxonomies: `[]`
- actions: `[]`
- filters: `[]`

## Promotion Gate

- Promote CPT/shortcode files first.
- Keep Python Auto_blogger outside runtime plugin until converted to backend/static pipeline.
- Do not include page templates as runtime includes until shortcode dependencies are mapped.
