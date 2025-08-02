<?php

namespace NikoGin\Services\StarterKits\Logic;

class BlockGenerator
{
    public static function blockExample(): string
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

    public static function generateIndex(): string
    {
        return <<<TS
// Automatically import all blocks in ./blocks
const context = require.context('./blocks', true, /\\.tsx?$/);
context.keys().forEach(context);
TS;
    }

    public static function blockExampleJson(string $pluginPrefix): string
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

    public static function blockExampleScc(): string
    {
        return <<<CSS
.block-example {
  background-color: red;
}
CSS;
    }
}