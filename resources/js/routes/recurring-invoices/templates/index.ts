import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\RecurringInvoiceController::create
 * @see app/Http/Controllers/RecurringInvoiceController.php:117
 * @route '/recurring-invoices/templates/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/templates/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::create
 * @see app/Http/Controllers/RecurringInvoiceController.php:117
 * @route '/recurring-invoices/templates/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::create
 * @see app/Http/Controllers/RecurringInvoiceController.php:117
 * @route '/recurring-invoices/templates/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RecurringInvoiceController::create
 * @see app/Http/Controllers/RecurringInvoiceController.php:117
 * @route '/recurring-invoices/templates/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::edit
 * @see app/Http/Controllers/RecurringInvoiceController.php:125
 * @route '/recurring-invoices/templates/{template}/edit'
 */
export const edit = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/templates/{template}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::edit
 * @see app/Http/Controllers/RecurringInvoiceController.php:125
 * @route '/recurring-invoices/templates/{template}/edit'
 */
edit.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { template: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    template: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        template: typeof args.template === 'object'
                ? args.template.id
                : args.template,
                }

    return edit.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::edit
 * @see app/Http/Controllers/RecurringInvoiceController.php:125
 * @route '/recurring-invoices/templates/{template}/edit'
 */
edit.get = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RecurringInvoiceController::edit
 * @see app/Http/Controllers/RecurringInvoiceController.php:125
 * @route '/recurring-invoices/templates/{template}/edit'
 */
edit.head = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::store
 * @see app/Http/Controllers/RecurringInvoiceController.php:136
 * @route '/recurring-invoices/templates'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/recurring-invoices/templates',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::store
 * @see app/Http/Controllers/RecurringInvoiceController.php:136
 * @route '/recurring-invoices/templates'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::store
 * @see app/Http/Controllers/RecurringInvoiceController.php:136
 * @route '/recurring-invoices/templates'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::update
 * @see app/Http/Controllers/RecurringInvoiceController.php:178
 * @route '/recurring-invoices/templates/{template}'
 */
export const update = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/recurring-invoices/templates/{template}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::update
 * @see app/Http/Controllers/RecurringInvoiceController.php:178
 * @route '/recurring-invoices/templates/{template}'
 */
update.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { template: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    template: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        template: typeof args.template === 'object'
                ? args.template.id
                : args.template,
                }

    return update.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::update
 * @see app/Http/Controllers/RecurringInvoiceController.php:178
 * @route '/recurring-invoices/templates/{template}'
 */
update.put = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroy
 * @see app/Http/Controllers/RecurringInvoiceController.php:218
 * @route '/recurring-invoices/templates/{template}'
 */
export const destroy = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/recurring-invoices/templates/{template}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroy
 * @see app/Http/Controllers/RecurringInvoiceController.php:218
 * @route '/recurring-invoices/templates/{template}'
 */
destroy.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { template: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    template: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        template: typeof args.template === 'object'
                ? args.template.id
                : args.template,
                }

    return destroy.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroy
 * @see app/Http/Controllers/RecurringInvoiceController.php:218
 * @route '/recurring-invoices/templates/{template}'
 */
destroy.delete = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::restore
 * @see app/Http/Controllers/RecurringInvoiceController.php:234
 * @route '/recurring-invoices/templates/{template}/restore'
 */
export const restore = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})

restore.definition = {
    methods: ["post"],
    url: '/recurring-invoices/templates/{template}/restore',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::restore
 * @see app/Http/Controllers/RecurringInvoiceController.php:234
 * @route '/recurring-invoices/templates/{template}/restore'
 */
restore.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { template: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    template: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        template: typeof args.template === 'object'
                ? args.template.id
                : args.template,
                }

    return restore.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::restore
 * @see app/Http/Controllers/RecurringInvoiceController.php:234
 * @route '/recurring-invoices/templates/{template}/restore'
 */
restore.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restore.url(args, options),
    method: 'post',
})
const templates = {
    create: Object.assign(create, create),
edit: Object.assign(edit, edit),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
restore: Object.assign(restore, restore),
}

export default templates