/// <reference types="vitest" />
import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from 'tailwindcss';
import { defineConfig } from 'vite';

export default defineConfig({
    server: {
        host: '127.0.0.1'
    },
    test: {
        globals: true,
        environment: 'jsdom',
        setupFiles: './resources/js/tests/setup.ts',
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
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': path.resolve(__dirname, 'vendor/tightenco/ziggy')
        }
    },
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer]
        }
    }
});
