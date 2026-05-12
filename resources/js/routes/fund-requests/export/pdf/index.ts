import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
 * @see routes/web.php:274
 * @route '/fund-requests/export/pdf/preview'
 */
export const preview = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(options),
    method: 'get',
})

preview.definition = {
    methods: ["get","head"],
    url: '/fund-requests/export/pdf/preview',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:274
 * @route '/fund-requests/export/pdf/preview'
 */
preview.url = (options?: RouteQueryOptions) => {
    return preview.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:274
 * @route '/fund-requests/export/pdf/preview'
 */
preview.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:274
 * @route '/fund-requests/export/pdf/preview'
 */
preview.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: preview.url(options),
    method: 'head',
})
const pdf = {
    preview: Object.assign(preview, preview),
}

export default pdf