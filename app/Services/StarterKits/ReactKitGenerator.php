<?php

namespace NikoGin\Services\StarterKits;

class ReactKitGenerator
{
    public static function generate(string $pluginDir, string $pluginPrefix, string $pluginName, array $directories): array
    {
        return [
            $pluginDir . '/package.json'                    => self::generatePackageJson($pluginName),
            $directories['src'] . '/dashboard.tsx'          => self::generateDashboardTsx($pluginPrefix),
            $directories['src'] . '/block.ts'               => self::generateIndex(),
            $directories['pages'] . '/DashboardPage.tsx'    => self::generateDashboardPageTsx($pluginPrefix),
            $pluginDir . '/webpack.config.js'               => self::generateWebpackConfig(),
            $pluginDir . '/tailwind.config.js'              => self::generateTailwindConfig($pluginPrefix),
            $pluginDir . '/postcss.config.js'               => self::generatePostcssConfig(),
            $pluginDir . '/tsconfig.json'                   => self::generateTsconfig(),
            $directories['styles'] . '/index.css'           => self::generateTailwindIndexCss($pluginPrefix),
            $directories['block-example'] . '/block.json'   => self::blockExampleJson($pluginPrefix),
            $directories['block-example'] . '/index.tsx'    => self::blockExample(),
            $directories['types'] . '/block-props.d.ts'     => self::blockProps(),
            $directories['types'] . '/require-context.d.ts' => self::requireContext(),
            $directories['services'] . '/BaseApi.ts'        => self::BaseApi($pluginPrefix),
            $directories['services'] . '/ExampleService.ts' => self::generateExampleService(),
        ];
    }

    private static function generatePackageJson(string $pluginSlug): string
    {
        $name = strtolower(preg_replace('/[^a-z0-9\-]/', '-', $pluginSlug));

        $package = [
            'name' => $name,
            'private' => true,
            'version' => '1.0.0',
            'scripts' => [
                'build' => 'wp-scripts build',
                'start' => 'wp-scripts start',
                'packages-update' => 'wp-scripts packages-update',
            ],
            'dependencies' => [
                '@tailwindcss/typography' => '^0.5.16',
                '@wordpress/api-fetch' => '^7.18.0',
                '@wordpress/components' => '^29.4.0',
                '@wordpress/element' => '^6.18.0',
                '@wordpress/i18n' => '^5.18.0',
                'dompurify' => '^3.2.4',
            ],
            'devDependencies' => [
                '@tailwindcss/postcss' => '^4.0.9',
                '@types/dompurify' => '^3.0.5',
                '@types/react' => '^19.0.10',
                '@types/react-dom' => '^19.0.4',
                '@types/wordpress__block-editor' => '^11.5.17',
                '@types/wordpress__blocks' => '^12.5.18',
                '@wordpress/scripts' => '^30.11.0',
                'autoprefixer' => '^10.4.20',
                'css-loader' => '^7.1.2',
                'postcss' => '^8.5.3',
                'postcss-loader' => '^8.1.1',
                'postcss-preset-env' => '^10.1.5',
                'style-loader' => '^4.0.0',
                'tailwindcss' => '^3.4.17',
                'typescript' => '^5.7.3',
            ],
        ];

        return json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private static function generateDashboardTsx(string $pluginPrefix): string
    {
        return <<<TSX
import {Container} from "react-dom/client";
import {createRoot} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

import {DashboardPage} from "@/pages/DashboardPage";
import '@/styles/index.css';

domReady(() => {
    const root = createRoot(
        // @ts-ignore
        document.getElementById('{$pluginPrefix}-dashboard') as Container
    );
    // @ts-ignore
    root.render(<DashboardPage />);
});
TSX;
    }

    private static function generateDashboardPageTsx(string $pluginPrefix): string
    {
        return <<<TSX
import React from 'react';
import { __ } from '@wordpress/i18n';

export const DashboardPage: React.FC = () => {
    return (
        <div className="{$pluginPrefix}-text-xl {$pluginPrefix}-font-semibold {$pluginPrefix}-p-4">
            {__('This is the dashboard page part.', '{$pluginPrefix}')}
        </div>
    );
};
TSX;
    }

    private static function generateWebpackConfig(): string
    {
        return <<<'JS'
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        'admin-dashboard': path.resolve(process.cwd(), 'src/dashboard.tsx'),
        'block': path.resolve(process.cwd(), 'src/block.ts'),
    },
    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules.filter(
                rule => !rule.test?.test?.('.css')
            ),
            {
                test: /\.css$/i,
                use: [
                    'style-loader',
                    {
                        loader: 'css-loader',
                        options: {
                            importLoaders: 1,
                        },
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [
                                    require('@tailwindcss/postcss'),
                                    require('autoprefixer'),
                                ],
                            },
                        },
                    },
                ],
            },
        ],
    },
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

    private static function generateTailwindConfig(string $pluginPrefix): string
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

    private static function generatePostcssConfig(): string
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

    private static function generateTsconfig(): string
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

    private static function generateTailwindIndexCss(string $pluginPrefix): string
    {
        return <<<CSS
@import url("https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;700&display=swap");

@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    html {
        margin-top: 0;
        font-family: "Public Sans", sans-serif !important;
    }
}

@layer utilities {
    /* Animation utilities */
    .{$pluginPrefix}-animate-fadeIn {
        animation: fadeIn 0.3s ease-in-out;
    }

    .{$pluginPrefix}-animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg);
        }
    }
}
CSS;
    }

    private static function blockExampleJson(string $pluginPrefix): string
    {
        return <<<JSON
{
  "apiVersion": 2,
  "name": "{$pluginPrefix}/block-example",
  "title": "Example Block",
  "category": "widgets",
  "icon": "smiley",
  "description": "A simple example block.",
  "editorScript": "file:../../block.js"
}
JSON;
    }


    private static function blockExample(): string
    {
        return <<<TSX
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType(metadata as any, {
    edit: () => {
        const blockProps = useBlockProps();
        return (
            <div {...blockProps}>
                <h3>Hello from Example Block</h3>
                <p>This is shown in WP Editor.</p>
            </div>
        );
    },
    save: () => {
        const blockProps = useBlockProps.save();
        return (
            <div {...blockProps}>
                <h3>Hello from Example Block</h3>
                <p>This is shown in Frontend.</p>
            </div>
        );
    },
});
TSX;
    }

    private static function generateIndex(): string
    {
        return <<<TS
// Automatically import all blocks in ./blocks
const context = require.context('./blocks', true, /\\.tsx?$/);
context.keys().forEach(context);
TS;
    }

    private static function blockProps(): string
    {
        return <<<TS
import { MyBlockProps } from '@/types/block-props';
TS;
    }

    private static function requireContext(): string
    {
        return <<<TS
declare const require: {
    context(path: string, deep?: boolean, filter?: RegExp): {
        keys(): string[];
        <T>(id: string): T;
    };
};
TS;
    }

    private static function BaseApi(string $pluginPrefix): string
    {
        return <<<TS
import apiFetch from '@wordpress/api-fetch';

export class BaseApi {
    protected static prefix = '/{$pluginPrefix}/v1';

    static get<T = any>(endpoint: string, options: { query?: Record<string, any> } = {}): Promise<T> {
        const queryString = options.query
            ? '?' + new URLSearchParams(options.query as Record<string, string>).toString()
            : '';

        return apiFetch({
            path: `\${this.prefix}\${endpoint}\${queryString}`,
            method: 'GET',
        });
    }

    static post<T = any>(endpoint: string, data: any, options: Record<string, any> = {}): Promise<T> {
        return apiFetch({
            path: `\${this.prefix}\${endpoint}`,
            method: 'POST',
            data,
            ...options,
        });
    }

    static put<T = any>(endpoint: string, data: any, options: Record<string, any> = {}): Promise<T> {
        return apiFetch({
            path: `\${this.prefix}\${endpoint}`,
            method: 'PUT',
            data,
            ...options,
        });
    }

    static delete<T = any>(endpoint: string, options: Record<string, any> = {}): Promise<T> {
        return apiFetch({
            path: `\${this.prefix}\${endpoint}`,
            method: 'DELETE',
            ...options,
        });
    }
}
TS;
    }

    private static function generateExampleService(): string
    {
        return <<<TS
import { BaseApi } from './BaseApi';

const EXAMPLE = '/example';

export const exampleService = {
    getAll: () => BaseApi.get(EXAMPLE),

    getOne: (id: number) => BaseApi.get(`\${EXAMPLE}/\${id}`),

    delete: (id: number) => BaseApi.delete(`\${EXAMPLE}/\${id}`),

    create: (data: any) => BaseApi.post(EXAMPLE, data),

    paginated(page: number = 1, search: string = '', perPage: number = 24) {
        const query: Record<string, string> = {
            page: page.toString(),
            per_page: perPage.toString(),
        };

        if (search.trim() !== '') {
            query.search = search;
        }

        return BaseApi.get(EXAMPLE, { query });
    },
};
TS;
    }


}
