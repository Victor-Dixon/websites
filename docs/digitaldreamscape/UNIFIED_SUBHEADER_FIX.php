<?php
/**
 * Digital Dreamscape - Unified Subheader & Styling System
 * =======================================================
 * 
 * Adds a consistent subheader strip under the menu (same on all pages)
 * and unifies the visual system (spacing, card chrome, headings, CTA hierarchy)
 * 
 * Added: 2025-12-22
 * Purpose: Create visual consistency between homepage and blog pages
 */

// Add unified subheader strip after header (same on all pages)
function digitaldreamscape_unified_subheader() {
    // Shared tagline - same on all pages
    $tagline = 'Build in Public. Stream & Create.';
    ?>
    <div class="unified-subheader-strip">
        <div class="subheader-container">
            <div class="subheader-content">
                <span class="subheader-tagline"><?php echo esc_html($tagline); ?></span>
                <span class="subheader-divider">•</span>
                <span class="subheader-context"><?php 
                    if (is_front_page()) {
                        echo 'Command Hub';
                    } elseif (is_home() || is_archive()) {
                        echo 'Episode Archive';
                    } elseif (is_single()) {
                        echo 'Episode View';
                    } else {
                        echo 'Digital Dreamscape';
                    }
                ?></span>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_body_open', 'digitaldreamscape_unified_subheader', 20); // After body opens, before content

// Add unified styling system CSS
function digitaldreamscape_unified_styling_system() {
    ?>
    <style id="digitaldreamscape-unified-styling">
    /* ============================================
       UNIFIED SUBHEADER STRIP - Consistent Across All Pages
       ============================================ */
    
    .unified-subheader-strip {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        border-bottom: 1px solid rgba(99, 102, 241, 0.2);
        padding: 0.75rem 0;
        margin-bottom: 2rem;
    }
    
    .subheader-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    
    .subheader-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: rgba(99, 102, 241, 0.9);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .subheader-tagline {
        color: rgba(99, 102, 241, 1);
    }
    
    .subheader-divider {
        color: rgba(99, 102, 241, 0.4);
        font-weight: 300;
    }
    
    .subheader-context {
        color: rgba(99, 102, 241, 0.7);
    }
    
    /* Responsive: Hide divider on small screens */
    @media (max-width: 640px) {
        .subheader-content {
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .subheader-divider {
            display: none;
        }
        
        .subheader-context {
            font-size: 0.75rem;
        }
    }
    
    /* ============================================
       UNIFIED CARD STYLING - Consistent Across All Pages
       ============================================ */
    
    /* Episode Cards / Content Cards - Unified Style */
    .episode-card,
    .content-card,
    article.post,
    .post-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .episode-card:hover,
    .content-card:hover,
    article.post:hover,
    .post-card:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(99, 102, 241, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }
    
    /* Unified Card Header Rhythm: Tags → Title → Hook → CTA */
    .card-header,
    .episode-card-header,
    .post-header {
        margin-bottom: 1rem;
    }
    
    /* Card Tags - Unified Style */
    .card-tags,
    .episode-tags,
    .post-tags {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        flex-wrap: wrap;
    }
    
    .card-tag,
    .episode-tag,
    .post-tag {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: rgba(99, 102, 241, 0.15);
        border: 1px solid rgba(99, 102, 241, 0.3);
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgba(99, 102, 241, 0.9);
    }
    
    /* Card Title - Unified Style */
    .card-title,
    .episode-title,
    .post-title,
    .entry-title {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 0.5rem;
        color: rgba(255, 255, 255, 0.95);
    }
    
    .card-title a,
    .episode-title a,
    .post-title a,
    .entry-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .card-title a:hover,
    .episode-title a:hover,
    .post-title a:hover,
    .entry-title a:hover {
        color: rgba(99, 102, 241, 1);
    }
    
    /* Card Hook (1-line description) - Unified Style */
    .card-hook,
    .episode-hook,
    .post-excerpt {
        font-size: 1rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 1rem;
    }
    
    /* Card CTA - Unified Style */
    .card-cta,
    .episode-cta,
    .post-link,
    .entry-link {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.8) 0%, rgba(139, 92, 246, 0.8) 100%);
        border: 1px solid rgba(99, 102, 241, 0.5);
        border-radius: 6px;
        color: rgba(255, 255, 255, 1);
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .card-cta:hover,
    .episode-cta:hover,
    .post-link:hover,
    .entry-link:hover {
        background: linear-gradient(135deg, rgba(99, 102, 241, 1) 0%, rgba(139, 92, 246, 1) 100%);
        border-color: rgba(99, 102, 241, 0.8);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
    }
    
    /* ============================================
       UNIFIED SPACING SYSTEM
       ============================================ */
    
    /* Section Spacing - Consistent across pages */
    .site-main > section,
    .main-content > section,
    .page-section {
        margin-bottom: 3rem;
    }
    
    /* Module Spacing - Consistent rhythm */
    .module,
    .content-module,
    .section-module {
        margin-bottom: 2rem;
    }
    
    /* ============================================
       UNIFIED HEADING HIERARCHY
       ============================================ */
    
    /* Section Headings - Unified Style */
    .section-heading,
    .page-heading,
    h1.page-title,
    h1.entry-title {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: rgba(255, 255, 255, 0.95);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* Subsection Headings - Unified Style */
    .subsection-heading,
    h2.section-title {
        font-size: 1.5rem;
        font-weight: 600;
        line-height: 1.3;
        margin-bottom: 1rem;
        color: rgba(255, 255, 255, 0.9);
    }
    
    /* ============================================
       UNIFIED CTA HIERARCHY
       ============================================ */
    
    /* Primary CTA - Hero level */
    .cta-primary {
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 700;
    }
    
    /* Secondary CTA - Module level */
    .cta-secondary {
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    /* Tertiary CTA - Card level */
    .cta-tertiary {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    /* CTA Group - Multiple CTAs */
    .cta-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }
    
    /* ============================================
       RESPONSIVE ADJUSTMENTS
       ============================================ */
    
    @media (max-width: 768px) {
        .card-title,
        .episode-title,
        .post-title,
        .entry-title {
            font-size: 1.25rem;
        }
        
        .section-heading,
        .page-heading,
        h1.page-title,
        h1.entry-title {
            font-size: 1.5rem;
        }
        
        .cta-group {
            flex-direction: column;
        }
        
        .card-cta,
        .episode-cta,
        .post-link,
        .entry-link {
            width: 100%;
            text-align: center;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'digitaldreamscape_unified_styling_system', 99);

