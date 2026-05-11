import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Livewire\Permissions\Index::__invoke
 * @see app/Livewire/Permissions/Index.php:7
 * @route '/permissions'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/permissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Permissions\Index::__invoke
 * @see app/Livewire/Permissions/Index.php:7
 * @route '/permissions'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Permissions\Index::__invoke
 * @see app/Livewire/Permissions/Index.php:7
 * @route '/permissions'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Permissions\Index::__invoke
 * @see app/Livewire/Permissions/Index.php:7
 * @route '/permissions'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const permissions = {
    index: Object.assign(index, index),
}

export default permissions