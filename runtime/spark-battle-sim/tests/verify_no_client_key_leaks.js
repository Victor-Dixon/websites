const fs = require("fs");
const path = require("path");

const root = path.resolve(__dirname, "..");
const clientDir = path.join(root, "client");

function walk(dir) {
  if (!fs.existsSync(dir)) return [];
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap(entry => {
    const p = path.join(dir, entry.name);
    return entry.isDirectory() ? walk(p) : [p];
  });
}

const badPatterns = [
  /api\.anthropic\.com/i,
  /ANTHROPIC_API_KEY/i,
  /x-api-key/i,
  /claude-sonnet/i
];

const offenders = [];

for (const file of walk(clientDir)) {
  const text = fs.readFileSync(file, "utf8");
  for (const pattern of badPatterns) {
    if (pattern.test(text)) {
      offenders.push(`${file}: ${pattern}`);
    }
  }
}

if (offenders.length) {
  console.error("CLIENT_SECRET_LEAK_SCAN=FAIL");
  console.error(offenders.join("\n"));
  process.exit(1);
}

console.log("CLIENT_SECRET_LEAK_SCAN=PASS");
