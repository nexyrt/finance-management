import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\BankAccountController::index
 * @see app/Http/Controllers/BankAccountController.php:18
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
* @see \App\Http\Controllers\BankAccountController::index
 * @see app/Http/Controllers/BankAccountController.php:18
 * @route '/bank-accounts'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::index
 * @see app/Http/Controllers/BankAccountController.php:18
 * @route '/bank-accounts'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankAccountController::index
 * @see app/Http/Controllers/BankAccountController.php:18
 * @route '/bank-accounts'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BankAccountController::store
 * @see app/Http/Controllers/BankAccountController.php:84
 * @route '/bank-accounts'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/bank-accounts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BankAccountController::store
 * @see app/Http/Controllers/BankAccountController.php:84
 * @route '/bank-accounts'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::store
 * @see app/Http/Controllers/BankAccountController.php:84
 * @route '/bank-accounts'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BankAccountController::update
 * @see app/Http/Controllers/BankAccountController.php:101
 * @route '/bank-accounts/{bankAccount}'
 */
export const update = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/bank-accounts/{bankAccount}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\BankAccountController::update
 * @see app/Http/Controllers/BankAccountController.php:101
 * @route '/bank-accounts/{bankAccount}'
 */
update.url = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bankAccount: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { bankAccount: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    bankAccount: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        bankAccount: typeof args.bankAccount === 'object'
                ? args.bankAccount.id
                : args.bankAccount,
                }

    return update.definition.url
            .replace('{bankAccount}', parsedArgs.bankAccount.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::update
 * @see app/Http/Controllers/BankAccountController.php:101
 * @route '/bank-accounts/{bankAccount}'
 */
update.put = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BankAccountController::destroy
 * @see app/Http/Controllers/BankAccountController.php:116
 * @route '/bank-accounts/{bankAccount}'
 */
export const destroy = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/bank-accounts/{bankAccount}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BankAccountController::destroy
 * @see app/Http/Controllers/BankAccountController.php:116
 * @route '/bank-accounts/{bankAccount}'
 */
destroy.url = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bankAccount: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { bankAccount: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    bankAccount: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        bankAccount: typeof args.bankAccount === 'object'
                ? args.bankAccount.id
                : args.bankAccount,
                }

    return destroy.definition.url
            .replace('{bankAccount}', parsedArgs.bankAccount.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::destroy
 * @see app/Http/Controllers/BankAccountController.php:116
 * @route '/bank-accounts/{bankAccount}'
 */
destroy.delete = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BankTransactionController::transactions
 * @see app/Http/Controllers/BankTransactionController.php:22
 * @route '/bank-accounts/transactions'
 */
export const transactions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transactions.url(options),
    method: 'get',
})

transactions.definition = {
    methods: ["get","head"],
    url: '/bank-accounts/transactions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BankTransactionController::transactions
 * @see app/Http/Controllers/BankTransactionController.php:22
 * @route '/bank-accounts/transactions'
 */
transactions.url = (options?: RouteQueryOptions) => {
    return transactions.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::transactions
 * @see app/Http/Controllers/BankTransactionController.php:22
 * @route '/bank-accounts/transactions'
 */
transactions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transactions.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankTransactionController::transactions
 * @see app/Http/Controllers/BankTransactionController.php:22
 * @route '/bank-accounts/transactions'
 */
transactions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: transactions.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BankTransactionController::payments
 * @see app/Http/Controllers/BankTransactionController.php:81
 * @route '/bank-accounts/payments'
 */
export const payments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: payments.url(options),
    method: 'get',
})

payments.definition = {
    methods: ["get","head"],
    url: '/bank-accounts/payments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BankTransactionController::payments
 * @see app/Http/Controllers/BankTransactionController.php:81
 * @route '/bank-accounts/payments'
 */
payments.url = (options?: RouteQueryOptions) => {
    return payments.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankTransactionController::payments
 * @see app/Http/Controllers/BankTransactionController.php:81
 * @route '/bank-accounts/payments'
 */
payments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: payments.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankTransactionController::payments
 * @see app/Http/Controllers/BankTransactionController.php:81
 * @route '/bank-accounts/payments'
 */
payments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: payments.url(options),
    method: 'head',
})
const bankAccounts = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
transactions: Object.assign(transactions, transactions),
payments: Object.assign(payments, payments),
}

export default bankAccounts