/**
 * @astrojs/sitemap が astro:routes:resolved 未発火時に _routes が undefined で落ちる件の回避。
 * 公式修正まで postinstall で適用する。
 */
import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const target = path.join(
  __dirname,
  "..",
  "node_modules",
  "@astrojs",
  "sitemap",
  "dist",
  "index.js",
);

if (!fs.existsSync(target)) {
  console.warn("[patch-astrojs-sitemap] skip: file not found", target);
  process.exit(0);
}

let code = fs.readFileSync(target, "utf8");
const needle = "const routeUrls = _routes.reduce";
if (!code.includes(needle)) {
  if (code.includes("(_routes ?? []).reduce")) {
    console.log("[patch-astrojs-sitemap] already applied");
  } else {
    console.warn("[patch-astrojs-sitemap] pattern not found; skip");
  }
  process.exit(0);
}

code = code.replace(
  needle,
  "const routeUrls = (_routes ?? []).reduce",
);
fs.writeFileSync(target, code);
console.log("[patch-astrojs-sitemap] patched", target);
