/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                'easym-blue': '#2563eb', // Màu nhận diện thương hiệu EasyM
            }
        },
    },
    plugins: [],
}