import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Permissions\Index::__invoke
 * @see app/Livewire/Permissions/Index.php:7
 * @route '/permissions'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/permissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Permissions\Index::__invoke
 * @see app/Livewire/Permissions/Index.php:7
 * @route '/permissions'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Permissions\Index::__invoke
 * @see app/Livewire/Permissions/Index.php:7
 * @route '/permissions'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Permissions\Index::__invoke
 * @see app/Livewire/Permissions/Index.php:7
 * @route '/permissions'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index