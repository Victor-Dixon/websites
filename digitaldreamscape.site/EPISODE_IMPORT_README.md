# Episode Import Automation

This script automates the import of all 3000+ episode HTML files into WordPress posts for the Digital Dreamscape site.

## Overview

The `import_episodes.php` script processes episode HTML files from the `episodes/` directory and converts them into properly structured WordPress posts with metadata.

## Features

- **Bulk Processing**: Imports thousands of episodes efficiently
- **Deduplication**: Skips episodes that have already been imported
- **Progress Tracking**: Real-time progress reporting with time estimates
- **Batch Processing**: Configurable batch sizes to manage system load
- **Export Mode**: Works even without WordPress environment (exports to JSON)
- **Flexible Filtering**: Start from specific episodes, limit processing
- **Error Handling**: Robust error handling with detailed logging

## Usage

### Basic Import (Production)

```bash
php import_episodes.php
```

This will process all 3258+ episode files and import them as WordPress posts.

### Dry Run (Testing)

```bash
php import_episodes.php --dry-run
```

Shows what would be imported without making any changes.

### Limited Testing

```bash
php import_episodes.php --dry-run --limit=10
```

Process only the first 10 episodes for testing.

### Batch Processing

```bash
php import_episodes.php --batch-size=50
```

Process episodes in batches of 50 to reduce system load.

### Resume from Specific Episode

```bash
php import_episodes.php --start-from=EP-1000
```

Start processing from episode EP-1000 onwards.

### Combined Options

```bash
php import_episodes.php --dry-run --limit=100 --batch-size=25 --start-from=EP-500
```

## Command Line Options

| Option | Description | Default |
|--------|-------------|---------|
| `--dry-run` | Show what would be imported without making changes | - |
| `--limit=N` | Process only the first N episodes | All episodes |
| `--start-from=EP-NNNN` | Start processing from specific episode number | EP-145 |
| `--batch-size=N` | Process episodes in batches of N | 100 |
| `--help` | Show help message | - |

## What Gets Imported

Each episode file is parsed to extract:

- **Title**: Episode title from HTML title or h1 tag
- **Content**: Full episode content including sections and formatting
- **Questline**: Associated questline/category
- **Agent**: Agent attribution if available
- **State**: Episode state (active, resolved, canon, etc.)
- **Era**: Time period (defaults to 2026)
- **Metadata**: Episode ID, source system, internal file reference

## Data Structure

Imported episodes include these custom fields:

```php
[
    'title' => 'Episode Title',
    'content' => '<div>Full HTML content...</div>',
    'excerpt' => 'Generated excerpt from content',
    'questline' => 'questline-name',
    'agent_id' => 'Agent-Name',
    'artifact_state' => 'active|resolved|canon',
    'era' => '2026',
    'source_system' => 'episode_import',
    'internal_source' => '/path/to/episode/file.html',
    'artifact_type' => 'episode',
    'episode_id' => 'EP-XXXX'
]
```

## Export Mode

When WordPress is not available, the script runs in export mode:

1. Parses all episode files
2. Extracts structured data
3. Saves everything to a JSON file: `episodes_export_YYYY-MM-DD_HH-MM-SS.json`
4. This JSON can be used with a WordPress import script later

## Progress Reporting

The script provides real-time progress information:

```
📊 Progress: 1500/3258 processed
   Rate: 45.23 episodes/second
   Elapsed: 33.2s
   Remaining: 38.7s
```

## Error Handling

- Invalid episode files are logged and skipped
- Network/database errors are caught and reported
- Processing continues even if individual episodes fail
- Final summary shows total processed, imported, skipped, and errors

## Performance Notes

- **Typical Speed**: 40-60 episodes/second (depending on system)
- **Memory Usage**: Minimal - processes one file at a time
- **Batch Processing**: Reduces peak memory usage and system load
- **Resume Capability**: Use `--start-from` to resume interrupted imports

## Requirements

- PHP 7.4+
- WordPress environment (for live import) or export mode
- Read access to `episodes/` directory
- Write access for JSON exports (when in export mode)

## File Structure

```
digitaldreamscape.site/
├── import_episodes.php          # Main import script
├── episodes/                     # Episode HTML files
│   ├── ep_145_...html
│   ├── ep_146_...html
│   └── ... (3258+ files)
├── episodes_export_*.json        # Generated export files
└── wp/                          # WordPress installation
    └── wp-content/themes/digitaldreamscape/
        └── inc/api.php          # Artifact promotion functions
```

## Safety Features

- **Deduplication**: Won't create duplicate posts
- **Dry Run**: Test without making changes
- **Batch Limits**: Prevent system overload
- **Error Recovery**: Continues processing after errors
- **Rollback Ready**: Failed imports don't leave partial data

## Troubleshooting

### WordPress Not Found
If you see "WordPress environment not found", the script will automatically switch to export mode and create JSON files for later import.

### Memory Issues
Use smaller batch sizes: `--batch-size=25`

### Slow Processing
The script is I/O bound by file reading. SSD storage significantly improves performance.

### Permission Errors
Ensure PHP has read access to episode files and write access for JSON exports.

## Examples

### Import Everything (Production)
```bash
php import_episodes.php
```

### Test First 50 Episodes
```bash
php import_episodes.php --dry-run --limit=50 --batch-size=10
```

### Resume Large Import
```bash
php import_episodes.php --start-from=EP-2000 --batch-size=50
```

### Export for Later Import
```bash
php import_episodes.php --dry-run  # Creates JSON export
```

The script is designed to handle the full 3000+ episode archive reliably and efficiently.