<?php

namespace NikoGin\Services\StarterKits\Logic;

class ServicesGenerator
{

    public static function BaseApi(string $pluginPrefix): string
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

    public static function generateExampleService(): string
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