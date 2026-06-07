# Restore original Spark generator URL as static page

Generated: 2026-06-05T08:09:47-05:00

## Local verification

```text
42:<body data-dreamos-original-spark-static="1">
54:    <button class="primary" type="button" onclick="Spark.start()">Start Spark Quiz</button>
89:      opts(q).map(([l,t])=>`<button class="option" type="button" onclick="Spark.answer('${phase}','${q.q}','${l}')" ${selected===l?'data-selected="1"':''}><strong>${l}</strong><span>${esc(t)}</span></button>`).join("")
101:    app.innerHTML=`<section class="panel"><div class="kicker">Pass 1</div><h2>Domain Typing</h2><p>Answer all 28 questions.</p>${progress("Domain pass",n,domain.length)}<div class="question-list">${domain.map(q=>qcard(q,s.domain_answers,"domain")).join("")}</div><div class="actions"><button class="primary" type="button" onclick="Spark.submitDomain()" ${n<domain.length?"disabled":""}>Generate Spark Pass 1</button><button class="secondary" type="button" onclick="Spark.reset()">Reset</button></div></section>`;
106:    app.innerHTML=`<section class="panel"><div class="kicker">Pass 2</div><h2>${esc(r.lead_domain||"Spark")} Flavor Pass</h2><p><strong>Manifested:</strong> ${esc((r.manifested||[]).join(", ")||"Pending")}</p>${progress("Flavor pass",n,qs.length)}<div class="question-list">${qs.map(q=>qcard(q,s.flavor_answers,"flavor")).join("")}</div><div class="actions"><button class="primary" type="button" onclick="Spark.submitFlavor()" ${qs.length&&n<qs.length?"disabled":""}>Build Final Dossier</button><button class="secondary" type="button" onclick="Spark.reset()">Reset</button></div></section>`;
125:    submitDomain:async function(){const s=load();debug("submit domain");try{const r=await post({answers:s.domain_answers||{},source:"original-spark-static"});s.phase="flavor";s.domain_result=r;s.flavor_answers=s.flavor_answers||{};save(s);renderFlavor();window.scrollTo({top:0,behavior:"smooth"})}catch(e){error(e)}},
126:    submitFlavor:async function(){const s=load();debug("submit flavor");try{const r=await post({answers:s.domain_answers||{},flavor_answers:s.flavor_answers||{},source:"original-spark-static"});s.phase="final";s.final_result=r;save(s);renderFinal();window.scrollTo({top:0,behavior:"smooth"})}catch(e){error(e)}}
44210 /data/data/com.termux/files/home/projects/websites/runtime/content/dadudekc.site/spark-generator/index.html
```

## Result

Built clean static Spark page directly for /spark-generator/.

## Live verification

```text
--- headers ---
HTTP/2 200 
content-type: text/html
server: LiteSpeed
--- body markers ---
42:<body data-dreamos-original-spark-static="1">
54:    <button class="primary" type="button" onclick="Spark.start()">Start Spark Quiz</button>
59:<div id="debug" class="debug">Original Spark static ready</div>
89:      opts(q).map(([l,t])=>`<button class="option" type="button" onclick="Spark.answer('${phase}','${q.q}','${l}')" ${selected===l?'data-selected="1"':''}><strong>${l}</strong><span>${esc(t)}</span></button>`).join("")
101:    app.innerHTML=`<section class="panel"><div class="kicker">Pass 1</div><h2>Domain Typing</h2><p>Answer all 28 questions.</p>${progress("Domain pass",n,domain.length)}<div class="question-list">${domain.map(q=>qcard(q,s.domain_answers,"domain")).join("")}</div><div class="actions"><button class="primary" type="button" onclick="Spark.submitDomain()" ${n<domain.length?"disabled":""}>Generate Spark Pass 1</button><button class="secondary" type="button" onclick="Spark.reset()">Reset</button></div></section>`;
106:    app.innerHTML=`<section class="panel"><div class="kicker">Pass 2</div><h2>${esc(r.lead_domain||"Spark")} Flavor Pass</h2><p><strong>Manifested:</strong> ${esc((r.manifested||[]).join(", ")||"Pending")}</p>${progress("Flavor pass",n,qs.length)}<div class="question-list">${qs.map(q=>qcard(q,s.flavor_answers,"flavor")).join("")}</div><div class="actions"><button class="primary" type="button" onclick="Spark.submitFlavor()" ${qs.length&&n<qs.length?"disabled":""}>Build Final Dossier</button><button class="secondary" type="button" onclick="Spark.reset()">Reset</button></div></section>`;
125:    submitDomain:async function(){const s=load();debug("submit domain");try{const r=await post({answers:s.domain_answers||{},source:"original-spark-static"});s.phase="flavor";s.domain_result=r;s.flavor_answers=s.flavor_answers||{};save(s);renderFlavor();window.scrollTo({top:0,behavior:"smooth"})}catch(e){error(e)}},
126:    submitFlavor:async function(){const s=load();debug("submit flavor");try{const r=await post({answers:s.domain_answers||{},flavor_answers:s.flavor_answers||{},source:"original-spark-static"});s.phase="final";s.final_result=r;save(s);renderFinal();window.scrollTo({top:0,behavior:"smooth"})}catch(e){error(e)}}
```
