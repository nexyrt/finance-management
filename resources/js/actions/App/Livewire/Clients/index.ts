import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Clients\Index::__invoke
 * @see app/Livewire/Clients/Index.php:7
 * @route '/clients'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/clients',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Clients\Index::__invoke
 * @see app/Livewire/Clients/Index.php:7
 * @route '/clients'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Clients\Index::__invoke
 * @see app/Livewire/Clients/Index.php:7
 * @route '/clients'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Clients\Index::__invoke
 * @see app/Livewire/Clients/Index.php:7
 * @route '/clients'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index