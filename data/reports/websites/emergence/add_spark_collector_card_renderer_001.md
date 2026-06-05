# Add Spark collector card renderer

Generated: 2026-06-05T08:19:33-05:00

## Local verification

```text
41:.card-maker{margin-top:1rem;border:1px solid var(--line);background:rgba(255,255,255,.045);border-radius:22px;padding:1rem}
42:.card-maker input{width:100%;padding:.8rem;border-radius:14px;border:1px solid var(--line);background:rgba(255,255,255,.08);color:var(--text);margin:.5rem 0 1rem}
43:#spark-card-canvas{width:100%;max-width:420px;border-radius:24px;border:1px solid var(--line);background:#111;margin-top:1rem}
71:/* DreamOS Spark collector card renderer v1 */
262:  function renderCollectorCard(){
263:    const canvas=document.getElementById("spark-card-canvas");
364:  function downloadCollectorCard(){
365:    const canvas=document.getElementById("spark-card-canvas");
379:    app.innerHTML=`<section class="panel"><div class="kicker">Final Dossier</div><h2>${esc(r.lead_domain||"Generated")} Spark</h2><div class="result-grid"><div><strong>Cast</strong><span>${esc(r.cast||"Pending")}</span></div><div><strong>Signature</strong><span>${esc(r.spark_signature||r.provisional_spark_signature||"Pending")}</span></div><div><strong>Combat</strong><span>${esc(r.combat_capability||r.provisional_combat_capability||"Pending")}</span></div><div><strong>Domains</strong><span>${esc(manifested)}</span></div></div><p>${esc(r.profile_shape||"Spark profile generated.")}</p><div class="panel" style="margin-top:1rem"><div class="kicker">AI Image Prompt</div><p>Use this prompt to generate character art.</p><pre id="spark-image-prompt">${esc(prompt)}</pre></div><div class="card-maker"><div class="kicker">Collector Card</div><p>Generate an image from the prompt, then upload it here to build the Spark collector card.</p><input type="file" accept="image/*" onchange="Spark.loadCardImage(this)"><canvas id="spark-card-canvas" width="900" height="1260"></canvas></div><div class="actions"><button class="primary" type="button" onclick="Spark.saveCharacter()">Save Character</button><button class="secondary" type="button" onclick="Spark.copyImagePrompt()">Copy Image Prompt</button><button class="secondary" type="button" onclick="Spark.renderCard()">Render Collector Card</button><button class="secondary" type="button" onclick="Spark.downloadCard()">Download Card PNG</button><a class="btn secondary" href="/battles/">Open Battle Simulator</a><button class="secondary" type="button" onclick="Spark.reset()">Start Over</button></div><details><summary>Raw protocol output</summary><pre>${esc(JSON.stringify(r,null,2))}</pre></details></section>`;
380:    debug("final rendered; waiting for save choice"); setTimeout(renderCollectorCard,80);
397:    loadCardImage:function(input){readImageFile(input)},
398:    renderCard:function(){renderCollectorCard()},
399:    downloadCard:function(){downloadCollectorCard()}
```
