import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\InvoiceController::excel
 * @see app/Http/Controllers/InvoiceController.php:250
 * @route '/invoices/export/excel'
 */
export const excel = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: excel.url(options),
    method: 'get',
})

excel.definition = {
    methods: ["get","head"],
    url: '/invoices/export/excel',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InvoiceController::excel
 * @see app/Http/Controllers/InvoiceController.php:250
 * @route '/invoices/export/excel'
 */
excel.url = (options?: RouteQueryOptions) => {
    return excel.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::excel
 * @see app/Http/Controllers/InvoiceController.php:250
 * @route '/invoices/export/excel'
 */
excel.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: excel.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InvoiceController::excel
 * @see app/Http/Controllers/InvoiceController.php:250
 * @route '/invoices/export/excel'
 */
excel.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: excel.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::pdf
 * @see app/Http/Controllers/InvoiceController.php:258
 * @route '/invoices/export/pdf'
 */
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/invoices/export/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InvoiceController::pdf
 * @see app/Http/Controllers/InvoiceController.php:258
 * @route '/invoices/export/pdf'
 */
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::pdf
 * @see app/Http/Controllers/InvoiceController.php:258
 * @route '/invoices/export/pdf'
 */
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\InvoiceController::pdf
 * @see app/Http/Controllers/InvoiceController.php:258
 * @route '/invoices/export/pdf'
 */
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})
const exportMethod = {
    excel: Object.assign(excel, excel),
pdf: Object.assign(pdf, pdf),
}

export default exportMethod