import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\CashFlow\Income::__invoke
 * @see app/Livewire/CashFlow/Income.php:7
 * @route '/cash-flow/income'
 */
const Income = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Income.url(options),
    method: 'get',
})

Income.definition = {
    methods: ["get","head"],
    url: '/cash-flow/income',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\CashFlow\Income::__invoke
 * @see app/Livewire/CashFlow/Income.php:7
 * @route '/cash-flow/income'
 */
Income.url = (options?: RouteQueryOptions) => {
    return Income.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\CashFlow\Income::__invoke
 * @see app/Livewire/CashFlow/Income.php:7
 * @route '/cash-flow/income'
 */
Income.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Income.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\CashFlow\Income::__invoke
 * @see app/Livewire/CashFlow/Income.php:7
 * @route '/cash-flow/income'
 */
Income.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Income.url(options),
    method: 'head',
})
export default Income