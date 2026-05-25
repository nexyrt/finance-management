import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:16
 * @route '/transaction-categories'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/transaction-categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:16
 * @route '/transaction-categories'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:16
 * @route '/transaction-categories'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:16
 * @route '/transaction-categories'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransactionCategoryController::store
 * @see app/Http/Controllers/TransactionCategoryController.php:79
 * @route '/transaction-categories'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/transaction-categories',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TransactionCategoryController::store
 * @see app/Http/Controllers/TransactionCategoryController.php:79
 * @route '/transaction-categories'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionCategoryController::store
 * @see app/Http/Controllers/TransactionCategoryController.php:79
 * @route '/transaction-categories'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionCategoryController::update
 * @see app/Http/Controllers/TransactionCategoryController.php:95
 * @route '/transaction-categories/{transactionCategory}'
 */
export const update = (args: { transactionCategory: number | { id: number } } | [transactionCategory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/transaction-categories/{transactionCategory}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\TransactionCategoryController::update
 * @see app/Http/Controllers/TransactionCategoryController.php:95
 * @route '/transaction-categories/{transactionCategory}'
 */
update.url = (args: { transactionCategory: number | { id: number } } | [transactionCategory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transactionCategory: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { transactionCategory: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    transactionCategory: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        transactionCategory: typeof args.transactionCategory === 'object'
                ? args.transactionCategory.id
                : args.transactionCategory,
                }

    return update.definition.url
            .replace('{transactionCategory}', parsedArgs.transactionCategory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionCategoryController::update
 * @see app/Http/Controllers/TransactionCategoryController.php:95
 * @route '/transaction-categories/{transactionCategory}'
 */
update.put = (args: { transactionCategory: number | { id: number } } | [transactionCategory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\TransactionCategoryController::destroy
 * @see app/Http/Controllers/TransactionCategoryController.php:102
 * @route '/transaction-categories/{transactionCategory}'
 */
export const destroy = (args: { transactionCategory: number | { id: number } } | [transactionCategory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/transaction-categories/{transactionCategory}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\TransactionCategoryController::destroy
 * @see app/Http/Controllers/TransactionCategoryController.php:102
 * @route '/transaction-categories/{transactionCategory}'
 */
destroy.url = (args: { transactionCategory: number | { id: number } } | [transactionCategory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transactionCategory: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { transactionCategory: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    transactionCategory: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        transactionCategory: typeof args.transactionCategory === 'object'
                ? args.transactionCategory.id
                : args.transactionCategory,
                }

    return destroy.definition.url
            .replace('{transactionCategory}', parsedArgs.transactionCategory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionCategoryController::destroy
 * @see app/Http/Controllers/TransactionCategoryController.php:102
 * @route '/transaction-categories/{transactionCategory}'
 */
destroy.delete = (args: { transactionCategory: number | { id: number } } | [transactionCategory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const TransactionCategoryController = { index, store, update, destroy }

export default TransactionCategoryController