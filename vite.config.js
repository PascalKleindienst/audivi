/// <reference types="vitest" />
import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        host: '127.0.0.1'
    },
    test: {
        globals: true,
        environment: 'jsdom',
        coverage: {
            exclude: ['**/node_modules/**', '**/dist/**', 'public/**', '**/vendor/**'],
            enabled: true,
            provider: 'v8',
            reporter: ['text', 'json', 'html']
        }
    },
    plugins: [
        laravel({
            input: 'resources/js/app.ts',
            refresh: true
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false
                }
            }
        })
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url))
        }
    }
});
