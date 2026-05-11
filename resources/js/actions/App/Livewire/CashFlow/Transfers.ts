import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\CashFlow\Transfers::__invoke
 * @see app/Livewire/CashFlow/Transfers.php:7
 * @route '/cash-flow/transfers'
 */
const Transfers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Transfers.url(options),
    method: 'get',
})

Transfers.definition = {
    methods: ["get","head"],
    url: '/cash-flow/transfers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\CashFlow\Transfers::__invoke
 * @see app/Livewire/CashFlow/Transfers.php:7
 * @route '/cash-flow/transfers'
 */
Transfers.url = (options?: RouteQueryOptions) => {
    return Transfers.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\CashFlow\Transfers::__invoke
 * @see app/Livewire/CashFlow/Transfers.php:7
 * @route '/cash-flow/transfers'
 */
Transfers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Transfers.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\CashFlow\Transfers::__invoke
 * @see app/Livewire/CashFlow/Transfers.php:7
 * @route '/cash-flow/transfers'
 */
Transfers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Transfers.url(options),
    method: 'head',
})
export default Transfers