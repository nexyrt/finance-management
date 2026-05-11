import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Livewire\Feedbacks\Index::__invoke
 * @see app/Livewire/Feedbacks/Index.php:7
 * @route '/feedbacks'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/feedbacks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Feedbacks\Index::__invoke
 * @see app/Livewire/Feedbacks/Index.php:7
 * @route '/feedbacks'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Feedbacks\Index::__invoke
 * @see app/Livewire/Feedbacks/Index.php:7
 * @route '/feedbacks'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Feedbacks\Index::__invoke
 * @see app/Livewire/Feedbacks/Index.php:7
 * @route '/feedbacks'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const feedbacks = {
    index: Object.assign(index, index),
}

export default feedbacks