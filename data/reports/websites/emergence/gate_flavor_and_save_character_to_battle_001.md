# Gate flavor questions and save Spark characters to battle simulator

Generated: 2026-06-05T08:16:06-05:00

## Local verification

```text
42:<body data-dreamos-original-spark-static="1">
64:/* DreamOS gated flavor + character save v1 */
134:  function saveCharacterRecord(payload){
138:      source:"original-spark-static",
149:    const keyChars="dreamos.savedSparkCharacters.v1";
171:    const character=saveCharacterRecord(r);
172:    app.innerHTML=`<section class="panel"><div class="kicker">Final Dossier</div><h2>${esc(r.lead_domain||"Generated")} Spark</h2><div class="result-grid"><div><strong>Cast</strong><span>${esc(r.cast||"Pending")}</span></div><div><strong>Signature</strong><span>${esc(r.spark_signature||r.provisional_spark_signature||"Pending")}</span></div><div><strong>Combat</strong><span>${esc(r.combat_capability||r.provisional_combat_capability||"Pending")}</span></div><div><strong>Domains</strong><span>${esc(manifested)}</span></div></div><p>${esc(r.profile_shape||"Spark profile generated.")}</p><p><strong>Saved:</strong> Character record stored for Battle Simulator.</p><div class="actions"><a class="btn primary" href="/battles/?character_id=${encodeURIComponent(character.id)}">Enter the What-If Arena</a><button class="secondary" type="button" onclick="Spark.reset()">Start Over</button></div><details><summary>Raw protocol output</summary><pre>${esc(JSON.stringify(r,null,2))}</pre></details></section>`;
186:    submitDomain:async function(){const s=load();debug("submit domain");try{const r=await post({answers:s.domain_answers||{},source:"original-spark-static"});s.phase="flavor";s.domain_result=r;s.flavor_answers=s.flavor_answers||{};save(s);renderFlavor();window.scrollTo({top:0,behavior:"smooth"})}catch(e){error(e)}},
187:    submitFlavor:async function(){const s=load();debug("submit flavor");try{const r=await post({answers:s.domain_answers||{},flavor_answers:s.flavor_answers||{},source:"original-spark-static"});s.phase="final";s.final_result=r;save(s);renderFinal();window.scrollTo({top:0,behavior:"smooth"})}catch(e){error(e)}}
25:<body data-dreamos-battle-character-reader="1">
37:  const key="dreamos.savedSparkCharacters.v1";
38:  const currentKey="dreamos.currentSparkCharacter.v1";
47:    const id=params.get("character_id");
58:    app.innerHTML=`<div class="card"><h2>${esc(c.lead_domain||"Generated")} Spark</h2><div class="grid"><div><strong>Cast</strong><span>${esc(c.cast||"Pending")}</span></div><div><strong>Signature</strong><span>${esc(c.spark_signature||"Pending")}</span></div><div><strong>Combat</strong><span>${esc(c.combat_capability||"Pending")}</span></div><div><strong>Domains</strong><span>${esc((c.manifested||[]).join(", "))}</span></div></div><p>${esc(c.profile_shape||"")}</p><details><summary>Character record</summary><pre>${esc(JSON.stringify(c,null,2))}</pre></details></div><div class="card"><h3>Saved Characters</h3>${chars.map(x=>`<p><a href="/battles/?character_id=${encodeURIComponent(x.id)}">${esc(x.lead_domain||"Spark")} · <small>${esc(x.created_at||"")}</small></a></p>`).join("")}</div>`;
```
