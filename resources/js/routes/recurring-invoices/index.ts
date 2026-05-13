import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import templates from './templates'
import monthly from './monthly'
/**
* @see \App\Http\Controllers\RecurringInvoiceController::index
 * @see app/Http/Controllers/RecurringInvoiceController.php:18
 * @route '/recurring-invoices'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::index
 * @see app/Http/Controllers/RecurringInvoiceController.php:18
 * @route '/recurring-invoices'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::index
 * @see app/Http/Controllers/RecurringInvoiceController.php:18
 * @route '/recurring-invoices'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RecurringInvoiceController::index
 * @see app/Http/Controllers/RecurringInvoiceController.php:18
 * @route '/recurring-invoices'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const recurringInvoices = {
    index: Object.assign(index, index),
templates: Object.assign(templates, templates),
monthly: Object.assign(monthly, monthly),
}

export default recurringInvoices