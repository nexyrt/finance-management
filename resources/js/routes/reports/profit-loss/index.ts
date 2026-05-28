import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ProfitLossReportController::pdf
 * @see app/Http/Controllers/ProfitLossReportController.php:43
 * @route '/reports/profit-loss/pdf'
 */
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/reports/profit-loss/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProfitLossReportController::pdf
 * @see app/Http/Controllers/ProfitLossReportController.php:43
 * @route '/reports/profit-loss/pdf'
 */
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProfitLossReportController::pdf
 * @see app/Http/Controllers/ProfitLossReportController.php:43
 * @route '/reports/profit-loss/pdf'
 */
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ProfitLossReportController::pdf
 * @see app/Http/Controllers/ProfitLossReportController.php:43
 * @route '/reports/profit-loss/pdf'
 */
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})
const profitLoss = {
    pdf: Object.assign(pdf, pdf),
}

export default profitLoss