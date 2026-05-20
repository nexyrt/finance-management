import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\FundRequestController::index
 * @see app/Http/Controllers/FundRequestController.php:20
 * @route '/fund-requests'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/fund-requests',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\FundRequestController::index
 * @see app/Http/Controllers/FundRequestController.php:20
 * @route '/fund-requests'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\FundRequestController::index
 * @see app/Http/Controllers/FundRequestController.php:20
 * @route '/fund-requests'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\FundRequestController::index
 * @see app/Http/Controllers/FundRequestController.php:20
 * @route '/fund-requests'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\FundRequestController::create
 * @see app/Http/Controllers/FundRequestController.php:150
 * @route '/fund-requests/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/fund-requests/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\FundRequestController::create
 * @see app/Http/Controllers/FundRequestController.php:150
 * @route '/fund-requests/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\FundRequestController::create
 * @see app/Http/Controllers/FundRequestController.php:150
 * @route '/fund-requests/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\FundRequestController::create
 * @see app/Http/Controllers/FundRequestController.php:150
 * @route '/fund-requests/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\FundRequestController::store
 * @see app/Http/Controllers/FundRequestController.php:166
 * @route '/fund-requests'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/fund-requests',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\FundRequestController::store
 * @see app/Http/Controllers/FundRequestController.php:166
 * @route '/fund-requests'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\FundRequestController::store
 * @see app/Http/Controllers/FundRequestController.php:166
 * @route '/fund-requests'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\FundRequestController::edit
 * @see app/Http/Controllers/FundRequestController.php:230
 * @route '/fund-requests/{fundRequest}/edit'
 */
export const edit = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/fund-requests/{fundRequest}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\FundRequestController::edit
 * @see app/Http/Controllers/FundRequestController.php:230
 * @route '/fund-requests/{fundRequest}/edit'
 */
edit.url = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { fundRequest: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { fundRequest: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    fundRequest: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        fundRequest: typeof args.fundRequest === 'object'
                ? args.fundRequest.id
                : args.fundRequest,
                }

    return edit.definition.url
            .replace('{fundRequest}', parsedArgs.fundRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FundRequestController::edit
 * @see app/Http/Controllers/FundRequestController.php:230
 * @route '/fund-requests/{fundRequest}/edit'
 */
edit.get = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\FundRequestController::edit
 * @see app/Http/Controllers/FundRequestController.php:230
 * @route '/fund-requests/{fundRequest}/edit'
 */
edit.head = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\FundRequestController::update
 * @see app/Http/Controllers/FundRequestController.php:274
 * @route '/fund-requests/{fundRequest}'
 */
export const update = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/fund-requests/{fundRequest}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\FundRequestController::update
 * @see app/Http/Controllers/FundRequestController.php:274
 * @route '/fund-requests/{fundRequest}'
 */
update.url = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { fundRequest: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { fundRequest: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    fundRequest: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        fundRequest: typeof args.fundRequest === 'object'
                ? args.fundRequest.id
                : args.fundRequest,
                }

    return update.definition.url
            .replace('{fundRequest}', parsedArgs.fundRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FundRequestController::update
 * @see app/Http/Controllers/FundRequestController.php:274
 * @route '/fund-requests/{fundRequest}'
 */
update.put = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\FundRequestController::destroy
 * @see app/Http/Controllers/FundRequestController.php:353
 * @route '/fund-requests/{fundRequest}'
 */
export const destroy = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/fund-requests/{fundRequest}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\FundRequestController::destroy
 * @see app/Http/Controllers/FundRequestController.php:353
 * @route '/fund-requests/{fundRequest}'
 */
destroy.url = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { fundRequest: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { fundRequest: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    fundRequest: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        fundRequest: typeof args.fundRequest === 'object'
                ? args.fundRequest.id
                : args.fundRequest,
                }

    return destroy.definition.url
            .replace('{fundRequest}', parsedArgs.fundRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FundRequestController::destroy
 * @see app/Http/Controllers/FundRequestController.php:353
 * @route '/fund-requests/{fundRequest}'
 */
destroy.delete = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\FundRequestController::submit
 * @see app/Http/Controllers/FundRequestController.php:364
 * @route '/fund-requests/{fundRequest}/submit'
 */
export const submit = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/fund-requests/{fundRequest}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\FundRequestController::submit
 * @see app/Http/Controllers/FundRequestController.php:364
 * @route '/fund-requests/{fundRequest}/submit'
 */
submit.url = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { fundRequest: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { fundRequest: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    fundRequest: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        fundRequest: typeof args.fundRequest === 'object'
                ? args.fundRequest.id
                : args.fundRequest,
                }

    return submit.definition.url
            .replace('{fundRequest}', parsedArgs.fundRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FundRequestController::submit
 * @see app/Http/Controllers/FundRequestController.php:364
 * @route '/fund-requests/{fundRequest}/submit'
 */
submit.post = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\FundRequestController::review
 * @see app/Http/Controllers/FundRequestController.php:379
 * @route '/fund-requests/{fundRequest}/review'
 */
export const review = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: review.url(args, options),
    method: 'post',
})

review.definition = {
    methods: ["post"],
    url: '/fund-requests/{fundRequest}/review',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\FundRequestController::review
 * @see app/Http/Controllers/FundRequestController.php:379
 * @route '/fund-requests/{fundRequest}/review'
 */
review.url = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { fundRequest: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { fundRequest: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    fundRequest: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        fundRequest: typeof args.fundRequest === 'object'
                ? args.fundRequest.id
                : args.fundRequest,
                }

    return review.definition.url
            .replace('{fundRequest}', parsedArgs.fundRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FundRequestController::review
 * @see app/Http/Controllers/FundRequestController.php:379
 * @route '/fund-requests/{fundRequest}/review'
 */
review.post = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: review.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\FundRequestController::disburse
 * @see app/Http/Controllers/FundRequestController.php:403
 * @route '/fund-requests/{fundRequest}/disburse'
 */
export const disburse = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: disburse.url(args, options),
    method: 'post',
})

disburse.definition = {
    methods: ["post"],
    url: '/fund-requests/{fundRequest}/disburse',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\FundRequestController::disburse
 * @see app/Http/Controllers/FundRequestController.php:403
 * @route '/fund-requests/{fundRequest}/disburse'
 */
disburse.url = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { fundRequest: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { fundRequest: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    fundRequest: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        fundRequest: typeof args.fundRequest === 'object'
                ? args.fundRequest.id
                : args.fundRequest,
                }

    return disburse.definition.url
            .replace('{fundRequest}', parsedArgs.fundRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FundRequestController::disburse
 * @see app/Http/Controllers/FundRequestController.php:403
 * @route '/fund-requests/{fundRequest}/disburse'
 */
disburse.post = (args: { fundRequest: number | { id: number } } | [fundRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: disburse.url(args, options),
    method: 'post',
})
const FundRequestController = { index, create, store, edit, update, destroy, submit, review, disburse }

export default FundRequestController