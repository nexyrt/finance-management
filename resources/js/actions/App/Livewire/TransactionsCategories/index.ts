import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\TransactionsCategories\Index::__invoke
 * @see app/Livewire/TransactionsCategories/Index.php:7
 * @route '/transaction-categories'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/transaction-categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\TransactionsCategories\Index::__invoke
 * @see app/Livewire/TransactionsCategories/Index.php:7
 * @route '/transaction-categories'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\TransactionsCategories\Index::__invoke
 * @see app/Livewire/TransactionsCategories/Index.php:7
 * @route '/transaction-categories'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\TransactionsCategories\Index::__invoke
 * @see app/Livewire/TransactionsCategories/Index.php:7
 * @route '/transaction-categories'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index