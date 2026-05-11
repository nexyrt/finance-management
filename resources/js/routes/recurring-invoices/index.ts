import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import template from './template'
import monthly from './monthly'
/**
* @see \App\Livewire\RecurringInvoices\Index::__invoke
 * @see app/Livewire/RecurringInvoices/Index.php:7
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
* @see \App\Livewire\RecurringInvoices\Index::__invoke
 * @see app/Livewire/RecurringInvoices/Index.php:7
 * @route '/recurring-invoices'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\RecurringInvoices\Index::__invoke
 * @see app/Livewire/RecurringInvoices/Index.php:7
 * @route '/recurring-invoices'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\Index::__invoke
 * @see app/Livewire/RecurringInvoices/Index.php:7
 * @route '/recurring-invoices'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const recurringInvoices = {
    index: Object.assign(index, index),
template: Object.assign(template, template),
monthly: Object.assign(monthly, monthly),
}

export default recurringInvoices