import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Livewire\Receivables\Index::__invoke
 * @see app/Livewire/Receivables/Index.php:7
 * @route '/receivables'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/receivables',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Receivables\Index::__invoke
 * @see app/Livewire/Receivables/Index.php:7
 * @route '/receivables'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Receivables\Index::__invoke
 * @see app/Livewire/Receivables/Index.php:7
 * @route '/receivables'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Receivables\Index::__invoke
 * @see app/Livewire/Receivables/Index.php:7
 * @route '/receivables'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const receivables = {
    index: Object.assign(index, index),
}

export default receivables