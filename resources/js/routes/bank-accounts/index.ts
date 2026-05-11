import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Livewire\Accounts\Index::__invoke
 * @see app/Livewire/Accounts/Index.php:7
 * @route '/bank-accounts'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/bank-accounts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Accounts\Index::__invoke
 * @see app/Livewire/Accounts/Index.php:7
 * @route '/bank-accounts'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Accounts\Index::__invoke
 * @see app/Livewire/Accounts/Index.php:7
 * @route '/bank-accounts'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Accounts\Index::__invoke
 * @see app/Livewire/Accounts/Index.php:7
 * @route '/bank-accounts'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const bankAccounts = {
    index: Object.assign(index, index),
}

export default bankAccounts