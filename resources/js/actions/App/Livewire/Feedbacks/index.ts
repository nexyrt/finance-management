import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Feedbacks\Index::__invoke
 * @see app/Livewire/Feedbacks/Index.php:7
 * @route '/feedbacks'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/feedbacks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Feedbacks\Index::__invoke
 * @see app/Livewire/Feedbacks/Index.php:7
 * @route '/feedbacks'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Feedbacks\Index::__invoke
 * @see app/Livewire/Feedbacks/Index.php:7
 * @route '/feedbacks'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Feedbacks\Index::__invoke
 * @see app/Livewire/Feedbacks/Index.php:7
 * @route '/feedbacks'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index