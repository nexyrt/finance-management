import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Invoices\Create::__invoke
 * @see app/Livewire/Invoices/Create.php:7
 * @route '/invoices/create'
 */
const Create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Create.url(options),
    method: 'get',
})

Create.definition = {
    methods: ["get","head"],
    url: '/invoices/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Invoices\Create::__invoke
 * @see app/Livewire/Invoices/Create.php:7
 * @route '/invoices/create'
 */
Create.url = (options?: RouteQueryOptions) => {
    return Create.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Invoices\Create::__invoke
 * @see app/Livewire/Invoices/Create.php:7
 * @route '/invoices/create'
 */
Create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Create.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Invoices\Create::__invoke
 * @see app/Livewire/Invoices/Create.php:7
 * @route '/invoices/create'
 */
Create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Create.url(options),
    method: 'head',
})
export default Create