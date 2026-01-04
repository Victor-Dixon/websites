<?php
/**
 * Victor Voice Transformer
 *
 * Transforms devlog content into Victor's archival voice for Dreamscape episodes
 * Usage: php transform_to_victor_voice.php <devlog_file>
 */

// Prevent web access
if (!defined('ABSPATH')) {
    die('This script must be run in a WordPress environment');
}

class VictorVoiceTransformer {

    private $voice_profile = [
        'casing' => 'lowercase_preferred',
        'max_lines_per_paragraph' => 3,
        'required_sections' => [
            'world_state',
            'what_changed',
            'what_held',
            'what_failed',
            'artifacts_created',
            'open_loops'
        ]
    ];

    public function transform($devlog_content) {
        // Parse devlog into components
        $parsed = $this->parse_devlog($devlog_content);

        // Restructure according to voice profile
        $episode = $this->apply_voice_structure($parsed);

        // Apply typographic rules
        $episode = $this->apply_typography($episode);

        return $episode;
    }

    private function parse_devlog($content) {
        $lines = explode("\n", $content);
        $parsed = [
            'title' => '',
            'date' => '',
            'questline' => '',
            'sections' => []
        ];

        $current_section = '';
        $current_content = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Extract metadata
            if (preg_match('/^\*\*Date\*\*:\s*(.+)$/i', $line, $matches)) {
                $parsed['date'] = trim($matches[1]);
            }
            if (preg_match('/^\*\*Questline\*\*:\s*(.+)$/i', $line, $matches)) {
                $parsed['questline'] = trim($matches[1]);
            }

            // Extract title
            if (preg_match('/^#\s+(.+)$/', $line, $matches) && empty($parsed['title'])) {
                $parsed['title'] = trim($matches[1]);
            }

            // Extract sections
            if (preg_match('/^##\s+(.+)$/', $line, $matches)) {
                if (!empty($current_section)) {
                    $parsed['sections'][$current_section] = $current_content;
                }
                $current_section = $this->normalize_section_name($matches[1]);
                $current_content = [];
            } elseif (!empty($current_section) && !empty($line)) {
                $current_content[] = $line;
            }
        }

        // Add final section
        if (!empty($current_section)) {
            $parsed['sections'][$current_section] = $current_content;
        }

        return $parsed;
    }

    private function normalize_section_name($name) {
        $name = strtolower(str_replace([' ', '-', '_'], '_', $name));

        // Map common devlog sections to voice profile sections
        $mappings = [
            'the_problem' => 'world_state',
            'what_we_found' => 'world_state',
            'what_happened' => 'what_changed',
            'the_cleanup_protocol' => 'what_changed',
            'the_results' => 'what_changed',
            'what_survived' => 'what_held',
            'the_lesson' => 'why_this_matters',
            'quest_status_update' => 'open_loops',
            'technical_notes' => 'artifacts_created'
        ];

        return $mappings[$name] ?? $name;
    }

    private function apply_voice_structure($parsed) {
        $episode = [
            'title' => $this->generate_episode_title($parsed),
            'sections' => []
        ];

        // Apply required section structure
        foreach ($this->voice_profile['required_sections'] as $section) {
            $episode['sections'][$section] = $this->generate_section_content($section, $parsed);
        }

        // Add optional sections if content exists
        $optional_sections = ['why_this_matters', 'future_readers_note'];
        foreach ($optional_sections as $section) {
            $content = $this->generate_section_content($section, $parsed);
            if (!empty($content)) {
                $episode['sections'][$section] = $content;
            }
        }

        // Add closing
        $episode['closing'] = $this->generate_closing($parsed);

        return $episode;
    }

    private function generate_episode_title($parsed) {
        // Convert descriptive titles to state-based titles
        $title_mappings = [
            'the day we killed 1,000 duplicate files' => 'filesystem cleanup',
            'agent cellphone cleanup' => 'cellphone integration',
            'automating canon' => 'canon automation',
            'introducing thea' => 'narrative authority',
            'the severance' => 'system severance',
            'victor and the swarm' => 'foundational coordination'
        ];

        $original = strtolower($parsed['title']);
        return $title_mappings[$original] ?? $this->extract_state_title($parsed);
    }

    private function extract_state_title($parsed) {
        // Generate title based on what changed
        if (isset($parsed['sections']['what_changed'])) {
            $changes = implode(' ', $parsed['sections']['what_changed']);
            if (stripos($changes, 'duplicate') !== false) return 'duplicate removal';
            if (stripos($changes, 'import') !== false) return 'import consolidation';
            if (stripos($changes, 'canon') !== false) return 'canon expansion';
        }
        return 'system state';
    }

    private function generate_section_content($section_name, $parsed) {
        $content = [];

        switch ($section_name) {
            case 'world_state':
                $content = $this->extract_world_state($parsed);
                break;
            case 'what_changed':
                $content = $this->extract_changes($parsed);
                break;
            case 'what_held':
                $content = $this->extract_stability($parsed);
                break;
            case 'what_failed':
                $content = $this->extract_failures($parsed);
                break;
            case 'artifacts_created':
                $content = $this->extract_artifacts($parsed);
                break;
            case 'open_loops':
                $content = $this->extract_open_loops($parsed);
                break;
            case 'why_this_matters':
                $content = $this->extract_significance($parsed);
                break;
        }

        return array_filter($content);
    }

    private function extract_world_state($parsed) {
        $content = [];

        // Look for problem statements
        if (isset($parsed['sections']['the_problem'])) {
            foreach ($parsed['sections']['the_problem'] as $line) {
                if (stripos($line, 'overgrown') !== false) {
                    $content[] = 'filesystem entropy had accumulated.';
                }
                if (stripos($line, 'duplicates') !== false) {
                    $content[] = 'duplicate files created operational noise.';
                }
                if (stripos($line, 'operation') !== false) {
                    $content[] = 'system pressure revealed the fault lines.';
                }
            }
        }

        return $content;
    }

    private function extract_changes($parsed) {
        $content = [];

        // Look for results and changes
        $change_sections = ['the_results', 'what_happened', 'the_cleanup_protocol'];
        foreach ($change_sections as $section) {
            if (isset($parsed['sections'][$section])) {
                foreach ($parsed['sections'][$section] as $line) {
                    if (stripos($line, 'duplicate') !== false && stripos($line, 'eliminated') !== false) {
                        $content[] = 'duplicate files were removed.';
                    }
                    if (stripos($line, 'storage') !== false) {
                        $content[] = 'storage constraints were addressed.';
                    }
                    if (stripos($line, 'detection') !== false) {
                        $content[] = 'duplicate detection was applied systematically.';
                    }
                }
            }
        }

        return $content;
    }

    private function extract_stability($parsed) {
        $content = [];

        if (isset($parsed['sections']['what_survived'])) {
            foreach ($parsed['sections']['what_survived'] as $line) {
                if (stripos($line, 'git') !== false) {
                    $content[] = 'git history remained intact.';
                }
                if (stripos($line, 'configurations') !== false) {
                    $content[] = 'working configurations were preserved.';
                }
                if (stripos($line, 'coordination') !== false) {
                    $content[] = 'agent coordination continued.';
                }
            }
        }

        return $content;
    }

    private function extract_failures($parsed) {
        // Victor's voice is honest about failures
        $content = [];

        // Look for any admission of issues
        foreach ($parsed['sections'] as $section_content) {
            foreach ($section_content as $line) {
                if (stripos($line, 'assumptions') !== false) {
                    $content[] = 'assumptions about directory isolation did not hold.';
                }
                if (stripos($line, 'redundancy') !== false && stripos($line, 'strength') !== false) {
                    $content[] = 'some redundancy served operational purposes.';
                }
            }
        }

        return $content;
    }

    private function extract_artifacts($parsed) {
        $content = [];

        if (isset($parsed['sections']['technical_notes'])) {
            foreach ($parsed['sections']['technical_notes'] as $line) {
                // Convert technical notes to artifact list
                if (stripos($line, 'fdupes') !== false) {
                    $content[] = 'duplicate detection matrix';
                }
                if (stripos($line, 'git') !== false) {
                    $content[] = 'preservation manifest';
                }
                if (stripos($line, 'verification') !== false) {
                    $content[] = 'cleanup verification logs';
                }
                if (stripos($line, 'storage') !== false) {
                    $content[] = 'storage utilization baseline';
                }
            }
        }

        return $content;
    }

    private function extract_open_loops($parsed) {
        $content = [];

        if (isset($parsed['sections']['quest_status_update'])) {
            foreach ($parsed['sections']['quest_status_update'] as $line) {
                if (stripos($line, 'next') !== false && stripos($line, 'import') !== false) {
                    $content[] = 'import statement variations persist.';
                }
                if (stripos($line, 'dependency') !== false) {
                    $content[] = 'dependency audit remains planned.';
                }
                if (stripos($line, 'documentation') !== false) {
                    $content[] = 'documentation sync needs pressure.';
                }
            }
        }

        return $content;
    }

    private function extract_significance($parsed) {
        $content = [];

        if (isset($parsed['sections']['the_lesson'])) {
            foreach ($parsed['sections']['the_lesson'] as $line) {
                if (stripos($line, 'stewardship') !== false) {
                    $content[] = 'systems require stewardship.';
                }
                if (stripos($line, 'entropy') !== false) {
                    $content[] = 'entropy accumulates without constraint.';
                }
                if (stripos($line, 'maintenance') !== false) {
                    $content[] = 'what seems like maintenance becomes preservation.';
                }
            }
        }

        return $content;
    }

    private function generate_closing($parsed) {
        $closings = [
            'this episode closes with open loops.',
            'they are visible now.',
            'this state is recorded.'
        ];

        return implode("\n", $closings);
    }

    private function apply_typography($episode) {
        // Apply lowercase preference
        $episode['title'] = strtolower($episode['title']);

        // Apply paragraph line limits
        foreach ($episode['sections'] as $section_name => $content) {
            if (is_array($content)) {
                $episode['sections'][$section_name] = array_map(function($line) {
                    return rtrim($line, '.') . '.'; // Ensure single period
                }, $content);
            }
        }

        return $episode;
    }

    public function format_as_markdown($episode) {
        $output = "# {$episode['title']}\n\n";

        foreach ($episode['sections'] as $section_name => $content) {
            $output .= "## {$section_name}\n";

            if (is_array($content)) {
                if ($section_name === 'artifacts_created') {
                    $output .= implode("\n", array_map(function($item) {
                        return "- {$item}";
                    }, $content)) . "\n\n";
                } else {
                    $output .= implode("\n", $content) . "\n\n";
                }
            } else {
                $output .= "{$content}\n\n";
            }
        }

        return $output;
    }
}

// Command line interface
if ($argc > 1) {
    $transformer = new VictorVoiceTransformer();

    if (file_exists($argv[1])) {
        $content = file_get_contents($argv[1]);
        $episode = $transformer->transform($content);
        echo $transformer->format_as_markdown($episode);
    } else {
        echo "Error: File not found: {$argv[1]}\n";
        exit(1);
    }
} else {
    echo "Victor Voice Transformer\n";
    echo "Usage: php transform_to_victor_voice.php <devlog_file.md>\n\n";
    echo "Transforms devlog content into Victor's archival voice for Dreamscape episodes.\n";
}