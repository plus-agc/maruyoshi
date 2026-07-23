import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import { rename } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';

// 本番URL（サイトマップ・canonical 用）。サブパス配信の場合は base も検討すること。
const site = 'https://maruyoshi-ironworks.com';

/**
 * 企業サイトの採用情報を /recruit.html として出力する。
 * （/recruit/ 子サイトの index と pages 直下のルートが衝突するため inject + リネーム）
 */
function recruitHtmlRoute() {
  const entrypoint = fileURLToPath(
    new URL('./src/corporate/recruit.astro', import.meta.url),
  );
  return {
    name: 'recruit-html-route',
    hooks: {
      'astro:config:setup': ({ injectRoute }) => {
        injectRoute({
          pattern: '/recruit.html',
          entrypoint,
        });
      },
      'astro:build:done': async ({ dir }) => {
        const from = new URL('recruit.html.html', dir);
        const to = new URL('recruit.html', dir);
        try {
          await rename(from, to);
        } catch (error) {
          if (error && typeof error === 'object' && 'code' in error && error.code === 'ENOENT') {
            return;
          }
          throw error;
        }
      },
    },
  };
}

export default defineConfig({
  site,
  output: 'static',
  compressHTML: true,
  build: {
    // 企業サイトの *.html とリクルート子サイトの /recruit/*.html を共存させる
    format: 'preserve',
  },
  integrations: [sitemap(), recruitHtmlRoute()],
});
