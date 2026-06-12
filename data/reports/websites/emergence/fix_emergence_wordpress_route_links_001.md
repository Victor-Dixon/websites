# Fix Emergence WordPress route links

Generated: 2026-06-05T01:43:51-05:00

## Before

```text
runtime/content/maskzero.site/the-emergence.html:694:              <a class="btn" href="/spark-generator.html">Generate Your Spark →</a>
runtime/content/maskzero.site/the-emergence.html:791:              <a class="btn" href="/spark-generator.html">Create a Spark First →</a>
```

## After stale-route scan

```text

```

## Good links

```text
runtime/content/maskzero.site/home.html:17:    <a href="/character-generator/"><strong>Generate your first Spark →</strong></a>
runtime/content/maskzero.site/home.html:92:    <a href="/spark-generator/">Generate Your Spark</a>
runtime/content/maskzero.site/home.html:93:    <a href="/battles/">Enter the What-If Arena</a>
runtime/content/maskzero.site/home.html:94:    <a href="/the-emergence/">Read The Emergence</a>
runtime/content/maskzero.site/character-generator.html:56:  <p><a href="/battles/">Enter Battles After Generating</a></p>
runtime/content/maskzero.site/battles.html:51:  <p><a href="/spark-generator/">Generate Your Spark First</a></p>
runtime/content/maskzero.site/the-emergence.html:694:              <a class="btn" href="/spark-generator/">Generate Your Spark →</a>
runtime/content/maskzero.site/the-emergence.html:791:              <a class="btn" href="/spark-generator/">Create a Spark First →</a>
runtime/content/maskzero.site/the-emergence.html:876:    <a href="/spark-generator/">Generate Your Spark</a> ·
runtime/content/maskzero.site/the-emergence.html:877:    <a href="/battles/">Enter the What-If Arena</a>
```

## Result

Replaced static `.html` links with WordPress slug routes:

```text
/spark-generator.html -> /spark-generator/
/character-generator.html -> /character-generator/
/battles.html -> /battles/
/battle-simulator.html -> /battles/
/the-emergence.html -> /the-emergence/
```
