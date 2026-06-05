# Inspect and fix Spark Q11 renderer

Generated: 2026-06-05T07:08:54-05:00

## Discovery

```text
--- plugin q10-q12/question_bank/function signals ---
33:function emergence_cg_question_bank() {
36:        return array('domain_questions' => array(), 'flavor_questions' => array());
41:        return array('domain_questions' => array(), 'flavor_questions' => array());
458:function emergence_cg_domain_option_label($q, $letter) {
459:    $bank = emergence_cg_question_bank();
460:    if (!isset($bank['domain_questions']) || !is_array($bank['domain_questions'])) {
464:    foreach ($bank['domain_questions'] as $item) {
465:        if (intval($item['q']) === intval($q) && isset($item['options'][$letter])) {
466:            return $letter . '. ' . $item['options'][$letter];
474:    $bank = emergence_cg_question_bank();
475:    $questions = isset($bank['domain_questions']) && is_array($bank['domain_questions']) ? $bank['domain_questions'] : array();
510:                        <option value="">Choose one...</option>
512:                            <?php if (isset($item['options'][$letter])) : ?>
513:                                <option value="<?php echo esc_attr($letter); ?>">
514:                                    <?php echo esc_html($letter . '. ' . $item['options'][$letter]); ?>
515:                                </option>
546:        'question_bank' => emergence_cg_question_bank(),
2528:    $counts = get_option($key, array());
2542:    update_option($key, $counts, false);
2552:    $counts = get_option(emergence_cg_tracking_storage_key(), array());
2682:        'manage_options',
2695:            return current_user_can('manage_options');
2701:    $counts = get_option(emergence_cg_tracking_storage_key(), array());
2745:    if (!current_user_can('manage_options')) {
2783:    if (!current_user_can('manage_options')) {
2861:            <li>This admin page requires <code>manage_options</code>.</li>
--- js q/render/select signals ---
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:22:  const questionBank = (window.EmergenceCG && window.EmergenceCG.question_bank) || {};
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:400:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:426:    flavorMount.innerHTML = '';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:521:      mount.innerHTML = '<p class="ecg-provider-error">Premium portrait request failed. The SVG fallback remains available.</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:526:      mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:535:    mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:627:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:682:    flavorMount.innerHTML = '';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:698:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:716:      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 could not unlock</h2><p>No manifested domains were returned by the scan.</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:746:    flavorMount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:767:      result.innerHTML += '<p>Building character sheet...</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:785:        result.innerHTML += '<p>Flavor error: ' + esc(error.message) + '</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:796:    result.innerHTML = '<p>Running Spark Type Scan...</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:798:    flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Preparing Pass 2...</h2><p>Manifested-domain flavor questions will appear here after the scan.</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:819:      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 failed to unlock</h2><p>' + esc(error.message) + '</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:820:      result.innerHTML = '<p>Generator error: ' + esc(error.message) + '</p>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:871:    if (window.EmergenceCG && window.EmergenceCG.question_bank) return window.EmergenceCG.question_bank;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:911:    mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1018:      var name = el.name || el.id || el.getAttribute("data-question") || "";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1104:    panel.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1325:        el.getAttribute("data-question") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1326:        el.getAttribute("data-q") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1327:        el.closest("[data-question]") ? el.closest("[data-question]").getAttribute("data-question") : "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1328:        el.closest("[data-q]") ? el.closest("[data-q]").getAttribute("data-q") : ""
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1399:    panel.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1579:        el.getAttribute("data-question") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1580:        el.getAttribute("data-q") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1581:        el.closest("[data-question]") ? el.closest("[data-question]").getAttribute("data-question") : "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1582:        el.closest("[data-q]") ? el.closest("[data-q]").getAttribute("data-q") : ""
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1647:    panel.innerHTML = html;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1817:        el.getAttribute("data-question") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1818:        el.getAttribute("data-q") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1819:        el.closest("[data-question]") ? el.closest("[data-question]").getAttribute("data-question") : "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1820:        el.closest("[data-q]") ? el.closest("[data-q]").getAttribute("data-q") : ""
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1874:    p.innerHTML = html;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2047:        el.getAttribute("data-question") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2048:        el.getAttribute("data-q") || "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2049:        el.closest("[data-question]") ? el.closest("[data-question]").getAttribute("data-question") : "",
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2050:        el.closest("[data-q]") ? el.closest("[data-q]").getAttribute("data-q") : ""
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2114:    panel.innerHTML = html;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2366:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2376:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2378:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2442:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2450:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2465:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2545:      panel(root()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2628:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2638:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2640:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2709:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2721:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2802:      panel(appRoot()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2884:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2891:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question'], [id*='flavor'], [class*='flavor']");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2893:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2975:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2985:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3066:      panel(appRoot()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:22:  const questionBank = (window.EmergenceCG && window.EmergenceCG.question_bank) || {};
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:400:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:426:    flavorMount.innerHTML = '';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:521:      mount.innerHTML = '<p class="ecg-provider-error">Premium portrait request failed. The SVG fallback remains available.</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:526:      mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:535:    mount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:627:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:682:    flavorMount.innerHTML = '';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:698:    result.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:716:      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 could not unlock</h2><p>No manifested domains were returned by the scan.</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:746:    flavorMount.innerHTML = [
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:767:      result.innerHTML += '<p>Building character sheet...</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:785:        result.innerHTML += '<p>Flavor error: ' + esc(error.message) + '</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:796:    result.innerHTML = '<p>Running Spark Type Scan...</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:798:    flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Preparing Pass 2...</h2><p>Manifested-domain flavor questions will appear here after the scan.</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:819:      flavorMount.innerHTML = '<div class="ecg-explainer"><h2>Pass 2 failed to unlock</h2><p>' + esc(error.message) + '</p></div>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:820:      result.innerHTML = '<p>Generator error: ' + esc(error.message) + '</p>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:962:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:972:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:974:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1038:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1046:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1061:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1141:      panel(root()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1224:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1234:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question']");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1236:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1305:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1317:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1398:      panel(appRoot()).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1480:    var attrs = ["name", "id", "data-question", "data-q", "aria-label"];
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1487:    var wrap = el.closest("[data-question], [data-q], .question, .ecg-question, [id*='question'], [class*='question'], [id*='flavor'], [class*='flavor']");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1489:      var val2 = wrap.getAttribute("data-question") || wrap.getAttribute("data-q") || wrap.id || wrap.className || "";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1571:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1581:    panel(scope).innerHTML =
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1662:      panel(appRoot()).innerHTML =
--- css potentially hiding selects/options/cards ---
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:13:  line-height: 1;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:34:.ecg-question {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:40:.ecg-question legend {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:44:.ecg-question select {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:124:  line-height: .95;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:231:  height: auto;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:379:  min-height: 220px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:403:.ecg-cosmetic-grid select {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:484:  min-height: 160px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:515:  max-height: 420px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:516:  overflow: auto;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:558:  overflow: auto;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:573:  overflow-x: hidden;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:586:#emergence-character-generator select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:589:.emergence-character-generator select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:592:.ecg-shell select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:595:.ecg-app select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:598:.ecg-wrap select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:601:[data-emergence-character-generator] select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:607:  overflow: hidden;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:608:  text-overflow: ellipsis;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:617:  min-height: 104px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:619:  overflow-wrap: anywhere;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:622:#emergence-character-generator select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:623:.emergence-character-generator select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:624:.ecg-shell select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:625:.ecg-app select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:626:.ecg-wrap select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:627:[data-emergence-character-generator] select {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:657:  #emergence-character-generator select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:660:  .emergence-character-generator select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:663:  .ecg-shell select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:666:  .ecg-app select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:669:  .ecg-wrap select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:672:  [data-emergence-character-generator] select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:676:    min-height: 54px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:679:    line-height: 1.35;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:692:    line-height: 1.25;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:780:    min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:813:  min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:841:  overflow: auto;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:883:  overflow: auto;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:888:  height: 10px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:889:  overflow: hidden;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:898:  height: 100%;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:934:  min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:965:  overflow: auto;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:996:  min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1005:  line-height: 1.15 !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1009:  user-select: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1033:  overflow: auto;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1060:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1066:  min-height: 52px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1074:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1075:  visibility: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1089:  min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1147:  overflow-wrap: anywhere;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1164:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1165:  visibility: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1180:  min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1204:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1205:  visibility: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1207:  height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1208:  min-height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1211:  overflow: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1214:/* Collapse locked/hidden two-pass sections so mobile does not show huge blank space. */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1215:[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1216:[data-locked="1"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1217:.ecg-flavor[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1218:.ecg-question[hidden],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1219:.ecg-question.is-hidden,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1222:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1223:  height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1224:  min-height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1227:  overflow: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1241:  min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1264:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1265:  visibility: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1269:/* Do not collapse normal question cards. Only collapse explicitly locked flavor panels. */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1270:.ecg-flavor[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1271:[data-phase="locked"][data-flavor-panel],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1272:[data-locked="1"][data-flavor-panel] {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1273:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1274:  height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1275:  min-height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1278:  overflow: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1281:/* Mobile question cards should not reserve giant empty space. */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1282:.ecg-question,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1283:.question,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1284:[class*="question"] {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1285:  min-height: auto;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1288:.ecg-question select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1289:.question select,
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1290:select {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:13:  line-height: 1;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:34:.ecg-question {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:40:.ecg-question legend {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:44:.ecg-question select {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:124:  line-height: .95;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:231:  height: auto;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:379:  min-height: 220px;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:403:.ecg-cosmetic-grid select {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:484:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:490:  min-height: 52px;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:498:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:499:  visibility: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:513:  min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:571:  overflow-wrap: anywhere;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:588:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:589:  visibility: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:604:  min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:628:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:629:  visibility: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:631:  height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:632:  min-height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:635:  overflow: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:638:/* Collapse locked/hidden two-pass sections so mobile does not show huge blank space. */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:639:[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:640:[data-locked="1"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:641:.ecg-flavor[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:642:.ecg-question[hidden],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:643:.ecg-question.is-hidden,
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:646:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:647:  height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:648:  min-height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:651:  overflow: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:665:  min-height: 56px;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:688:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:689:  visibility: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:693:/* Do not collapse normal question cards. Only collapse explicitly locked flavor panels. */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:694:.ecg-flavor[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:695:[data-phase="locked"][data-flavor-panel],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:696:[data-locked="1"][data-flavor-panel] {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:697:  display: none !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:698:  height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:699:  min-height: 0 !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:702:  overflow: hidden !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:705:/* Mobile question cards should not reserve giant empty space. */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:706:.ecg-question,
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:707:.question,
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:708:[class*="question"] {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:709:  min-height: auto;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:712:.ecg-question select,
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:713:.question select,
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:714:select {
```

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.6-q11-renderer-fix-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.6-q11-renderer-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.6-q11-renderer-fix-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.6-q11-renderer-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.6-q11-renderer-fix-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3123:/* DreamOS Spark Q11 Renderer Fix
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3130:  if (window.__DreamOSSparkQ11RendererFix) return;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3131:  window.__DreamOSSparkQ11RendererFix = true;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3186:    select.className = "dreamos-q11-repair-select";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3221:      if (card.getAttribute("data-dreamos-q11-renderer-repaired") === "1") return;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3230:      // Target blank question cards only. Q11 is known failure, but this also fixes same renderer failure for Q12+.
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3242:      card.setAttribute("data-dreamos-q11-renderer-repaired", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1719:/* DreamOS Spark Q11 Renderer Fix
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1726:  if (window.__DreamOSSparkQ11RendererFix) return;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1727:  window.__DreamOSSparkQ11RendererFix = true;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1782:    select.className = "dreamos-q11-repair-select";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1817:      if (card.getAttribute("data-dreamos-q11-renderer-repaired") === "1") return;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1826:      // Target blank question cards only. Q11 is known failure, but this also fixes same renderer failure for Q12+.
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1838:      card.setAttribute("data-dreamos-q11-renderer-repaired", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1296:/* DreamOS Spark Q11 Renderer Fix */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1297:[data-dreamos-q11-renderer-repaired="1"] {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1307:.dreamos-q11-repair-select {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:720:/* DreamOS Spark Q11 Renderer Fix */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:721:[data-dreamos-q11-renderer-repaired="1"] {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:731:.dreamos-q11-repair-select {
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

Added a targeted renderer repair for visible question cards that show Q text but have no answer control mounted. This specifically addresses Q11 and same-class blank question failures without fabricating answers.
