import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import pdf81d01d from './pdf'
/**
* @see \App\Http\Controllers\CashFlowExportController::pdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/cash-flow/export/pdf'
 */
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/cash-flow/export/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CashFlowExportController::pdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/cash-flow/export/pdf'
 */
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowExportController::pdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/cash-flow/export/pdf'
 */
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CashFlowExportController::pdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/cash-flow/export/pdf'
 */
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})
const exportMethod = {
    pdf: Object.assign(pdf, pdf81d01d),
}

export default exportMethod