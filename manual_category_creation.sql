-- SQL to manually create Digital Dreamscape categories in WordPress
-- Run this in phpMyAdmin or WordPress database

INSERT INTO wp_terms (name, slug, term_group) VALUES
('Infrastructure & Architecture', 'infrastructure-architecture', 0),
('Agent Coordination', 'agent-coordination', 0),
('Digital Dreamscape Chronicles', 'digitaldreamscape-chronicles', 0),
('Canon Automation', 'canon-automation', 0),
('Development Operations', 'development-operations', 0),
('System Debugging', 'system-debugging', 0),
('General Episodes', 'general-episodes', 0);

-- Get the term IDs we just created
SET @infra_id = (SELECT term_id FROM wp_terms WHERE slug = 'infrastructure-architecture');
SET @agent_id = (SELECT term_id FROM wp_terms WHERE slug = 'agent-coordination');
SET @chronicles_id = (SELECT term_id FROM wp_terms WHERE slug = 'digitaldreamscape-chronicles');
SET @canon_id = (SELECT term_id FROM wp_terms WHERE slug = 'canon-automation');
SET @devops_id = (SELECT term_id FROM wp_terms WHERE slug = 'development-operations');
SET @debug_id = (SELECT term_id FROM wp_terms WHERE slug = 'system-debugging');
SET @general_id = (SELECT term_id FROM wp_terms WHERE slug = 'general-episodes');

-- Add them to the category taxonomy
INSERT INTO wp_term_taxonomy (term_id, taxonomy, description, parent, count) VALUES
(@infra_id, 'category', 'Digital Dreamscape episodes about system design and architecture', 0, 0),
(@agent_id, 'category', 'Digital Dreamscape episodes about multi-agent collaboration', 0, 0),
(@chronicles_id, 'category', 'Digital Dreamscape narrative and story content', 0, 0),
(@canon_id, 'category', 'Digital Dreamscape content canonization and automation', 0, 0),
(@devops_id, 'category', 'Digital Dreamscape development and operations', 0, 0),
(@debug_id, 'category', 'Digital Dreamscape system debugging and troubleshooting', 0, 0),
(@general_id, 'category', 'General Digital Dreamscape episodes', 0, 0);