# Fix live WordPress Emergence route links

Generated: 2026-06-05T01:45:00-05:00

## Before live scan

```text
--- https://maskzero.site/?cb=1780641875837403008 ---
86:      <a href="/spark-generator/">Generate</a>
696:              <a class="btn" href="/spark-generator.html">Generate Your Spark →</a><br />
781:              <a class="btn" href="/spark-generator.html">Create a Spark First →</a>

--- https://maskzero.site/the-emergence/?cb=1780641876436026008 ---
86:      <a href="/spark-generator/">Generate</a>
696:              <a class="btn" href="/spark-generator.html">Generate Your Spark →</a><br />
781:              <a class="btn" href="/spark-generator.html">Create a Spark First →</a>

--- https://maskzero.site/spark-generator/?cb=1780641877048093393 ---
68:<link rel="canonical" href="https://maskzero.site/spark-generator/" />
88:      <a href="/spark-generator/">Generate</a>
953:            window.location.href = '/battles/?spark_handoff=1';
1093:        const battle = links.find(function (href) { return href.indexOf('character_record=') !== -1 && href.indexOf('/battles/') !== -1; }) || '';
1787:        if (text.indexOf('battle') !== -1 || href.indexOf('/battles/') !== -1) {

--- https://maskzero.site/character-generator/?cb=1780641877649447316 ---
88:      <a href="/spark-generator/">Generate</a>
963:            window.location.href = '/battles/?spark_handoff=1';
1103:        const battle = links.find(function (href) { return href.indexOf('character_record=') !== -1 && href.indexOf('/battles/') !== -1; }) || '';
1797:        if (text.indexOf('battle') !== -1 || href.indexOf('/battles/') !== -1) {

--- https://maskzero.site/battles/?cb=1780641878261397393 ---
68:<link rel="canonical" href="https://maskzero.site/battles/" />
88:      <a href="/spark-generator/">Generate</a>
107:            <input type="hidden" id="spark_battle_nonce" name="spark_battle_nonce" value="0944a6add7" /><input type="hidden" name="_wp_http_referer" value="/battles/?cb=1780641878261397393" />
```

## WordPress route repair

```text
WP_ROOT=/home/u996867598/domains/maskzero.site/public_html
WP_CLI=/usr/local/bin/wp
== WP INFO ==
7.0
== DRY RUN ==
Table	Column	Replacements	Type
wp_posts	post_content	5	SQL
Success: 5 replacements to be made.
Table	Column	Replacements	Type
wp_posts	post_content	5	SQL
Success: 5 replacements to be made.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: 0 replacements to be made.
Table	Column	Replacements	Type
wp_posts	post_content	1	SQL
Success: 1 replacement to be made.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: 0 replacements to be made.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: 0 replacements to be made.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: 0 replacements to be made.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: 0 replacements to be made.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: 0 replacements to be made.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: 0 replacements to be made.
== APPLY ==
Table	Column	Replacements	Type
wp_posts	post_content	5	SQL
Success: Made 5 replacements.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: Made 0 replacements.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: Made 0 replacements.
Table	Column	Replacements	Type
wp_posts	post_content	1	SQL
Success: Made 1 replacement.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: Made 0 replacements.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: Made 0 replacements.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: Made 0 replacements.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: Made 0 replacements.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: Made 0 replacements.
Table	Column	Replacements	Type
wp_posts	post_content	0	SQL
Success: Made 0 replacements.
== FLUSH CACHE ==
Success: The cache was flushed.
WP_ROUTE_REPAIR=PASS
```

## After live scan

```text
--- https://maskzero.site/?cb=1780641897077861702 ---
86:      <a href="/spark-generator/">Generate</a>
696:              <a class="btn" href="/spark-generator/">Generate Your Spark →</a><br />
781:              <a class="btn" href="/spark-generator/">Create a Spark First →</a>

--- https://maskzero.site/the-emergence/?cb=1780641897829772471 ---
86:      <a href="/spark-generator/">Generate</a>
696:              <a class="btn" href="/spark-generator/">Generate Your Spark →</a><br />
781:              <a class="btn" href="/spark-generator/">Create a Spark First →</a>

--- https://maskzero.site/spark-generator/?cb=1780641898514139933 ---
68:<link rel="canonical" href="https://maskzero.site/spark-generator/" />
88:      <a href="/spark-generator/">Generate</a>
953:            window.location.href = '/battles/?spark_handoff=1';
1093:        const battle = links.find(function (href) { return href.indexOf('character_record=') !== -1 && href.indexOf('/battles/') !== -1; }) || '';
1787:        if (text.indexOf('battle') !== -1 || href.indexOf('/battles/') !== -1) {

--- https://maskzero.site/character-generator/?cb=1780641899193251933 ---
88:      <a href="/spark-generator/">Generate</a>
963:            window.location.href = '/battles/?spark_handoff=1';
1103:        const battle = links.find(function (href) { return href.indexOf('character_record=') !== -1 && href.indexOf('/battles/') !== -1; }) || '';
1797:        if (text.indexOf('battle') !== -1 || href.indexOf('/battles/') !== -1) {

--- https://maskzero.site/battles/?cb=1780641899879259241 ---
68:<link rel="canonical" href="https://maskzero.site/battles/" />
88:      <a href="/spark-generator/">Generate</a>
107:            <input type="hidden" id="spark_battle_nonce" name="spark_battle_nonce" value="0944a6add7" /><input type="hidden" name="_wp_http_referer" value="/battles/?cb=1780641899879259241" />
```

## Result

Live WordPress content now points to slug routes instead of stale static `.html` routes.
