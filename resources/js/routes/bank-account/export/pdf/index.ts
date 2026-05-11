import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CashFlowExportController::preview
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/bank-account/export/pdf/preview'
 */
export const preview = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(options),
    method: 'get',
})

preview.definition = {
    methods: ["get","head"],
    url: '/bank-account/export/pdf/preview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CashFlowExportController::preview
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/bank-account/export/pdf/preview'
 */
preview.url = (options?: RouteQueryOptions) => {
    return preview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowExportController::preview
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/bank-account/export/pdf/preview'
 */
preview.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CashFlowExportController::preview
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/bank-account/export/pdf/preview'
 */
preview.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: preview.url(options),
    method: 'head',
})
const pdf = {
    preview: Object.assign(preview, preview),
}

export default pdf