# Spark OS browser truth harness

Generated: 2026-06-05T08:07:56-05:00

## Test output

```text
SPARK_OS_STATIC_SOURCE_TRUTH=PASS
```

## Manual browser probe

```javascript
/*
Manual browser-console probe for https://maskzero.site/spark-os/
Paste into DevTools Console if phone/desktop behavior disagrees.

Expected:
1. Clicks Start Spark Quiz
2. Clicks first Q1 option
3. Reports selected Q1 and progress text
*/
(function(){
  const out = [];
  function log(k,v){ out.push(k + "=" + v); }
  const start = document.querySelector('[data-action="start"]');
  log("START_FOUND", !!start);
  if (start) start.click();

  setTimeout(function(){
    const q1 = document.querySelector('[data-question="1"][data-answer]');
    log("Q1_FOUND", !!q1);
    if (q1) q1.click();

    setTimeout(function(){
      const selected = document.querySelector('[data-question="1"][data-answer][data-selected="1"]');
      const progress = document.querySelector('.progress')?.innerText || '';
      log("Q1_SELECTED", !!selected);
      log("PROGRESS", progress.replace(/\s+/g,' ').trim());
      console.log(out.join("\n"));
    }, 250);
  }, 250);
})();
```

## Result

Added a source/runtime branch truth harness. This prevents treating route grep as proof that buttons work.
