# Add explicit Spark save and AI image prompt

Generated: 2026-06-05T08:18:46-05:00

## Local verification

```text
65:/* DreamOS explicit save + AI image prompt v1 */
150:    const keyChars="dreamos.savedSparkCharacters.v1";
160:  function buildImagePrompt(payload){
190:      image_prompt: buildImagePrompt(payload)
221:    const prompt=buildImagePrompt(r);
222:    app.innerHTML=`<section class="panel"><div class="kicker">Final Dossier</div><h2>${esc(r.lead_domain||"Generated")} Spark</h2><div class="result-grid"><div><strong>Cast</strong><span>${esc(r.cast||"Pending")}</span></div><div><strong>Signature</strong><span>${esc(r.spark_signature||r.provisional_spark_signature||"Pending")}</span></div><div><strong>Combat</strong><span>${esc(r.combat_capability||r.provisional_combat_capability||"Pending")}</span></div><div><strong>Domains</strong><span>${esc(manifested)}</span></div></div><p>${esc(r.profile_shape||"Spark profile generated.")}</p><div class="panel" style="margin-top:1rem"><div class="kicker">AI Image Prompt</div><p>Use this prompt to generate character art.</p><pre id="spark-image-prompt">${esc(prompt)}</pre></div><div class="actions"><button class="primary" type="button" onclick="Spark.saveCharacter()">Save Character</button><button class="secondary" type="button" onclick="Spark.copyImagePrompt()">Copy Image Prompt</button><a class="btn secondary" href="/battles/">Open Battle Simulator</a><button class="secondary" type="button" onclick="Spark.reset()">Start Over</button></div><details><summary>Raw protocol output</summary><pre>${esc(JSON.stringify(r,null,2))}</pre></details></section>`;
238:    saveCharacter:function(){const s=load();const r=s.final_result||s.domain_result;if(!r){debug("nothing to save");return}const character=saveCharacterRecord(r);debug("character saved");app.querySelector(".actions").insertAdjacentHTML("afterbegin",`<a class="btn primary" href="/battles/?character_id=${encodeURIComponent(character.id)}">Enter the What-If Arena</a>`)},
239:    copyImagePrompt:function(){const data=pendingCharacterRecord();copyText(data.image_prompt)}
25:<body data-dreamos-battle-character-reader="1">
37:  const key="dreamos.savedSparkCharacters.v1";
47:    const id=params.get("character_id");
58:    app.innerHTML=`<div class="card"><h2>${esc(c.lead_domain||"Generated")} Spark</h2><div class="grid"><div><strong>Cast</strong><span>${esc(c.cast||"Pending")}</span></div><div><strong>Signature</strong><span>${esc(c.spark_signature||"Pending")}</span></div><div><strong>Combat</strong><span>${esc(c.combat_capability||"Pending")}</span></div><div><strong>Domains</strong><span>${esc((c.manifested||[]).join(", "))}</span></div></div><p>${esc(c.profile_shape||"")}</p><details><summary>Character record</summary><pre>${esc(JSON.stringify(c,null,2))}</pre></details></div><div class="card"><h3>Saved Characters</h3>${chars.map(x=>`<p><a href="/battles/?character_id=${encodeURIComponent(x.id)}">${esc(x.lead_domain||"Spark")} · <small>${esc(x.created_at||"")}</small></a></p>`).join("")}</div>`;
```

## Live verification

```text
--- headers ---
HTTP/2 200 
content-type: text/html
server: LiteSpeed
--- markers ---
65:/* DreamOS explicit save + AI image prompt v1 */
150:    const keyChars="dreamos.savedSparkCharacters.v1";
160:  function buildImagePrompt(payload){
190:      image_prompt: buildImagePrompt(payload)
221:    const prompt=buildImagePrompt(r);
222:    app.innerHTML=`<section class="panel"><div class="kicker">Final Dossier</div><h2>${esc(r.lead_domain||"Generated")} Spark</h2><div class="result-grid"><div><strong>Cast</strong><span>${esc(r.cast||"Pending")}</span></div><div><strong>Signature</strong><span>${esc(r.spark_signature||r.provisional_spark_signature||"Pending")}</span></div><div><strong>Combat</strong><span>${esc(r.combat_capability||r.provisional_combat_capability||"Pending")}</span></div><div><strong>Domains</strong><span>${esc(manifested)}</span></div></div><p>${esc(r.profile_shape||"Spark profile generated.")}</p><div class="panel" style="margin-top:1rem"><div class="kicker">AI Image Prompt</div><p>Use this prompt to generate character art.</p><pre id="spark-image-prompt">${esc(prompt)}</pre></div><div class="actions"><button class="primary" type="button" onclick="Spark.saveCharacter()">Save Character</button><button class="secondary" type="button" onclick="Spark.copyImagePrompt()">Copy Image Prompt</button><a class="btn secondary" href="/battles/">Open Battle Simulator</a><button class="secondary" type="button" onclick="Spark.reset()">Start Over</button></div><details><summary>Raw protocol output</summary><pre>${esc(JSON.stringify(r,null,2))}</pre></details></section>`;
238:    saveCharacter:function(){const s=load();const r=s.final_result||s.domain_result;if(!r){debug("nothing to save");return}const character=saveCharacterRecord(r);debug("character saved");app.querySelector(".actions").insertAdjacentHTML("afterbegin",`<a class="btn primary" href="/battles/?character_id=${encodeURIComponent(character.id)}">Enter the What-If Arena</a>`)},
239:    copyImagePrompt:function(){const data=pendingCharacterRecord();copyText(data.image_prompt)}
```
