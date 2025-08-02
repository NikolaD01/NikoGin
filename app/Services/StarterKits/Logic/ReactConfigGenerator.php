<?php

namespace NikoGin\Services\StarterKits\Logic;

class ReactConfigGenerator
{

    public static function generateWebpackConfig(): string
    {
        return <<<'JS'
const path = require('path');
const glob = require('glob');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

// Define entry points
const entries = {
    'admin-dashboard': path.resolve(__dirname, 'src/dashboard.tsx'),
    'locations-dashboard': path.resolve(__dirname, 'src/locations.tsx'),
    'clients-dashboard': path.resolve(__dirname, 'src/clients.tsx'),
    'materials-services-dashboard': path.resolve(__dirname, 'src/materials-services.tsx'),
    'block': path.resolve(__dirname, 'src/block.ts'),
};

// Add SCSS-only entries for each block (e.g., blocks/example/style.css)
glob.sync('./src/blocks/**/index.scss').forEach((file) => {
    const match = file.match(/blocks\/([^/]+)\/index\.scss$/);
    if (match) {
        const blockName = match[1];
        const entryName = `blocks/${blockName}/style`;
        entries[entryName] = path.resolve(__dirname, file);
    }
});

module.exports = {
    ...defaultConfig,
    entry: entries,
    module: {
        ...defaultConfig.module,
        rules: [
            // Remove default CSS/SCSS rules
            ...defaultConfig.module.rules.filter(
                rule => !rule.test?.test?.('.css') && !rule.test?.test?.('.scss')
            ),
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: { importLoaders: 1 },
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [
                                    require('tailwindcss'),
                                    require('autoprefixer'),
                                ],
                            },
                        },
                    },
                    'sass-loader',
                ],
            },
        ],
    },
    plugins: [
        new RemoveEmptyScriptsPlugin(), // Prevent style.js
        new MiniCssExtractPlugin({
            filename: ({ chunk }) =>
                chunk.name.startsWith('blocks/')
                    ? `${chunk.name}.css`
                    : '[name].css',
        }),
        ...defaultConfig.plugins.filter(
            (plugin) => plugin.constructor.name !== 'RtlCssPlugin'
        ),
    ],
    resolve: {
        ...defaultConfig.resolve,
        alias: {
            ...defaultConfig.resolve.alias,
            '@': path.resolve(__dirname, 'src'),
        },
    },
};
JS;
    }


    public static function generateTailwindConfig(string $pluginPrefix): string
    {
        return <<<JS
module.exports = {
    content: [
        './src/**/*.{js,jsx,ts,tsx}',
    ],
    prefix: '{$pluginPrefix}-',
    plugins: [require('@tailwindcss/typography')],
};
JS;
    }

    public static function generatePostcssConfig(): string
    {
        return <<<JS
module.exports = {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
}
JS;
    }

    public static function generateTsconfig(): string
    {
        return <<<JSON
{
    "compilerOptions": {
        "target": "es2015",
        "lib": [
            "dom",
            "dom.iterable",
            "esnext"
        ],
        "allowJs": true,
        "skipLibCheck": true,
        "esModuleInterop": true,
        "allowSyntheticDefaultImports": true,
        "strict": true,
        "forceConsistentCasingInFileNames": true,
        "noFallthroughCasesInSwitch": true,
        "module": "esnext",
        "moduleResolution": "node",
        "resolveJsonModule": true,
        "isolatedModules": true,
        "jsx": "react-jsx",
        "baseUrl": "src",
        "paths": {
            "@/*": ["*"]
        }
    },
    "include": [
        "src"
    ]
}
JSON;
    }
}