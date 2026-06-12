# Fix original Spark renderer Q11 branch

Generated: 2026-06-05T07:53:51-05:00

## Discovery

```text
--- question bank Q10-Q12 ---
Q 10 When you decide you're going to win, what's your honest first move?
{'A': "Hit hard and early, with everything I've got.", 'B': 'Feel out the opponent and adapt to what they show me.', 'C': 'Get there first; speed is my weapon.', 'D': 'Stay unseen and pick my moment.', 'E': 'Take control of the variables and arrange them my way.', 'F': 'Meet them head on and simply outlast and overpower them.', 'G': 'Get inside their head and turn their own intentions against them.', 'H': "Strike from where they aren't looking, with a precision that arrives like fate."}
Q 11 What kind of resilience best describes you?
{'A': "I bounce off and keep moving; I don't dwell.", 'B': 'I step back, reassess, and re-plan around the hit.', 'C': "I absorb it and don't break.", 'D': 'I withdraw, regroup quietly, and re-emerge later.', 'E': 'I get more fired up the harder things push.', 'F': "I change myself to survive what's coming.", 'G': 'I shield my mind, stay clear, and refuse to be shaken.', 'H': 'I bend around it like light — still present, just arriving from another angle.'}
Q 12 How do you carry a heavy responsibility?
{'A': 'I shoulder it and refuse to let it slip.', 'B': 'I break it into parts and manage each one.', 'C': 'I stay loose and handle it by feel as it evolves.', 'D': "I pour myself into it completely until it's done.", 'E': 'I knock it out fast before it can pile up.', 'F': "I quietly handle it alone without making it anyone's business.", 'G': 'I hold the whole of it clear in my mind and bring others around to my view.', 'H': 'I carry it in the open for all to see, or bury it so deep only I feel the weight.'}
--- JS render/function signals ---
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:8:  let lastDomainAnswers = [];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:22:  const questionBank = (window.EmergenceCG && window.EmergenceCG.question_bank) || {};
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:52:  async function postGenerate(body) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:390:  function renderTotalityObservation(finalPayload) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:400:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:426:    flavorMount.innerHTML = '';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:516:  function renderPremiumImageProviderResult(data) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:521:      mount.innerHTML = '<p class="ecg-provider-error">Premium portrait request failed. The SVG fallback remains available.</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:526:      mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:535:    mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:599:  function renderBattleHandoffCTA(finalPayload) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:611:  function renderCharacterProfile(finalPayload) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:627:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:682:    flavorMount.innerHTML = '';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:686:  function renderDomainResult(payload) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:698:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:711:  function renderFlavorForm(payload) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:716:      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 could not unlock</h2><p>No manifested domains were returned by the scan.</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:729:          '<fieldset class="ecg-question">',
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:746:    flavorMount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:767:      result.innerHTML += '<p>Building character sheet...</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:777:        const finalPayload = await postGenerate({
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:778:          answers: lastDomainAnswers,
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:785:        result.innerHTML += '<p>Flavor error: ' + esc(error.message) + '</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:796:    result.innerHTML = '<p>Running Spark Type Scan...</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:798:    flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Preparing Pass 2...</h2><p>Manifested-domain flavor questions will appear here after the scan.</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:801:    lastDomainAnswers = [];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:804:      lastDomainAnswers.push(value);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:808:      const domainPayload = await postGenerate({answers: lastDomainAnswers});
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:815:      renderDomainResult(domainPayload);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:819:      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 failed to unlock</h2><p>' + esc(error.message) + '</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:820:      result.innerHTML = '<p>Generator error: ' + esc(error.message) + '</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:871:    if (window.EmergenceCG && window.EmergenceCG.question_bank) return window.EmergenceCG.question_bank;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:891:  function renderFallback(mount, reason) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:911:    mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1018:      var name = el.name || el.id || el.getAttribute("data-question") || "";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1087:  function renderDossier(mount, data, flavor) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1104:    panel.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1325:        el.getAttribute("data-question") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1327:        el.closest("[data-question]") ? el.closest("[data-question]").getAttribute("data-question") : "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1393:  function render(root, data, flavor) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1399:    panel.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1579:        el.getAttribute("data-question") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1581:        el.closest("[data-question]") ? el.closest("[data-question]").getAttribute("data-question") : "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1647:    panel.innerHTML = html;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1670:  function renderResult(btn, data, flavor) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1692:  function renderError(btn, err) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1817:        el.getAttribute("data-question") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1819:        el.closest("[data-question]") ? el.closest("[data-question]").getAttribute("data-question") : "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1874:    p.innerHTML = html;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1881:  function render(data, f) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2047:        el.getAttribute("data-question") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2049:        el.closest("[data-question]") ? el.closest("[data-question]").getAttribute("data-question") : "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2114:    panel.innerHTML = html;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2121:  function render(data, flavor) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2366:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2376:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2378:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2441:  function renderIncomplete(scope, count) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2442:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2449:  function renderLoading(scope) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2450:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2457:  function renderDossier(scope, payload) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2465:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2545:      panel(root()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2628:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2638:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2640:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2708:  function renderLoading(scope) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2709:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2713:  function renderDossier(scope, payload) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2721:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2802:      panel(appRoot()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2884:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2891:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question'], [id*='flavor'], [class*='flavor']");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2893:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2974:  function renderLoading(scope) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2975:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2979:  function renderDossier(scope, payload) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2985:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3066:      panel(appRoot()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:8:  let lastDomainAnswers = [];
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:22:  const questionBank = (window.EmergenceCG && window.EmergenceCG.question_bank) || {};
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:52:  async function postGenerate(body) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:390:  function renderTotalityObservation(finalPayload) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:400:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:426:    flavorMount.innerHTML = '';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:516:  function renderPremiumImageProviderResult(data) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:521:      mount.innerHTML = '<p class="ecg-provider-error">Premium portrait request failed. The SVG fallback remains available.</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:526:      mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:535:    mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:599:  function renderBattleHandoffCTA(finalPayload) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:611:  function renderCharacterProfile(finalPayload) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:627:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:682:    flavorMount.innerHTML = '';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:686:  function renderDomainResult(payload) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:698:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:711:  function renderFlavorForm(payload) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:716:      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 could not unlock</h2><p>No manifested domains were returned by the scan.</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:729:          '<fieldset class="ecg-question">',
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:746:    flavorMount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:767:      result.innerHTML += '<p>Building character sheet...</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:777:        const finalPayload = await postGenerate({
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:778:          answers: lastDomainAnswers,
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:785:        result.innerHTML += '<p>Flavor error: ' + esc(error.message) + '</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:796:    result.innerHTML = '<p>Running Spark Type Scan...</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:798:    flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Preparing Pass 2...</h2><p>Manifested-domain flavor questions will appear here after the scan.</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:801:    lastDomainAnswers = [];
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:804:      lastDomainAnswers.push(value);
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:808:      const domainPayload = await postGenerate({answers: lastDomainAnswers});
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:815:      renderDomainResult(domainPayload);
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:819:      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 failed to unlock</h2><p>' + esc(error.message) + '</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:820:      result.innerHTML = '<p>Generator error: ' + esc(error.message) + '</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:962:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:972:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:974:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1037:  function renderIncomplete(scope, count) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1038:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1045:  function renderLoading(scope) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1046:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1053:  function renderDossier(scope, payload) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1061:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1141:      panel(root()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1224:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1234:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1236:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1304:  function renderLoading(scope) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1305:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1309:  function renderDossier(scope, payload) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1317:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1398:      panel(appRoot()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1480:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1487:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question'], [id*='flavor'], [class*='flavor']");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1489:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1570:  function renderLoading(scope) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1571:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1575:  function renderDossier(scope, payload) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1581:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1662:      panel(appRoot()).innerHTML =
--- exact Q11 text signals ---
runtime/plugins/emergence-character-generator/assets/protocol-v85-question-bank.json:147:      "question": "What kind of resilience best describes you?",
runtime/plugins/emergence-character-generator/assets/protocol-v85-question-bank.json:161:      "question": "How do you carry a heavy responsibility?",
```

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.9-original-renderer-q11-fix-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.9-original-renderer-q11-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.9-original-renderer-q11-fix-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.9-original-renderer-q11-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.9-original-renderer-q11-fix-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3124:/* DreamOS Original Domain Question Renderer Branch Fix
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3252:    select.setAttribute("data-dreamos-original-renderer-q11-fixed", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3264:    select.setAttribute("data-dreamos-original-renderer-q11-fixed", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3293:      wrap.className = "dreamos-original-renderer-control-repair";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1720:/* DreamOS Original Domain Question Renderer Branch Fix
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1848:    select.setAttribute("data-dreamos-original-renderer-q11-fixed", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1860:    select.setAttribute("data-dreamos-original-renderer-q11-fixed", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1889:      wrap.className = "dreamos-original-renderer-control-repair";
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1296:/* DreamOS Original Domain Question Renderer Branch Fix */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1297:select[data-dreamos-original-renderer-q11-fixed="1"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1298:.dreamos-original-renderer-control-repair select {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1316:select[data-dreamos-original-renderer-q11-fixed="1"] option,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1317:.dreamos-original-renderer-control-repair option {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:720:/* DreamOS Original Domain Question Renderer Branch Fix */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:721:select[data-dreamos-original-renderer-q11-fixed="1"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:722:.dreamos-original-renderer-control-repair select {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:740:select[data-dreamos-original-renderer-q11-fixed="1"] option,
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:741:.dreamos-original-renderer-control-repair option {
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

Patched the original legacy renderer branch only. The patch repairs missing/empty selects in Q1-Q28 from the existing question bank without reintroducing canonical replacement renderer overlays.

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
5: * Version: 0.8.9-original-renderer-q11-fix-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.8.9-original-renderer-q11-fix-001
emergence-character-generator.css?ver=0.8.9-original-renderer-q11-fix-001
emergence-cg.js?ver=0.8.9-original-renderer-q11-fix-001
emergence-character-generator.js?ver=0.8.9-original-renderer-q11-fix-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master cf738727] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 6 insertions(+), 6 deletions(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live verification

```text
--- headers ---
HTTP/2 200 
cache-control: no-store, no-cache, must-revalidate, max-age=0, private
x-dreamos-spark-route: no-store-0.8.1
x-litespeed-cache-control: no-cache
--- page version ---
emergence-cg.css?ver=0.8.9-original-renderer-q11-fix-001
emergence-character-generator.css?ver=0.8.9-original-renderer-q11-fix-001
What kind of resilience
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
What kind of resilience
emergence-cg.js?ver=0.8.9-original-renderer-q11-fix-001
emergence-character-generator.js?ver=0.8.9-original-renderer-q11-fix-001
--- live original fix marker ---
/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_original_spark_renderer_q11_branch_001/live.js:3124:/* DreamOS Original Domain Question Renderer Branch Fix
/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_original_spark_renderer_q11_branch_001/live.js:3252:    select.setAttribute("data-dreamos-original-renderer-q11-fixed", "1");
/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_original_spark_renderer_q11_branch_001/live.js:3264:    select.setAttribute("data-dreamos-original-renderer-q11-fixed", "1");
/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_original_spark_renderer_q11_branch_001/live.js:3293:      wrap.className = "dreamos-original-renderer-control-repair";
/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_original_spark_renderer_q11_branch_001/live.css:1296:/* DreamOS Original Domain Question Renderer Branch Fix */
/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_original_spark_renderer_q11_branch_001/live.css:1297:select[data-dreamos-original-renderer-q11-fixed="1"],
/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_original_spark_renderer_q11_branch_001/live.css:1298:.dreamos-original-renderer-control-repair select {
/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_original_spark_renderer_q11_branch_001/live.css:1316:select[data-dreamos-original-renderer-q11-fixed="1"] option,
/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_original_spark_renderer_q11_branch_001/live.css:1317:.dreamos-original-renderer-control-repair option {
--- live bad overlay scan ---
```
