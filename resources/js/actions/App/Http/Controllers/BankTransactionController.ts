import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BankTransactionController::store
 * @see app/Http/Controllers/BankTransactionController.php:13
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
 * @see app/Http/Controllers/BankTransactionController.php:13
 * @route '/bank-transactions'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::store
 * @see app/Http/Controllers/BankTransactionController.php:13
 * @route '/bank-transactions'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BankTransactionController::transfer
 * @see app/Http/Controllers/BankTransactionController.php:149
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
 * @see app/Http/Controllers/BankTransactionController.php:149
 * @route '/bank-transactions/transfer'
 */
transfer.url = (options?: RouteQueryOptions) => {
    return transfer.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::transfer
 * @see app/Http/Controllers/BankTransactionController.php:149
 * @route '/bank-transactions/transfer'
 */
transfer.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: transfer.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BankTransactionController::categorize
 * @see app/Http/Controllers/BankTransactionController.php:132
 * @route '/bank-transactions/categorize'
 */
export const categorize = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: categorize.url(options),
    method: 'patch',
})

categorize.definition = {
    methods: ["patch"],
    url: '/bank-transactions/categorize',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BankTransactionController::categorize
 * @see app/Http/Controllers/BankTransactionController.php:132
 * @route '/bank-transactions/categorize'
 */
categorize.url = (options?: RouteQueryOptions) => {
    return categorize.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::categorize
 * @see app/Http/Controllers/BankTransactionController.php:132
 * @route '/bank-transactions/categorize'
 */
categorize.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: categorize.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\BankTransactionController::bulkDestroy
 * @see app/Http/Controllers/BankTransactionController.php:102
 * @route '/bank-transactions/bulk'
 */
export const bulkDestroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: bulkDestroy.url(options),
    method: 'delete',
})

bulkDestroy.definition = {
    methods: ["delete"],
    url: '/bank-transactions/bulk',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BankTransactionController::bulkDestroy
 * @see app/Http/Controllers/BankTransactionController.php:102
 * @route '/bank-transactions/bulk'
 */
bulkDestroy.url = (options?: RouteQueryOptions) => {
    return bulkDestroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::bulkDestroy
 * @see app/Http/Controllers/BankTransactionController.php:102
 * @route '/bank-transactions/bulk'
 */
bulkDestroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: bulkDestroy.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BankTransactionController::update
 * @see app/Http/Controllers/BankTransactionController.php:57
 * @route '/bank-transactions/{bankTransaction}'
 */
export const update = (args: { bankTransaction: number | { id: number } } | [bankTransaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/bank-transactions/{bankTransaction}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\BankTransactionController::update
 * @see app/Http/Controllers/BankTransactionController.php:57
 * @route '/bank-transactions/{bankTransaction}'
 */
update.url = (args: { bankTransaction: number | { id: number } } | [bankTransaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{bankTransaction}', parsedArgs.bankTransaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::update
 * @see app/Http/Controllers/BankTransactionController.php:57
 * @route '/bank-transactions/{bankTransaction}'
 */
update.put = (args: { bankTransaction: number | { id: number } } | [bankTransaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BankTransactionController::destroy
 * @see app/Http/Controllers/BankTransactionController.php:76
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
 * @see app/Http/Controllers/BankTransactionController.php:76
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
 * @see app/Http/Controllers/BankTransactionController.php:76
 * @route '/bank-transactions/{bankTransaction}'
 */
destroy.delete = (args: { bankTransaction: number | { id: number } } | [bankTransaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const BankTransactionController = { store, transfer, categorize, bulkDestroy, update, destroy }

export default BankTransactionController