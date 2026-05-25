import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ProfitLossReportController::index
 * @see app/Http/Controllers/ProfitLossReportController.php:14
 * @route '/reports/profit-loss'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/reports/profit-loss',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProfitLossReportController::index
 * @see app/Http/Controllers/ProfitLossReportController.php:14
 * @route '/reports/profit-loss'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProfitLossReportController::index
 * @see app/Http/Controllers/ProfitLossReportController.php:14
 * @route '/reports/profit-loss'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ProfitLossReportController::index
 * @see app/Http/Controllers/ProfitLossReportController.php:14
 * @route '/reports/profit-loss'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const ProfitLossReportController = { index }

export default ProfitLossReportController