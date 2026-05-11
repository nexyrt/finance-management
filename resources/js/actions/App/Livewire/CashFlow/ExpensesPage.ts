import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\CashFlow\ExpensesPage::__invoke
 * @see app/Livewire/CashFlow/ExpensesPage.php:7
 * @route '/cash-flow/expenses'
 */
const ExpensesPage = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ExpensesPage.url(options),
    method: 'get',
})

ExpensesPage.definition = {
    methods: ["get","head"],
    url: '/cash-flow/expenses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\CashFlow\ExpensesPage::__invoke
 * @see app/Livewire/CashFlow/ExpensesPage.php:7
 * @route '/cash-flow/expenses'
 */
ExpensesPage.url = (options?: RouteQueryOptions) => {
    return ExpensesPage.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\CashFlow\ExpensesPage::__invoke
 * @see app/Livewire/CashFlow/ExpensesPage.php:7
 * @route '/cash-flow/expenses'
 */
ExpensesPage.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ExpensesPage.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\CashFlow\ExpensesPage::__invoke
 * @see app/Livewire/CashFlow/ExpensesPage.php:7
 * @route '/cash-flow/expenses'
 */
ExpensesPage.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ExpensesPage.url(options),
    method: 'head',
})
export default ExpensesPage