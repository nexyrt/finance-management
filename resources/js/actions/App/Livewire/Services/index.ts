import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Services\Index::__invoke
 * @see app/Livewire/Services/Index.php:7
 * @route '/services'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/services',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Services\Index::__invoke
 * @see app/Livewire/Services/Index.php:7
 * @route '/services'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Services\Index::__invoke
 * @see app/Livewire/Services/Index.php:7
 * @route '/services'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Services\Index::__invoke
 * @see app/Livewire/Services/Index.php:7
 * @route '/services'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index