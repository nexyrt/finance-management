import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Livewire\Loans\Index::__invoke
 * @see app/Livewire/Loans/Index.php:7
 * @route '/loans'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/loans',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Loans\Index::__invoke
 * @see app/Livewire/Loans/Index.php:7
 * @route '/loans'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Loans\Index::__invoke
 * @see app/Livewire/Loans/Index.php:7
 * @route '/loans'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Loans\Index::__invoke
 * @see app/Livewire/Loans/Index.php:7
 * @route '/loans'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const loans = {
    index: Object.assign(index, index),
}

export default loans