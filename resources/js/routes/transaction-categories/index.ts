import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Livewire\TransactionsCategories\Index::__invoke
 * @see app/Livewire/TransactionsCategories/Index.php:7
 * @route '/transaction-categories'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/transaction-categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\TransactionsCategories\Index::__invoke
 * @see app/Livewire/TransactionsCategories/Index.php:7
 * @route '/transaction-categories'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\TransactionsCategories\Index::__invoke
 * @see app/Livewire/TransactionsCategories/Index.php:7
 * @route '/transaction-categories'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\TransactionsCategories\Index::__invoke
 * @see app/Livewire/TransactionsCategories/Index.php:7
 * @route '/transaction-categories'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const transactionCategories = {
    index: Object.assign(index, index),
}

export default transactionCategories