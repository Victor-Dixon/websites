<?php
/**
 * Canon Declaration System for Digital Dreamscape
 *
 * Scans all posts and identifies repeated references to declare as canon
 * Usage: php canon_declaration_system.php scan
 */

// Load WordPress environment
require_once('wp/wp-content/themes/digitaldreamscape/functions.php');

class CanonDeclarationSystem {

    private $canon_terms = [];
    private $reference_counts = [];
    private $canon_threshold = 3; // Minimum references to become canon

    public function __construct() {
        $this->load_existing_canon();
    }

    /**
     * Main execution method
     */
    public function run_scan() {
        echo "🎭 DIGITAL DREAMSCAPE CANON DECLARATION SYSTEM\n";
        echo "==============================================\n\n";

        echo "🔍 Scanning all posts for repeated references...\n";

        // Get all posts
        $posts = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);

        echo "📊 Found " . count($posts) . " published posts\n\n";

        // Analyze each post
        foreach ($posts as $post) {
            $this->analyze_post($post);
        }

        // Process canon declarations
        $this->process_canon_declarations();

        // Save results
        $this->save_canon_data();

        echo "\n✅ Canon declaration scan complete!\n";
        echo "📈 New canon elements declared: " . count($this->canon_terms) . "\n";
    }

    /**
     * Analyze a single post for references
     */
    private function analyze_post($post) {
        $content = $post->post_content;
        $title = $post->post_title;

        // Extract potential canon references
        $references = $this->extract_references($content, $title);

        foreach ($references as $reference) {
            if (!isset($this->reference_counts[$reference])) {
                $this->reference_counts[$reference] = 0;
            }
            $this->reference_counts[$reference]++;
        }

        echo "📝 Analyzed: " . substr($title, 0, 50) . "...\n";
    }

    /**
     * Extract references from post content
     */
    private function extract_references($content, $title) {
        $references = [];

        // Look for quoted terms
        if (preg_match_all('/"([^"]+)"/', $content, $matches)) {
            $references = array_merge($references, $matches[1]);
        }

        // Look for emphasized terms
        if (preg_match_all('/\*\*([^*]+)\*\*/', $content, $matches)) {
            $references = array_merge($references, $matches[1]);
        }

        // Look for system references
        if (preg_match_all('/\[([^\]]+)\]/', $content, $matches)) {
            $references = array_merge($references, $matches[1]);
        }

        // Look for named entities (basic)
        if (preg_match_all('/\b([A-Z][a-z]+ [A-Z][a-z]+)\b/', $content, $matches)) {
            $references = array_merge($references, $matches[1]);
        }

        return array_unique($references);
    }

    /**
     * Process canon declarations based on reference counts
     */
    private function process_canon_declarations() {
        echo "\n🔬 Processing canon declarations...\n";

        foreach ($this->reference_counts as $term => $count) {
            if ($count >= $this->canon_threshold && !isset($this->canon_terms[$term])) {
                $this->declare_canon($term, $count);
            }
        }
    }

    /**
     * Declare a term as canon
     */
    private function declare_canon($term, $count) {
        $this->canon_terms[$term] = [
            'term' => $term,
            'reference_count' => $count,
            'declared_date' => date('Y-m-d H:i:s'),
            'status' => 'canon'
        ];

        echo "⚡ DECLARED CANON: \"$term\" (referenced $count times)\n";
    }

    /**
     * Load existing canon data
     */
    private function load_existing_canon() {
        $canon_file = __DIR__ . '/canon_data.json';
        if (file_exists($canon_file)) {
            $data = json_decode(file_get_contents($canon_file), true);
            if ($data && isset($data['canon_terms'])) {
                $this->canon_terms = $data['canon_terms'];
            }
        }
    }

    /**
     * Save canon data
     */
    private function save_canon_data() {
        $data = [
            'canon_terms' => $this->canon_terms,
            'reference_counts' => $this->reference_counts,
            'last_scan' => date('Y-m-d H:i:s'),
            'total_posts_scanned' => wp_count_posts('post')->publish
        ];

        $canon_file = __DIR__ . '/canon_data.json';
        file_put_contents($canon_file, json_encode($data, JSON_PRETTY_PRINT));

        echo "\n💾 Canon data saved to: $canon_file\n";
    }

    /**
     * Display canon status
     */
    public function show_status() {
        echo "🎭 CANON DECLARATION SYSTEM STATUS\n";
        echo "==================================\n\n";

        echo "📊 Canon Terms: " . count($this->canon_terms) . "\n";
        echo "📈 Reference Threshold: " . $this->canon_threshold . "\n\n";

        if (!empty($this->canon_terms)) {
            echo "🏛️ DECLARED CANON ELEMENTS:\n";
            foreach ($this->canon_terms as $term => $data) {
                echo "  • \"$term\" (referenced {$data['reference_count']} times)\n";
            }
        } else {
            echo "❌ No canon elements declared yet\n";
        }

        echo "\n🔍 Run 'php canon_declaration_system.php scan' to scan for new canon elements\n";
    }
}

// Command line interface
if ($argc > 1) {
    $system = new CanonDeclarationSystem();

    switch ($argv[1]) {
        case 'scan':
            $system->run_scan();
            break;
        case 'status':
            $system->show_status();
            break;
        default:
            echo "Usage: php canon_declaration_system.php [scan|status]\n";
            echo "  scan  - Scan all posts for canon declarations\n";
            echo "  status - Show current canon status\n";
            break;
    }
} else {
    echo "🎭 Digital Dreamscape Canon Declaration System\n";
    echo "Usage: php canon_declaration_system.php [command]\n\n";
    echo "Commands:\n";
    echo "  scan   - Scan all posts and declare canon elements\n";
    echo "  status - Show current canon declaration status\n";
}