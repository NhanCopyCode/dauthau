// import { defineConfig } from 'vite';
// import tailwindcss from "@tailwindcss/vite";
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     plugins: [
//         tailwindcss(),
//         laravel({
//             input: ["resources/css/app.css", "resources/js/app.js"],
//             refresh: true,
//         }),
//     ],
// });

import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],

           
            refresh: ["resources/views/**/*.blade.php", "routes/**/*.php"],
        }),

        tailwindcss(),
    ],

    server: {
        watch: {
            ignored: [
                "**/node_modules/**",
                "**/vendor/**",
                "**/storage/**",
                "**/.git/**",
            ],
        },
    },
});
