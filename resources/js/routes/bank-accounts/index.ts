import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\BankAccountController::index
 * @see app/Http/Controllers/BankAccountController.php:16
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
 * @see app/Http/Controllers/BankAccountController.php:16
 * @route '/bank-accounts'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::index
 * @see app/Http/Controllers/BankAccountController.php:16
 * @route '/bank-accounts'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankAccountController::index
 * @see app/Http/Controllers/BankAccountController.php:16
 * @route '/bank-accounts'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BankAccountController::store
 * @see app/Http/Controllers/BankAccountController.php:96
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
 * @see app/Http/Controllers/BankAccountController.php:96
 * @route '/bank-accounts'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::store
 * @see app/Http/Controllers/BankAccountController.php:96
 * @route '/bank-accounts'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BankAccountController::update
 * @see app/Http/Controllers/BankAccountController.php:131
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
 * @see app/Http/Controllers/BankAccountController.php:131
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
 * @see app/Http/Controllers/BankAccountController.php:131
 * @route '/bank-accounts/{bankAccount}'
 */
update.put = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BankAccountController::destroy
 * @see app/Http/Controllers/BankAccountController.php:203
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
 * @see app/Http/Controllers/BankAccountController.php:203
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
 * @see app/Http/Controllers/BankAccountController.php:203
 * @route '/bank-accounts/{bankAccount}'
 */
destroy.delete = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BankAccountController::chartData
 * @see app/Http/Controllers/BankAccountController.php:218
 * @route '/bank-accounts/{bankAccount}/chart-data'
 */
export const chartData = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: chartData.url(args, options),
    method: 'get',
})

chartData.definition = {
    methods: ["get","head"],
    url: '/bank-accounts/{bankAccount}/chart-data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BankAccountController::chartData
 * @see app/Http/Controllers/BankAccountController.php:218
 * @route '/bank-accounts/{bankAccount}/chart-data'
 */
chartData.url = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return chartData.definition.url
            .replace('{bankAccount}', parsedArgs.bankAccount.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::chartData
 * @see app/Http/Controllers/BankAccountController.php:218
 * @route '/bank-accounts/{bankAccount}/chart-data'
 */
chartData.get = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: chartData.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankAccountController::chartData
 * @see app/Http/Controllers/BankAccountController.php:218
 * @route '/bank-accounts/{bankAccount}/chart-data'
 */
chartData.head = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: chartData.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BankAccountController::activity
 * @see app/Http/Controllers/BankAccountController.php:497
 * @route '/bank-accounts/{bankAccount}/activity'
 */
export const activity = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activity.url(args, options),
    method: 'get',
})

activity.definition = {
    methods: ["get","head"],
    url: '/bank-accounts/{bankAccount}/activity',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BankAccountController::activity
 * @see app/Http/Controllers/BankAccountController.php:497
 * @route '/bank-accounts/{bankAccount}/activity'
 */
activity.url = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return activity.definition.url
            .replace('{bankAccount}', parsedArgs.bankAccount.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::activity
 * @see app/Http/Controllers/BankAccountController.php:497
 * @route '/bank-accounts/{bankAccount}/activity'
 */
activity.get = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activity.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankAccountController::activity
 * @see app/Http/Controllers/BankAccountController.php:497
 * @route '/bank-accounts/{bankAccount}/activity'
 */
activity.head = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: activity.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BankAccountController::monthlyStats
 * @see app/Http/Controllers/BankAccountController.php:387
 * @route '/bank-accounts/{bankAccount}/monthly-stats'
 */
export const monthlyStats = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: monthlyStats.url(args, options),
    method: 'get',
})

monthlyStats.definition = {
    methods: ["get","head"],
    url: '/bank-accounts/{bankAccount}/monthly-stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BankAccountController::monthlyStats
 * @see app/Http/Controllers/BankAccountController.php:387
 * @route '/bank-accounts/{bankAccount}/monthly-stats'
 */
monthlyStats.url = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return monthlyStats.definition.url
            .replace('{bankAccount}', parsedArgs.bankAccount.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::monthlyStats
 * @see app/Http/Controllers/BankAccountController.php:387
 * @route '/bank-accounts/{bankAccount}/monthly-stats'
 */
monthlyStats.get = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: monthlyStats.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankAccountController::monthlyStats
 * @see app/Http/Controllers/BankAccountController.php:387
 * @route '/bank-accounts/{bankAccount}/monthly-stats'
 */
monthlyStats.head = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: monthlyStats.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BankAccountController::transactions
 * @see app/Http/Controllers/BankAccountController.php:265
 * @route '/bank-accounts/{bankAccount}/transactions'
 */
export const transactions = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transactions.url(args, options),
    method: 'get',
})

transactions.definition = {
    methods: ["get","head"],
    url: '/bank-accounts/{bankAccount}/transactions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BankAccountController::transactions
 * @see app/Http/Controllers/BankAccountController.php:265
 * @route '/bank-accounts/{bankAccount}/transactions'
 */
transactions.url = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return transactions.definition.url
            .replace('{bankAccount}', parsedArgs.bankAccount.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::transactions
 * @see app/Http/Controllers/BankAccountController.php:265
 * @route '/bank-accounts/{bankAccount}/transactions'
 */
transactions.get = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transactions.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankAccountController::transactions
 * @see app/Http/Controllers/BankAccountController.php:265
 * @route '/bank-accounts/{bankAccount}/transactions'
 */
transactions.head = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: transactions.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BankAccountController::payments
 * @see app/Http/Controllers/BankAccountController.php:326
 * @route '/bank-accounts/{bankAccount}/payments'
 */
export const payments = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: payments.url(args, options),
    method: 'get',
})

payments.definition = {
    methods: ["get","head"],
    url: '/bank-accounts/{bankAccount}/payments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BankAccountController::payments
 * @see app/Http/Controllers/BankAccountController.php:326
 * @route '/bank-accounts/{bankAccount}/payments'
 */
payments.url = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return payments.definition.url
            .replace('{bankAccount}', parsedArgs.bankAccount.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BankAccountController::payments
 * @see app/Http/Controllers/BankAccountController.php:326
 * @route '/bank-accounts/{bankAccount}/payments'
 */
payments.get = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: payments.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BankAccountController::payments
 * @see app/Http/Controllers/BankAccountController.php:326
 * @route '/bank-accounts/{bankAccount}/payments'
 */
payments.head = (args: { bankAccount: number | { id: number } } | [bankAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: payments.url(args, options),
    method: 'head',
})
const bankAccounts = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
chartData: Object.assign(chartData, chartData),
activity: Object.assign(activity, activity),
monthlyStats: Object.assign(monthlyStats, monthlyStats),
transactions: Object.assign(transactions, transactions),
payments: Object.assign(payments, payments),
}

export default bankAccounts