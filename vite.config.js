import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/layouts/admin.css',
                'resources/css/layouts/forms.css',
                'resources/css/layouts/security.css',
                'resources/css/layouts/resident.css',
                'resources/css/auth/admin.css',
                'resources/css/auth/security.css',
                'resources/css/auth/login.css',
                'resources/css/auth/reset-password.css',
                'resources/css/pages/admin/users/index.css',
                'resources/css/pages/admin/blocks/index.css',
                'resources/css/pages/admin/floors/index.css',
                'resources/css/pages/admin/apartments/index.css',
                'resources/css/pages/admin/apartments/show.css',
                'resources/css/pages/admin/blocks/show.css',
                'resources/css/pages/admin/floors/show.css',
                'resources/css/pages/resident/profile/index.css',
                'resources/css/pages/resident/members/index.css'
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
