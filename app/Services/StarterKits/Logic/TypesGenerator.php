<?php

namespace NikoGin\Services\StarterKits\Logic;

class TypesGenerator
{
    public static function blockProps(): string
    {
        return <<<TS
import { MyBlockProps } from '@/types/block-props';
TS;
    }

    public static function requireContext(): string
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
}