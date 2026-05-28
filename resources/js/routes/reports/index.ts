import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import profitLossF3bc4a from './profit-loss'
/**
* @see \App\Http\Controllers\ProfitLossReportController::profitLoss
 * @see app/Http/Controllers/ProfitLossReportController.php:17
 * @route '/reports/profit-loss'
 */
export const profitLoss = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profitLoss.url(options),
    method: 'get',
})

profitLoss.definition = {
    methods: ["get","head"],
    url: '/reports/profit-loss',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProfitLossReportController::profitLoss
 * @see app/Http/Controllers/ProfitLossReportController.php:17
 * @route '/reports/profit-loss'
 */
profitLoss.url = (options?: RouteQueryOptions) => {
    return profitLoss.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProfitLossReportController::profitLoss
 * @see app/Http/Controllers/ProfitLossReportController.php:17
 * @route '/reports/profit-loss'
 */
profitLoss.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profitLoss.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ProfitLossReportController::profitLoss
 * @see app/Http/Controllers/ProfitLossReportController.php:17
 * @route '/reports/profit-loss'
 */
profitLoss.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profitLoss.url(options),
    method: 'head',
})
const reports = {
    profitLoss: Object.assign(profitLoss, profitLossF3bc4a),
}

export default reports