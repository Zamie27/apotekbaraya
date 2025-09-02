import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import fs from "fs";

export default defineConfig({
    // server: {
    //     // https: {
    //     //     key: fs.readFileSync(
    //     //         "C:/Users/zamie/.config/herd/config/valet/Certificates/apotekbaraya.test.key"
    //     //     ),
    //     //     cert: fs.readFileSync(
    //     //         "C:/Users/zamie/.config/herd/config/valet/Certificates/apotekbaraya.test.crt"
    //     //     ),
    //     // },
    //     host: "localhost",
    //     port: 5173,
    //     strictPort: true, // error kalau port bentrok

    //     cors: true,
    //     headers: {
    //         "Access-Control-Allow-Origin": "*",
    //         "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
    //         "Access-Control-Allow-Headers": "Content-Type, Authorization",
    //     },
    // },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
