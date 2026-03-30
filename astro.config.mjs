import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';

// 本番URL（サイトマップ・canonical 用）。サブパス配信の場合は base も検討すること。
const site = 'https://maruyoshi-ironworks.com';

/** public/recruit に置いた静的 HTML（リクルート子サイト） */
const recruitMicrositePages = [
  '/recruit/',
  '/recruit/index.html',
  '/recruit/job.html',
  '/recruit/job-newgraduate.html',
  '/recruit/interview.html',
  '/recruit/entry.html',
].map((path) => new URL(path.replace(/^\//, ''), site).href);

export default defineConfig({
  site,
  output: 'static',
  compressHTML: true,
  build: {
    // 従来の *.html フラット構成に近づけ、recruit.html と recruit/ 以下を共存させる
    format: 'file',
  },
  integrations: [
    sitemap({
      customPages: recruitMicrositePages,
    }),
  ],
});
