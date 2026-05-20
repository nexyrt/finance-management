import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BankTransactionController::indexTransactions
 * @see app/Http/Controllers/BankTransactionController.php:18
 * @route '/bank-accounts/transactions'
 */
export const indexTransactions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexTransactions.url(options),
    method: 'get',
})

indexTransactions.definition = {
    methods: ["get","head"],
    url: '/bank-accounts/transactions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BankTransactionController::indexTransactions
 * @see app/Http/Controllers/BankTransactionController.php:18
 * @route '/bank-accounts/transactions'
 */
indexTransactions.url = (options?: RouteQueryOptions) => {
    return indexTransactions.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::indexTransactions
 * @see app/Http/Controllers/BankTransactionController.php:18
 * @route '/bank-accounts/transactions'
 */
indexTransactions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexTransactions.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankTransactionController::indexTransactions
 * @see app/Http/Controllers/BankTransactionController.php:18
 * @route '/bank-accounts/transactions'
 */
indexTransactions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexTransactions.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BankTransactionController::indexPayments
 * @see app/Http/Controllers/BankTransactionController.php:77
 * @route '/bank-accounts/payments'
 */
export const indexPayments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexPayments.url(options),
    method: 'get',
})

indexPayments.definition = {
    methods: ["get","head"],
    url: '/bank-accounts/payments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BankTransactionController::indexPayments
 * @see app/Http/Controllers/BankTransactionController.php:77
 * @route '/bank-accounts/payments'
 */
indexPayments.url = (options?: RouteQueryOptions) => {
    return indexPayments.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::indexPayments
 * @see app/Http/Controllers/BankTransactionController.php:77
 * @route '/bank-accounts/payments'
 */
indexPayments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexPayments.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankTransactionController::indexPayments
 * @see app/Http/Controllers/BankTransactionController.php:77
 * @route '/bank-accounts/payments'
 */
indexPayments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexPayments.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BankTransactionController::store
 * @see app/Http/Controllers/BankTransactionController.php:150
 * @route '/bank-transactions'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/bank-transactions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BankTransactionController::store
 * @see app/Http/Controllers/BankTransactionController.php:150
 * @route '/bank-transactions'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::store
 * @see app/Http/Controllers/BankTransactionController.php:150
 * @route '/bank-transactions'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BankTransactionController::destroy
 * @see app/Http/Controllers/BankTransactionController.php:192
 * @route '/bank-transactions/{bankTransaction}'
 */
export const destroy = (args: { bankTransaction: number | { id: number } } | [bankTransaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/bank-transactions/{bankTransaction}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BankTransactionController::destroy
 * @see app/Http/Controllers/BankTransactionController.php:192
 * @route '/bank-transactions/{bankTransaction}'
 */
destroy.url = (args: { bankTransaction: number | { id: number } } | [bankTransaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bankTransaction: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { bankTransaction: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    bankTransaction: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        bankTransaction: typeof args.bankTransaction === 'object'
                ? args.bankTransaction.id
                : args.bankTransaction,
                }

    return destroy.definition.url
            .replace('{bankTransaction}', parsedArgs.bankTransaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::destroy
 * @see app/Http/Controllers/BankTransactionController.php:192
 * @route '/bank-transactions/{bankTransaction}'
 */
destroy.delete = (args: { bankTransaction: number | { id: number } } | [bankTransaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BankTransactionController::bulkDestroy
 * @see app/Http/Controllers/BankTransactionController.php:207
 * @route '/bank-transactions/bulk-delete'
 */
export const bulkDestroy = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkDestroy.url(options),
    method: 'post',
})

bulkDestroy.definition = {
    methods: ["post"],
    url: '/bank-transactions/bulk-delete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BankTransactionController::bulkDestroy
 * @see app/Http/Controllers/BankTransactionController.php:207
 * @route '/bank-transactions/bulk-delete'
 */
bulkDestroy.url = (options?: RouteQueryOptions) => {
    return bulkDestroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::bulkDestroy
 * @see app/Http/Controllers/BankTransactionController.php:207
 * @route '/bank-transactions/bulk-delete'
 */
bulkDestroy.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkDestroy.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BankTransactionController::transfer
 * @see app/Http/Controllers/BankTransactionController.php:242
 * @route '/bank-transactions/transfer'
 */
export const transfer = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: transfer.url(options),
    method: 'post',
})

transfer.definition = {
    methods: ["post"],
    url: '/bank-transactions/transfer',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BankTransactionController::transfer
 * @see app/Http/Controllers/BankTransactionController.php:242
 * @route '/bank-transactions/transfer'
 */
transfer.url = (options?: RouteQueryOptions) => {
    return transfer.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::transfer
 * @see app/Http/Controllers/BankTransactionController.php:242
 * @route '/bank-transactions/transfer'
 */
transfer.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: transfer.url(options),
    method: 'post',
})
const BankTransactionController = { indexTransactions, indexPayments, store, destroy, bulkDestroy, transfer }

export default BankTransactionController