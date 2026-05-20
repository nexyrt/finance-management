import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\LoanController::index
 * @see app/Http/Controllers/LoanController.php:19
 * @route '/loans'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/loans',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LoanController::index
 * @see app/Http/Controllers/LoanController.php:19
 * @route '/loans'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::index
 * @see app/Http/Controllers/LoanController.php:19
 * @route '/loans'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LoanController::index
 * @see app/Http/Controllers/LoanController.php:19
 * @route '/loans'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LoanController::store
 * @see app/Http/Controllers/LoanController.php:105
 * @route '/loans'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/loans',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LoanController::store
 * @see app/Http/Controllers/LoanController.php:105
 * @route '/loans'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::store
 * @see app/Http/Controllers/LoanController.php:105
 * @route '/loans'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LoanController::update
 * @see app/Http/Controllers/LoanController.php:159
 * @route '/loans/{loan}'
 */
export const update = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/loans/{loan}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\LoanController::update
 * @see app/Http/Controllers/LoanController.php:159
 * @route '/loans/{loan}'
 */
update.url = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loan: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { loan: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    loan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        loan: typeof args.loan === 'object'
                ? args.loan.id
                : args.loan,
                }

    return update.definition.url
            .replace('{loan}', parsedArgs.loan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::update
 * @see app/Http/Controllers/LoanController.php:159
 * @route '/loans/{loan}'
 */
update.put = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\LoanController::destroy
 * @see app/Http/Controllers/LoanController.php:207
 * @route '/loans/{loan}'
 */
export const destroy = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/loans/{loan}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LoanController::destroy
 * @see app/Http/Controllers/LoanController.php:207
 * @route '/loans/{loan}'
 */
destroy.url = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loan: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { loan: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    loan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        loan: typeof args.loan === 'object'
                ? args.loan.id
                : args.loan,
                }

    return destroy.definition.url
            .replace('{loan}', parsedArgs.loan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::destroy
 * @see app/Http/Controllers/LoanController.php:207
 * @route '/loans/{loan}'
 */
destroy.delete = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\LoanController::pay
 * @see app/Http/Controllers/LoanController.php:220
 * @route '/loans/{loan}/pay'
 */
export const pay = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})

pay.definition = {
    methods: ["post"],
    url: '/loans/{loan}/pay',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LoanController::pay
 * @see app/Http/Controllers/LoanController.php:220
 * @route '/loans/{loan}/pay'
 */
pay.url = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loan: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { loan: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    loan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        loan: typeof args.loan === 'object'
                ? args.loan.id
                : args.loan,
                }

    return pay.definition.url
            .replace('{loan}', parsedArgs.loan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::pay
 * @see app/Http/Controllers/LoanController.php:220
 * @route '/loans/{loan}/pay'
 */
pay.post = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})
const loans = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
pay: Object.assign(pay, pay),
}

export default loans