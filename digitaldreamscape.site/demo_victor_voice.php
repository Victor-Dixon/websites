<?php
/**
 * Victor Voice Transformation Demo
 *
 * Shows how devlog content becomes archival episodes
 */

class VictorVoiceDemo {

    public function demonstrate_transformation() {
        // Original devlog content (sample)
        $original_devlog = "# The Day We Killed 1,000 Duplicate Files

**Date**: 2026-01-03
**Questline**: technical-debt
**Status**: completed

---

## The Problem

Our digital garden had become overgrown. Files were scattered like weeds across the filesystem, with duplicates hiding in every shadow. The Agent-8 cellphone cleanup operation had revealed the tip of a much larger iceberg.

## What We Found

* 1,247 duplicate files across 47 directories
* 23 different variations of the same import statement
* 89 orphaned configuration files
* 156 empty directories masquerading as real estate

The filesystem was a hoarder's paradise.";

        echo "🎭 VICTOR VOICE TRANSFORMATION DEMO\n";
        echo "====================================\n\n";

        echo "📝 ORIGINAL DEVLOG (first 300 chars):\n";
        echo "-------------------------------------\n";
        echo substr($original_devlog, 0, 300) . "...\n\n";

        echo "📖 TRANSFORMED EPISODE:\n";
        echo "-----------------------\n";
        echo $this->transform_to_victor_voice($original_devlog);
    }

    private function transform_to_victor_voice($content) {
        // Simple transformation demo
        $episode = "# filesystem cleanup\n\n";

        $episode .= "## world state\n";
        $episode .= "filesystem entropy had accumulated.\n";
        $episode .= "duplicate files created operational noise.\n";
        $episode .= "system pressure revealed the fault lines.\n\n";

        $episode .= "## what changed\n";
        $episode .= "duplicate detection was applied systematically.\n";
        $episode .= "redundant paths were removed.\n";
        $episode .= "storage constraints were addressed.\n\n";

        $episode .= "## what held\n";
        $episode .= "git history remained intact.\n";
        $episode .= "working configurations were preserved.\n";
        $episode .= "agent coordination continued.\n\n";

        $episode .= "## what failed\n";
        $episode .= "assumptions about directory isolation did not hold.\n";
        $episode .= "some redundancy served operational purposes.\n\n";

        $episode .= "## artifacts created\n";
        $episode .= "- duplicate detection matrix\n";
        $episode .= "- preservation manifest\n";
        $episode .= "- cleanup verification logs\n";
        $episode .= "- storage utilization baseline\n\n";

        $episode .= "## open loops\n";
        $episode .= "import statement variations persist.\n";
        $episode .= "dependency audit remains planned.\n";
        $episode .= "documentation sync needs pressure.\n\n";

        $episode .= "## why this matters\n";
        $episode .= "systems require stewardship.\n";
        $episode .= "entropy accumulates without constraint.\n";
        $episode .= "what seems like maintenance becomes preservation.\n\n";

        $episode .= "## future readers note\n";
        $episode .= "this cleanup was not exceptional.\n";
        $episode .= "it was necessary work.\n";
        $episode .= "the system surfaces what requires attention.\n\n";

        $episode .= "episode closes with open loops.\n";
        $episode .= "they are visible now.\n";
        $episode .= "this state is recorded.";

        return $episode;
    }
}

// Run the demo
$demo = new VictorVoiceDemo();
$demo->demonstrate_transformation();