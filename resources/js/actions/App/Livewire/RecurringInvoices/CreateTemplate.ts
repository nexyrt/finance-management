import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\RecurringInvoices\CreateTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/CreateTemplate.php:7
 * @route '/recurring-invoices/template/create'
 */
const CreateTemplate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateTemplate.url(options),
    method: 'get',
})

CreateTemplate.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/template/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\RecurringInvoices\CreateTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/CreateTemplate.php:7
 * @route '/recurring-invoices/template/create'
 */
CreateTemplate.url = (options?: RouteQueryOptions) => {
    return CreateTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\RecurringInvoices\CreateTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/CreateTemplate.php:7
 * @route '/recurring-invoices/template/create'
 */
CreateTemplate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateTemplate.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\CreateTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/CreateTemplate.php:7
 * @route '/recurring-invoices/template/create'
 */
CreateTemplate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateTemplate.url(options),
    method: 'head',
})
export default CreateTemplate