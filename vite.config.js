import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss';
import vue from '@vitejs/plugin-vue';
import { defineConfig } from 'vite';

export default defineConfig(({ mode }) => ({
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1',   // forces IPv4, no ambiguity
    },
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
    build: {
        // Enable sourcemaps in dev only
        sourcemap: mode !== 'production',
        // Reduce CSS/JS chunk sizes
        cssMinify: 'lightningcss',
        minify: 'esbuild',
        // Rollup-specific optimizations
        rollupOptions: {
            output: {
                // Manual chunks to reduce vendor bundle size impact
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        // Separate commonly used large libraries
                        if (id.includes('alpinejs')) return 'alpine';
                        if (id.includes('chart.js') || id.includes('chartjs')) return 'charts';
                        return 'vendor';
                    }
                },
                // Chunk naming for better caching
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash][extname]',
            },
        },
        // Increase chunk size warning limit (adjust if needed)
        chunkSizeWarningLimit: 600,
        // Enable/disable brotli size reporting
        reportCompressedSize: false,
    },
    // Optimize for production builds
    esbuild: {
        drop: mode === 'production' ? ['console', 'debugger'] : [],
    },
}));