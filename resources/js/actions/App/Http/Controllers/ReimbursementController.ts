import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ReimbursementController::index
 * @see app/Http/Controllers/ReimbursementController.php:18
 * @route '/reimbursements'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/reimbursements',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReimbursementController::index
 * @see app/Http/Controllers/ReimbursementController.php:18
 * @route '/reimbursements'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReimbursementController::index
 * @see app/Http/Controllers/ReimbursementController.php:18
 * @route '/reimbursements'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReimbursementController::index
 * @see app/Http/Controllers/ReimbursementController.php:18
 * @route '/reimbursements'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReimbursementController::create
 * @see app/Http/Controllers/ReimbursementController.php:146
 * @route '/reimbursements/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/reimbursements/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReimbursementController::create
 * @see app/Http/Controllers/ReimbursementController.php:146
 * @route '/reimbursements/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReimbursementController::create
 * @see app/Http/Controllers/ReimbursementController.php:146
 * @route '/reimbursements/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReimbursementController::create
 * @see app/Http/Controllers/ReimbursementController.php:146
 * @route '/reimbursements/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReimbursementController::store
 * @see app/Http/Controllers/ReimbursementController.php:151
 * @route '/reimbursements'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/reimbursements',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReimbursementController::store
 * @see app/Http/Controllers/ReimbursementController.php:151
 * @route '/reimbursements'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReimbursementController::store
 * @see app/Http/Controllers/ReimbursementController.php:151
 * @route '/reimbursements'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ReimbursementController::edit
 * @see app/Http/Controllers/ReimbursementController.php:197
 * @route '/reimbursements/{reimbursement}/edit'
 */
export const edit = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/reimbursements/{reimbursement}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReimbursementController::edit
 * @see app/Http/Controllers/ReimbursementController.php:197
 * @route '/reimbursements/{reimbursement}/edit'
 */
edit.url = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reimbursement: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reimbursement: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reimbursement: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reimbursement: typeof args.reimbursement === 'object'
                ? args.reimbursement.id
                : args.reimbursement,
                }

    return edit.definition.url
            .replace('{reimbursement}', parsedArgs.reimbursement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReimbursementController::edit
 * @see app/Http/Controllers/ReimbursementController.php:197
 * @route '/reimbursements/{reimbursement}/edit'
 */
edit.get = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReimbursementController::edit
 * @see app/Http/Controllers/ReimbursementController.php:197
 * @route '/reimbursements/{reimbursement}/edit'
 */
edit.head = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReimbursementController::update
 * @see app/Http/Controllers/ReimbursementController.php:223
 * @route '/reimbursements/{reimbursement}'
 */
export const update = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/reimbursements/{reimbursement}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ReimbursementController::update
 * @see app/Http/Controllers/ReimbursementController.php:223
 * @route '/reimbursements/{reimbursement}'
 */
update.url = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reimbursement: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reimbursement: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reimbursement: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reimbursement: typeof args.reimbursement === 'object'
                ? args.reimbursement.id
                : args.reimbursement,
                }

    return update.definition.url
            .replace('{reimbursement}', parsedArgs.reimbursement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReimbursementController::update
 * @see app/Http/Controllers/ReimbursementController.php:223
 * @route '/reimbursements/{reimbursement}'
 */
update.put = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ReimbursementController::destroy
 * @see app/Http/Controllers/ReimbursementController.php:284
 * @route '/reimbursements/{reimbursement}'
 */
export const destroy = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/reimbursements/{reimbursement}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ReimbursementController::destroy
 * @see app/Http/Controllers/ReimbursementController.php:284
 * @route '/reimbursements/{reimbursement}'
 */
destroy.url = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reimbursement: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reimbursement: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reimbursement: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reimbursement: typeof args.reimbursement === 'object'
                ? args.reimbursement.id
                : args.reimbursement,
                }

    return destroy.definition.url
            .replace('{reimbursement}', parsedArgs.reimbursement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReimbursementController::destroy
 * @see app/Http/Controllers/ReimbursementController.php:284
 * @route '/reimbursements/{reimbursement}'
 */
destroy.delete = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ReimbursementController::submit
 * @see app/Http/Controllers/ReimbursementController.php:295
 * @route '/reimbursements/{reimbursement}/submit'
 */
export const submit = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/reimbursements/{reimbursement}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReimbursementController::submit
 * @see app/Http/Controllers/ReimbursementController.php:295
 * @route '/reimbursements/{reimbursement}/submit'
 */
submit.url = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reimbursement: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reimbursement: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reimbursement: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reimbursement: typeof args.reimbursement === 'object'
                ? args.reimbursement.id
                : args.reimbursement,
                }

    return submit.definition.url
            .replace('{reimbursement}', parsedArgs.reimbursement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReimbursementController::submit
 * @see app/Http/Controllers/ReimbursementController.php:295
 * @route '/reimbursements/{reimbursement}/submit'
 */
submit.post = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ReimbursementController::review
 * @see app/Http/Controllers/ReimbursementController.php:310
 * @route '/reimbursements/{reimbursement}/review'
 */
export const review = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: review.url(args, options),
    method: 'post',
})

review.definition = {
    methods: ["post"],
    url: '/reimbursements/{reimbursement}/review',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReimbursementController::review
 * @see app/Http/Controllers/ReimbursementController.php:310
 * @route '/reimbursements/{reimbursement}/review'
 */
review.url = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reimbursement: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reimbursement: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reimbursement: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reimbursement: typeof args.reimbursement === 'object'
                ? args.reimbursement.id
                : args.reimbursement,
                }

    return review.definition.url
            .replace('{reimbursement}', parsedArgs.reimbursement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReimbursementController::review
 * @see app/Http/Controllers/ReimbursementController.php:310
 * @route '/reimbursements/{reimbursement}/review'
 */
review.post = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: review.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ReimbursementController::pay
 * @see app/Http/Controllers/ReimbursementController.php:336
 * @route '/reimbursements/{reimbursement}/pay'
 */
export const pay = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})

pay.definition = {
    methods: ["post"],
    url: '/reimbursements/{reimbursement}/pay',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReimbursementController::pay
 * @see app/Http/Controllers/ReimbursementController.php:336
 * @route '/reimbursements/{reimbursement}/pay'
 */
pay.url = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reimbursement: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reimbursement: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reimbursement: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reimbursement: typeof args.reimbursement === 'object'
                ? args.reimbursement.id
                : args.reimbursement,
                }

    return pay.definition.url
            .replace('{reimbursement}', parsedArgs.reimbursement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReimbursementController::pay
 * @see app/Http/Controllers/ReimbursementController.php:336
 * @route '/reimbursements/{reimbursement}/pay'
 */
pay.post = (args: { reimbursement: number | { id: number } } | [reimbursement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})
const ReimbursementController = { index, create, store, edit, update, destroy, submit, review, pay }

export default ReimbursementController