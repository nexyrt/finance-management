import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ReceivableController::index
 * @see app/Http/Controllers/ReceivableController.php:21
 * @route '/receivables'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/receivables',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReceivableController::index
 * @see app/Http/Controllers/ReceivableController.php:21
 * @route '/receivables'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReceivableController::index
 * @see app/Http/Controllers/ReceivableController.php:21
 * @route '/receivables'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReceivableController::index
 * @see app/Http/Controllers/ReceivableController.php:21
 * @route '/receivables'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReceivableController::store
 * @see app/Http/Controllers/ReceivableController.php:142
 * @route '/receivables'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/receivables',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReceivableController::store
 * @see app/Http/Controllers/ReceivableController.php:142
 * @route '/receivables'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReceivableController::store
 * @see app/Http/Controllers/ReceivableController.php:142
 * @route '/receivables'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ReceivableController::update
 * @see app/Http/Controllers/ReceivableController.php:203
 * @route '/receivables/{receivable}'
 */
export const update = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/receivables/{receivable}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ReceivableController::update
 * @see app/Http/Controllers/ReceivableController.php:203
 * @route '/receivables/{receivable}'
 */
update.url = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { receivable: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { receivable: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    receivable: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        receivable: typeof args.receivable === 'object'
                ? args.receivable.id
                : args.receivable,
                }

    return update.definition.url
            .replace('{receivable}', parsedArgs.receivable.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReceivableController::update
 * @see app/Http/Controllers/ReceivableController.php:203
 * @route '/receivables/{receivable}'
 */
update.put = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ReceivableController::destroy
 * @see app/Http/Controllers/ReceivableController.php:277
 * @route '/receivables/{receivable}'
 */
export const destroy = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/receivables/{receivable}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ReceivableController::destroy
 * @see app/Http/Controllers/ReceivableController.php:277
 * @route '/receivables/{receivable}'
 */
destroy.url = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { receivable: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { receivable: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    receivable: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        receivable: typeof args.receivable === 'object'
                ? args.receivable.id
                : args.receivable,
                }

    return destroy.definition.url
            .replace('{receivable}', parsedArgs.receivable.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReceivableController::destroy
 * @see app/Http/Controllers/ReceivableController.php:277
 * @route '/receivables/{receivable}'
 */
destroy.delete = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ReceivableController::submit
 * @see app/Http/Controllers/ReceivableController.php:291
 * @route '/receivables/{receivable}/submit'
 */
export const submit = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/receivables/{receivable}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReceivableController::submit
 * @see app/Http/Controllers/ReceivableController.php:291
 * @route '/receivables/{receivable}/submit'
 */
submit.url = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { receivable: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { receivable: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    receivable: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        receivable: typeof args.receivable === 'object'
                ? args.receivable.id
                : args.receivable,
                }

    return submit.definition.url
            .replace('{receivable}', parsedArgs.receivable.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReceivableController::submit
 * @see app/Http/Controllers/ReceivableController.php:291
 * @route '/receivables/{receivable}/submit'
 */
submit.post = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ReceivableController::approve
 * @see app/Http/Controllers/ReceivableController.php:300
 * @route '/receivables/{receivable}/approve'
 */
export const approve = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/receivables/{receivable}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReceivableController::approve
 * @see app/Http/Controllers/ReceivableController.php:300
 * @route '/receivables/{receivable}/approve'
 */
approve.url = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { receivable: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { receivable: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    receivable: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        receivable: typeof args.receivable === 'object'
                ? args.receivable.id
                : args.receivable,
                }

    return approve.definition.url
            .replace('{receivable}', parsedArgs.receivable.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReceivableController::approve
 * @see app/Http/Controllers/ReceivableController.php:300
 * @route '/receivables/{receivable}/approve'
 */
approve.post = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ReceivableController::pay
 * @see app/Http/Controllers/ReceivableController.php:348
 * @route '/receivables/{receivable}/pay'
 */
export const pay = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})

pay.definition = {
    methods: ["post"],
    url: '/receivables/{receivable}/pay',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReceivableController::pay
 * @see app/Http/Controllers/ReceivableController.php:348
 * @route '/receivables/{receivable}/pay'
 */
pay.url = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { receivable: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { receivable: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    receivable: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        receivable: typeof args.receivable === 'object'
                ? args.receivable.id
                : args.receivable,
                }

    return pay.definition.url
            .replace('{receivable}', parsedArgs.receivable.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReceivableController::pay
 * @see app/Http/Controllers/ReceivableController.php:348
 * @route '/receivables/{receivable}/pay'
 */
pay.post = (args: { receivable: number | { id: number } } | [receivable: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})
const receivables = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
submit: Object.assign(submit, submit),
approve: Object.assign(approve, approve),
pay: Object.assign(pay, pay),
}

export default receivables