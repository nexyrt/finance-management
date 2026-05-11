import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Livewire\RecurringInvoices\Monthly\CreateInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/CreateInvoice.php:7
 * @route '/recurring-invoices/monthly/create'
 */
const CreateInvoice = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateInvoice.url(options),
    method: 'get',
})

CreateInvoice.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/monthly/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\RecurringInvoices\Monthly\CreateInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/CreateInvoice.php:7
 * @route '/recurring-invoices/monthly/create'
 */
CreateInvoice.url = (options?: RouteQueryOptions) => {
    return CreateInvoice.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\RecurringInvoices\Monthly\CreateInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/CreateInvoice.php:7
 * @route '/recurring-invoices/monthly/create'
 */
CreateInvoice.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateInvoice.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\Monthly\CreateInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/CreateInvoice.php:7
 * @route '/recurring-invoices/monthly/create'
 */
CreateInvoice.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateInvoice.url(options),
    method: 'head',
})
export default CreateInvoice