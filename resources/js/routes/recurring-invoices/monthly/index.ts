import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\RecurringInvoiceController::generate
 * @see app/Http/Controllers/RecurringInvoiceController.php:236
 * @route '/recurring-invoices/monthly/generate'
 */
export const generate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::generate
 * @see app/Http/Controllers/RecurringInvoiceController.php:236
 * @route '/recurring-invoices/monthly/generate'
 */
generate.url = (options?: RouteQueryOptions) => {
    return generate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::generate
 * @see app/Http/Controllers/RecurringInvoiceController.php:236
 * @route '/recurring-invoices/monthly/generate'
 */
generate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::store
 * @see app/Http/Controllers/RecurringInvoiceController.php:287
 * @route '/recurring-invoices/monthly'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::store
 * @see app/Http/Controllers/RecurringInvoiceController.php:287
 * @route '/recurring-invoices/monthly'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::store
 * @see app/Http/Controllers/RecurringInvoiceController.php:287
 * @route '/recurring-invoices/monthly'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::update
 * @see app/Http/Controllers/RecurringInvoiceController.php:348
 * @route '/recurring-invoices/monthly/{invoice}'
 */
export const update = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/recurring-invoices/monthly/{invoice}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::update
 * @see app/Http/Controllers/RecurringInvoiceController.php:348
 * @route '/recurring-invoices/monthly/{invoice}'
 */
update.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { invoice: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: typeof args.invoice === 'object'
                ? args.invoice.id
                : args.invoice,
                }

    return update.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::update
 * @see app/Http/Controllers/RecurringInvoiceController.php:348
 * @route '/recurring-invoices/monthly/{invoice}'
 */
update.put = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroy
 * @see app/Http/Controllers/RecurringInvoiceController.php:395
 * @route '/recurring-invoices/monthly/{invoice}'
 */
export const destroy = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/recurring-invoices/monthly/{invoice}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroy
 * @see app/Http/Controllers/RecurringInvoiceController.php:395
 * @route '/recurring-invoices/monthly/{invoice}'
 */
destroy.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { invoice: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: typeof args.invoice === 'object'
                ? args.invoice.id
                : args.invoice,
                }

    return destroy.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroy
 * @see app/Http/Controllers/RecurringInvoiceController.php:395
 * @route '/recurring-invoices/monthly/{invoice}'
 */
destroy.delete = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::publish
 * @see app/Http/Controllers/RecurringInvoiceController.php:406
 * @route '/recurring-invoices/monthly/{invoice}/publish'
 */
export const publish = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publish.url(args, options),
    method: 'post',
})

publish.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly/{invoice}/publish',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::publish
 * @see app/Http/Controllers/RecurringInvoiceController.php:406
 * @route '/recurring-invoices/monthly/{invoice}/publish'
 */
publish.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { invoice: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: typeof args.invoice === 'object'
                ? args.invoice.id
                : args.invoice,
                }

    return publish.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::publish
 * @see app/Http/Controllers/RecurringInvoiceController.php:406
 * @route '/recurring-invoices/monthly/{invoice}/publish'
 */
publish.post = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publish.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkDestroy
 * @see app/Http/Controllers/RecurringInvoiceController.php:435
 * @route '/recurring-invoices/monthly/bulk-destroy'
 */
export const bulkDestroy = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkDestroy.url(options),
    method: 'post',
})

bulkDestroy.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly/bulk-destroy',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkDestroy
 * @see app/Http/Controllers/RecurringInvoiceController.php:435
 * @route '/recurring-invoices/monthly/bulk-destroy'
 */
bulkDestroy.url = (options?: RouteQueryOptions) => {
    return bulkDestroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkDestroy
 * @see app/Http/Controllers/RecurringInvoiceController.php:435
 * @route '/recurring-invoices/monthly/bulk-destroy'
 */
bulkDestroy.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkDestroy.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkPublish
 * @see app/Http/Controllers/RecurringInvoiceController.php:449
 * @route '/recurring-invoices/monthly/bulk-publish'
 */
export const bulkPublish = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkPublish.url(options),
    method: 'post',
})

bulkPublish.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly/bulk-publish',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkPublish
 * @see app/Http/Controllers/RecurringInvoiceController.php:449
 * @route '/recurring-invoices/monthly/bulk-publish'
 */
bulkPublish.url = (options?: RouteQueryOptions) => {
    return bulkPublish.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkPublish
 * @see app/Http/Controllers/RecurringInvoiceController.php:449
 * @route '/recurring-invoices/monthly/bulk-publish'
 */
bulkPublish.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkPublish.url(options),
    method: 'post',
})
const monthly = {
    generate: Object.assign(generate, generate),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
publish: Object.assign(publish, publish),
bulkDestroy: Object.assign(bulkDestroy, bulkDestroy),
bulkPublish: Object.assign(bulkPublish, bulkPublish),
}

export default monthly