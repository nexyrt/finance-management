import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\RecurringInvoices\Index::__invoke
 * @see app/Livewire/RecurringInvoices/Index.php:7
 * @route '/recurring-invoices'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\RecurringInvoices\Index::__invoke
 * @see app/Livewire/RecurringInvoices/Index.php:7
 * @route '/recurring-invoices'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\RecurringInvoices\Index::__invoke
 * @see app/Livewire/RecurringInvoices/Index.php:7
 * @route '/recurring-invoices'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\Index::__invoke
 * @see app/Livewire/RecurringInvoices/Index.php:7
 * @route '/recurring-invoices'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index