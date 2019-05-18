let mix = require('laravel-mix');
let tailwindcss = require('tailwindcss');
require('laravel-mix-purgecss');

mix.disableNotifications()
   .js('resources/js/app.js', 'public/js')
   .postCss('resources/main.pcss', 'public/css', [
        tailwindcss('tailwind.js')
    ])
    .purgeCss({
        globs: [
            path.join(__dirname, 'resources/views/**/*.php'),
            path.join(__dirname, 'resources/js/**/*.js'),
            path.join(__dirname, 'resources/js/**/*.vue'),
            path.join(__dirname, 'resources/js/**/*.tsx'),
        ],

        extensions: ['html', 'js', 'php', 'vue', 'ts', 'tsx', 'jsx'],
    })
    .webpackConfig({
        module: {
            rules: [
                {
                    test: /\.tsx?$/,
                    loader: 'ts-loader',
                    exclude: /node_modules/
                }
            ]
        },
        resolve: {
            extensions: ['*', '.js', '.jsx', '.vue', '.ts', '.tsx']
        }
    })
    .version();
