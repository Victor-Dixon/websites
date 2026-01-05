# Dynamic Content System for dadudekc.com

## Overview

This system replaces hardcoded/mock data with **dynamic content** that auto-populates from Victor's actual work, experiments, projects, and resume items.

## SSOT Alignment

Per the SSOT whiteboard:
- **Builder Logs**: Experiments → learnings → next build
- **Project Demos**: What shipped + proof
- **Automation Offers**: What I sell + how it works
- **Resume/Portfolio**: Compiled skills, projects, and proof of execution

## Custom Post Types Created

### 1. `experiment` (Builder Logs)
- **Purpose**: Track experiments, learnings, and next builds
- **Fields**:
  - `experiment_status`: live, in-progress, shipped, archived
  - `experiment_url`: Link to experiment (GitHub, demo, etc.)
  - `experiment_stats`: JSON string of metrics
  - `experiment_learnings`: Key learnings
  - `next_build`: What to build next
- **Archive**: `/experiments/`

### 2. `project` (Project Demos)
- **Purpose**: Show what shipped and proof it works
- **Fields**:
  - `project_url`: Live URL or demo link
  - `project_github`: GitHub repository URL
  - `project_status`: shipped, in-progress, archived
  - `project_skills`: Comma-separated skills used
  - `project_proof`: Proof/evidence (screenshots, metrics)
- **Archive**: `/projects/`

### 3. `resume_item` (Resume & Portfolio)
- **Purpose**: Compiled skills, projects, and proof
- **Fields**:
  - `resume_category`: skill, project, achievement, education
  - `resume_date`: Date or date range
  - `resume_proof_url`: URL to proof/demo
  - `resume_priority`: Display priority (higher = more prominent)
- **Note**: Private (not publicly queryable)

## Dynamic Components

### 1. Experiments Feed (`template-parts/components/experiments-feed.php`)
- Pulls from `experiment` CPT
- Shows status, learnings, stats, and links
- Replaces hardcoded experiments section

### 2. Project Demos (`template-parts/components/project-demos.php`)
- Pulls from `project` CPT (status = 'shipped')
- Shows thumbnails, skills, proof, and links
- New section on homepage

### 3. Proof Metrics (`inc/functions/proof-metrics.php`)
- **Dynamic calculations**:
  - AI Agents: Counts live experiments
  - Revenue Sites: Counts projects with 'revenue-site' category
  - Avg Sprint Delivery: Calculates from project delivery times
  - Automation Running: Checks for live automation experiments
- **Dynamic lists**:
  - Shipped Systems: Pulls from projects (status = 'shipped')
  - Active Experiments: Pulls from experiments (status = 'live' or 'in-progress')

## How It Works

1. **Victor creates content** in WordPress admin:
   - Adds experiments as he builds
   - Adds projects when they ship
   - Adds resume items as skills are learned

2. **Homepage auto-updates**:
   - Experiments feed shows latest experiments
   - Project demos show shipped projects
   - Proof metrics calculate from actual data
   - Resume compiles from all items

3. **Fallback behavior**:
   - If no data exists, shows default/placeholder content
   - Metrics fall back to reasonable defaults
   - Lists show example items if empty

## Future Enhancements

### Integration Points
- **GitHub API**: Auto-create projects from repos
- **weareswarm.online**: Pull experiments from build feed
- **Dreamvault**: Auto-blog from conversation history
- **Discord**: Pull devlogs automatically

### Resume Compilation
- Auto-compile resume from:
  - All projects (skills used)
  - All experiments (learnings)
  - All resume items
- Generate PDF resume automatically
- Update portfolio page dynamically

### Automation
- **WP-Cron jobs** to:
  - Sync GitHub repos → projects
  - Pull weareswarm.online experiments
  - Update metrics daily
  - Compile resume weekly

## Usage

### Adding an Experiment
1. Go to WordPress admin → Experiments → Add New
2. Fill in title, description, status
3. Add learnings, stats, next build
4. Publish → Appears on homepage automatically

### Adding a Project
1. Go to WordPress admin → Projects → Add New
2. Fill in title, description, status = 'shipped'
3. Add project URL, GitHub, skills, proof
4. Publish → Appears in Project Demos section

### Adding Resume Item
1. Go to WordPress admin → Resume → Add New
2. Fill in category, date, proof URL
3. Set priority for display order
4. Publish → Included in resume compilation

## Files Created/Modified

### New Files
- `inc/post-types/experiment.php`
- `inc/post-types/project.php`
- `inc/post-types/resume-item.php`
- `inc/functions/proof-metrics.php`
- `template-parts/components/experiments-feed.php`
- `template-parts/components/project-demos.php`

### Modified Files
- `functions.php` (includes new CPTs and functions)
- `front-page.php` (uses dynamic components)

## Next Steps

1. **Create initial content**: Add real experiments, projects, resume items
2. **Test dynamic components**: Verify they display correctly
3. **Set up integrations**: GitHub API, weareswarm.online sync
4. **Automate resume compilation**: Generate PDF from all items
5. **Add REST API endpoints**: For external integrations

