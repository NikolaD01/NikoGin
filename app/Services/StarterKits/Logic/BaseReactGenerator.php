<?php

namespace NikoGin\Services\StarterKits\Logic;

class BaseReactGenerator
{
    public static function generatePackageJson(string $pluginSlug): string
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
                'autoprefixer' => '^10.4.21',
                'css-loader' => '^7.1.2',
                'mini-css-extract-plugin' => '^2.9.2',
                'postcss' => '^8.5.3',
                'postcss-loader' => '^8.1.1',
                'postcss-preset-env' => '^10.1.5',
                'sass' => '^1.89.2',
                'sass-loader' => '^16.0.5',
                'style-loader' => '^4.0.0',
                'tailwindcss' => '^3.4.17',
                'typescript' => '^5.7.3',
                'webpack' => '^5.101.0',
                'webpack-cli' => '^6.0.1',
                'webpack-remove-empty-scripts' => '^1.1.1',
            ],
        ];

        return json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public static function generateDashboardTsx(string $pluginPrefix): string
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

    public static function generateDashboardPageTsx(string $pluginPrefix): string
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







    public static function generateTailwindIndexCss(string $pluginPrefix): string
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
  .pt-animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
  }

  .pt-animate-spin {
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


}