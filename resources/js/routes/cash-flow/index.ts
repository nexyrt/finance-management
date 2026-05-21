import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import exportMethod from './export'
/**
 * @see routes/web.php:273
 * @route '/cash-flow'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/cash-flow',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:273
 * @route '/cash-flow'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:273
 * @route '/cash-flow'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:273
 * @route '/cash-flow'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CashFlowController::income
 * @see app/Http/Controllers/CashFlowController.php:21
 * @route '/cash-flow/income'
 */
export const income = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: income.url(options),
    method: 'get',
})

income.definition = {
    methods: ["get","head"],
    url: '/cash-flow/income',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CashFlowController::income
 * @see app/Http/Controllers/CashFlowController.php:21
 * @route '/cash-flow/income'
 */
income.url = (options?: RouteQueryOptions) => {
    return income.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowController::income
 * @see app/Http/Controllers/CashFlowController.php:21
 * @route '/cash-flow/income'
 */
income.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: income.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CashFlowController::income
 * @see app/Http/Controllers/CashFlowController.php:21
 * @route '/cash-flow/income'
 */
income.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: income.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CashFlowController::expenses
 * @see app/Http/Controllers/CashFlowController.php:175
 * @route '/cash-flow/expenses'
 */
export const expenses = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: expenses.url(options),
    method: 'get',
})

expenses.definition = {
    methods: ["get","head"],
    url: '/cash-flow/expenses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CashFlowController::expenses
 * @see app/Http/Controllers/CashFlowController.php:175
 * @route '/cash-flow/expenses'
 */
expenses.url = (options?: RouteQueryOptions) => {
    return expenses.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowController::expenses
 * @see app/Http/Controllers/CashFlowController.php:175
 * @route '/cash-flow/expenses'
 */
expenses.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: expenses.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CashFlowController::expenses
 * @see app/Http/Controllers/CashFlowController.php:175
 * @route '/cash-flow/expenses'
 */
expenses.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: expenses.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CashFlowController::transfers
 * @see app/Http/Controllers/CashFlowController.php:270
 * @route '/cash-flow/transfers'
 */
export const transfers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transfers.url(options),
    method: 'get',
})

transfers.definition = {
    methods: ["get","head"],
    url: '/cash-flow/transfers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CashFlowController::transfers
 * @see app/Http/Controllers/CashFlowController.php:270
 * @route '/cash-flow/transfers'
 */
transfers.url = (options?: RouteQueryOptions) => {
    return transfers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowController::transfers
 * @see app/Http/Controllers/CashFlowController.php:270
 * @route '/cash-flow/transfers'
 */
transfers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transfers.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CashFlowController::transfers
 * @see app/Http/Controllers/CashFlowController.php:270
 * @route '/cash-flow/transfers'
 */
transfers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: transfers.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CashFlowController::bulkDestroy
 * @see app/Http/Controllers/CashFlowController.php:394
 * @route '/cash-flow/bulk-delete'
 */
export const bulkDestroy = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkDestroy.url(options),
    method: 'post',
})

bulkDestroy.definition = {
    methods: ["post"],
    url: '/cash-flow/bulk-delete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CashFlowController::bulkDestroy
 * @see app/Http/Controllers/CashFlowController.php:394
 * @route '/cash-flow/bulk-delete'
 */
bulkDestroy.url = (options?: RouteQueryOptions) => {
    return bulkDestroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowController::bulkDestroy
 * @see app/Http/Controllers/CashFlowController.php:394
 * @route '/cash-flow/bulk-delete'
 */
bulkDestroy.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkDestroy.url(options),
    method: 'post',
})
const cashFlow = {
    index: Object.assign(index, index),
income: Object.assign(income, income),
expenses: Object.assign(expenses, expenses),
transfers: Object.assign(transfers, transfers),
bulkDestroy: Object.assign(bulkDestroy, bulkDestroy),
export: Object.assign(exportMethod, exportMethod),
}

export default cashFlow