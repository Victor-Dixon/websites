# Recover canonical Spark route and force dossier button

Generated: 2026-06-05T06:17:38-05:00

## Local patch verification

```text
1784:/* DreamOS Canonical Final Dossier Force Button
1785: * Purpose: canonical /spark-generator/ must always have a visible, direct-bound dossier button.
1913:      '<p>The click registered on canonical /spark-generator/. Generating now.</p>',
921:/* DreamOS Canonical Final Dossier Force Button Styles */
5: * Version: 0.7.6-canonical-dossier-001
540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.7.6-canonical-dossier-001');
541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.7.6-canonical-dossier-001', true);
930:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.7.6-canonical-dossier-001');
931:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.7.6-canonical-dossier-001', true);
```

## Live route restore

```text
Table	Column	Replacements	Type
wp_actionscheduler_actions	hook	0	PHP
wp_actionscheduler_actions	status	0	PHP
wp_actionscheduler_actions	args	0	PHP
wp_actionscheduler_actions	schedule	0	PHP
wp_actionscheduler_actions	extended_args	0	PHP
wp_actionscheduler_groups	slug	0	PHP
wp_actionscheduler_logs	message	0	PHP
wp_commentmeta	meta_key	0	PHP
wp_commentmeta	meta_value	0	PHP
wp_comments	comment_author	0	PHP
wp_comments	comment_author_email	0	PHP
wp_comments	comment_author_url	0	PHP
wp_comments	comment_author_IP	0	PHP
wp_comments	comment_content	0	PHP
wp_comments	comment_approved	0	PHP
wp_comments	comment_agent	0	PHP
wp_comments	comment_type	0	PHP
wp_hostinger_reach_carts	hash	0	PHP
wp_hostinger_reach_carts	customer_email	0	PHP
wp_hostinger_reach_carts	items	0	PHP
wp_hostinger_reach_carts	totals	0	PHP
wp_hostinger_reach_carts	currency	0	PHP
wp_hostinger_reach_carts	status	0	PHP
wp_hostinger_reach_contact_lists	name	0	PHP
wp_hostinger_reach_forms	form_id	0	PHP
wp_hostinger_reach_forms	form_title	0	PHP
wp_hostinger_reach_forms	type	0	PHP
wp_links	link_url	0	PHP
wp_links	link_name	0	PHP
wp_links	link_image	0	PHP
wp_links	link_target	0	PHP
wp_links	link_description	0	PHP
wp_links	link_visible	0	PHP
wp_links	link_rel	0	PHP
wp_links	link_notes	0	PHP
wp_links	link_rss	0	PHP
wp_litespeed_url	url	0	PHP
wp_litespeed_url	cache_tags	0	PHP
wp_litespeed_url_file	vary	0	PHP
wp_litespeed_url_file	filename	0	PHP
wp_options	option_name	0	PHP
wp_options	option_value	0	PHP
wp_options	autoload	0	PHP
wp_postmeta	meta_key	0	PHP
wp_postmeta	meta_value	0	PHP
wp_posts	post_content	0	PHP
wp_posts	post_title	0	PHP
wp_posts	post_excerpt	0	PHP
wp_posts	post_status	0	PHP
wp_posts	comment_status	0	PHP
wp_posts	ping_status	0	PHP
wp_posts	post_password	0	PHP
wp_posts	post_name	0	PHP
wp_posts	to_ping	0	PHP
wp_posts	pinged	0	PHP
wp_posts	post_content_filtered	0	PHP
wp_posts	guid	0	PHP
wp_posts	post_type	0	PHP
wp_posts	post_mime_type	0	PHP
wp_term_taxonomy	taxonomy	0	PHP
wp_term_taxonomy	description	0	PHP
wp_termmeta	meta_key	0	PHP
wp_termmeta	meta_value	0	PHP
wp_terms	name	0	PHP
wp_terms	slug	0	PHP
wp_usermeta	meta_key	0	PHP
wp_usermeta	meta_value	0	PHP
wp_users	user_login	0	PHP
wp_users	user_nicename	0	PHP
wp_users	user_email	0	PHP
wp_users	user_url	0	PHP
wp_users	user_activation_key	0	PHP
wp_users	display_name	0	PHP
Success: Made 0 replacements.
Table	Column	Replacements	Type
wp_actionscheduler_actions	hook	0	PHP
wp_actionscheduler_actions	status	0	PHP
wp_actionscheduler_actions	args	0	PHP
wp_actionscheduler_actions	schedule	0	PHP
wp_actionscheduler_actions	extended_args	0	PHP
wp_actionscheduler_groups	slug	0	PHP
wp_actionscheduler_logs	message	0	PHP
wp_commentmeta	meta_key	0	PHP
wp_commentmeta	meta_value	0	PHP
wp_comments	comment_author	0	PHP
wp_comments	comment_author_email	0	PHP
wp_comments	comment_author_url	0	PHP
wp_comments	comment_author_IP	0	PHP
wp_comments	comment_content	0	PHP
wp_comments	comment_approved	0	PHP
wp_comments	comment_agent	0	PHP
wp_comments	comment_type	0	PHP
wp_hostinger_reach_carts	hash	0	PHP
wp_hostinger_reach_carts	customer_email	0	PHP
wp_hostinger_reach_carts	items	0	PHP
wp_hostinger_reach_carts	totals	0	PHP
wp_hostinger_reach_carts	currency	0	PHP
wp_hostinger_reach_carts	status	0	PHP
wp_hostinger_reach_contact_lists	name	0	PHP
wp_hostinger_reach_forms	form_id	0	PHP
wp_hostinger_reach_forms	form_title	0	PHP
wp_hostinger_reach_forms	type	0	PHP
wp_links	link_url	0	PHP
wp_links	link_name	0	PHP
wp_links	link_image	0	PHP
wp_links	link_target	0	PHP
wp_links	link_description	0	PHP
wp_links	link_visible	0	PHP
wp_links	link_rel	0	PHP
wp_links	link_notes	0	PHP
wp_links	link_rss	0	PHP
wp_litespeed_url	url	0	PHP
wp_litespeed_url	cache_tags	0	PHP
wp_litespeed_url_file	vary	0	PHP
wp_litespeed_url_file	filename	0	PHP
wp_options	option_name	0	PHP
wp_options	option_value	0	PHP
wp_options	autoload	0	PHP
wp_postmeta	meta_key	0	PHP
wp_postmeta	meta_value	0	PHP
wp_posts	post_content	0	PHP
wp_posts	post_title	0	PHP
wp_posts	post_excerpt	0	PHP
wp_posts	post_status	0	PHP
wp_posts	comment_status	0	PHP
wp_posts	ping_status	0	PHP
wp_posts	post_password	0	PHP
wp_posts	post_name	0	PHP
wp_posts	to_ping	0	PHP
wp_posts	pinged	0	PHP
wp_posts	post_content_filtered	0	PHP
wp_posts	guid	0	PHP
wp_posts	post_type	0	PHP
wp_posts	post_mime_type	0	PHP
wp_term_taxonomy	taxonomy	0	PHP
wp_term_taxonomy	description	0	PHP
wp_termmeta	meta_key	0	PHP
wp_termmeta	meta_value	0	PHP
wp_terms	name	0	PHP
wp_terms	slug	0	PHP
wp_usermeta	meta_key	0	PHP
wp_usermeta	meta_value	0	PHP
wp_users	user_login	0	PHP
wp_users	user_nicename	0	PHP
wp_users	user_email	0	PHP
wp_users	user_url	0	PHP
wp_users	user_activation_key	0	PHP
wp_users	display_name	0	PHP
Success: Made 0 replacements.
Success: The cache was flushed.
LIVE_CANONICAL_ROUTE_RESTORE=PASS
```

## Deploy

```text
== VERIFY ENV ==
== WRITE TASK ==
== DISCOVER REMOTE PLUGIN DIR ==
/home/u996867598/domains/maskzero.site/public_html/wp-content/plugins/emergence-character-generator
REMOTE_PLUGIN_DIR=/home/u996867598/domains/maskzero.site/public_html/wp-content/plugins/emergence-character-generator
REMOTE_ASSET_DIR=/home/u996867598/domains/maskzero.site/public_html/wp-content/plugins/emergence-character-generator/assets
== UPLOAD PHP AND ASSETS ==
UPLOAD=PASS
== REMOTE CHMOD / CACHE FLUSH ==
Success: The cache was flushed.
Plugin 'emergence-character-generator' deactivated.
Success: Deactivated 1 of 1 plugins.
Plugin 'emergence-character-generator' activated.
Success: Activated 1 of 1 plugins.
Success: The cache was flushed.
5: * Version: 0.7.6-canonical-dossier-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.7.6-canonical-dossier-001
emergence-character-generator.css?ver=0.7.6-canonical-dossier-001
emergence-cg.js?ver=0.7.6-canonical-dossier-001
emergence-character-generator.js?ver=0.7.6-canonical-dossier-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master 7130f77f] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 1 insertion(+), 1 deletion(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live homepage routes

```text
86:      <a href="/spark-generator/">Generate</a>
696:              <a class="btn" href="/spark-generator/">Generate Your Spark →</a><br />
781:              <a class="btn" href="/spark-generator/">Create a Spark First →</a>
```

## Canonical page verification

```text
emergence-cg.css?ver=0.7.6-canonical-dossier-001
emergence-character-generator.css?ver=0.7.6-canonical-dossier-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.7.6-canonical-dossier-001
emergence-character-generator.js?ver=0.7.6-canonical-dossier-001
```

## Live JS marker

```text
1784:/* DreamOS Canonical Final Dossier Force Button
```

## Result

Canonical route restored and canonical Spark generator page loads a forced direct-bound dossier button.
