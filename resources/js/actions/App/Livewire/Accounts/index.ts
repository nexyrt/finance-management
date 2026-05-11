import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Accounts\Index::__invoke
 * @see app/Livewire/Accounts/Index.php:7
 * @route '/bank-accounts'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/bank-accounts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Accounts\Index::__invoke
 * @see app/Livewire/Accounts/Index.php:7
 * @route '/bank-accounts'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Accounts\Index::__invoke
 * @see app/Livewire/Accounts/Index.php:7
 * @route '/bank-accounts'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Accounts\Index::__invoke
 * @see app/Livewire/Accounts/Index.php:7
 * @route '/bank-accounts'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index