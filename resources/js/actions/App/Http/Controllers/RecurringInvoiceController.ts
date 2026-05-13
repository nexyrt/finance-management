import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\RecurringInvoiceController::index
 * @see app/Http/Controllers/RecurringInvoiceController.php:18
 * @route '/recurring-invoices'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::index
 * @see app/Http/Controllers/RecurringInvoiceController.php:18
 * @route '/recurring-invoices'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::index
 * @see app/Http/Controllers/RecurringInvoiceController.php:18
 * @route '/recurring-invoices'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RecurringInvoiceController::index
 * @see app/Http/Controllers/RecurringInvoiceController.php:18
 * @route '/recurring-invoices'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::createTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:112
 * @route '/recurring-invoices/templates/create'
 */
export const createTemplate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createTemplate.url(options),
    method: 'get',
})

createTemplate.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/templates/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::createTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:112
 * @route '/recurring-invoices/templates/create'
 */
createTemplate.url = (options?: RouteQueryOptions) => {
    return createTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::createTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:112
 * @route '/recurring-invoices/templates/create'
 */
createTemplate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createTemplate.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RecurringInvoiceController::createTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:112
 * @route '/recurring-invoices/templates/create'
 */
createTemplate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: createTemplate.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::editTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:120
 * @route '/recurring-invoices/templates/{template}/edit'
 */
export const editTemplate = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editTemplate.url(args, options),
    method: 'get',
})

editTemplate.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/templates/{template}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::editTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:120
 * @route '/recurring-invoices/templates/{template}/edit'
 */
editTemplate.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return editTemplate.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::editTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:120
 * @route '/recurring-invoices/templates/{template}/edit'
 */
editTemplate.get = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editTemplate.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RecurringInvoiceController::editTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:120
 * @route '/recurring-invoices/templates/{template}/edit'
 */
editTemplate.head = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: editTemplate.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::storeTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:131
 * @route '/recurring-invoices/templates'
 */
export const storeTemplate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeTemplate.url(options),
    method: 'post',
})

storeTemplate.definition = {
    methods: ["post"],
    url: '/recurring-invoices/templates',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::storeTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:131
 * @route '/recurring-invoices/templates'
 */
storeTemplate.url = (options?: RouteQueryOptions) => {
    return storeTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::storeTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:131
 * @route '/recurring-invoices/templates'
 */
storeTemplate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeTemplate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::updateTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:190
 * @route '/recurring-invoices/templates/{template}'
 */
export const updateTemplate = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateTemplate.url(args, options),
    method: 'put',
})

updateTemplate.definition = {
    methods: ["put"],
    url: '/recurring-invoices/templates/{template}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::updateTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:190
 * @route '/recurring-invoices/templates/{template}'
 */
updateTemplate.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateTemplate.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::updateTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:190
 * @route '/recurring-invoices/templates/{template}'
 */
updateTemplate.put = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateTemplate.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroyTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:247
 * @route '/recurring-invoices/templates/{template}'
 */
export const destroyTemplate = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyTemplate.url(args, options),
    method: 'delete',
})

destroyTemplate.definition = {
    methods: ["delete"],
    url: '/recurring-invoices/templates/{template}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroyTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:247
 * @route '/recurring-invoices/templates/{template}'
 */
destroyTemplate.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroyTemplate.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroyTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:247
 * @route '/recurring-invoices/templates/{template}'
 */
destroyTemplate.delete = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyTemplate.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::restoreTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:263
 * @route '/recurring-invoices/templates/{template}/restore'
 */
export const restoreTemplate = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restoreTemplate.url(args, options),
    method: 'post',
})

restoreTemplate.definition = {
    methods: ["post"],
    url: '/recurring-invoices/templates/{template}/restore',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::restoreTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:263
 * @route '/recurring-invoices/templates/{template}/restore'
 */
restoreTemplate.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return restoreTemplate.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::restoreTemplate
 * @see app/Http/Controllers/RecurringInvoiceController.php:263
 * @route '/recurring-invoices/templates/{template}/restore'
 */
restoreTemplate.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restoreTemplate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::generateMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:275
 * @route '/recurring-invoices/monthly/generate'
 */
export const generateMonthly = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateMonthly.url(options),
    method: 'post',
})

generateMonthly.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::generateMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:275
 * @route '/recurring-invoices/monthly/generate'
 */
generateMonthly.url = (options?: RouteQueryOptions) => {
    return generateMonthly.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::generateMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:275
 * @route '/recurring-invoices/monthly/generate'
 */
generateMonthly.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateMonthly.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::storeMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:326
 * @route '/recurring-invoices/monthly'
 */
export const storeMonthly = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeMonthly.url(options),
    method: 'post',
})

storeMonthly.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::storeMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:326
 * @route '/recurring-invoices/monthly'
 */
storeMonthly.url = (options?: RouteQueryOptions) => {
    return storeMonthly.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::storeMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:326
 * @route '/recurring-invoices/monthly'
 */
storeMonthly.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeMonthly.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::updateMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:387
 * @route '/recurring-invoices/monthly/{invoice}'
 */
export const updateMonthly = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateMonthly.url(args, options),
    method: 'put',
})

updateMonthly.definition = {
    methods: ["put"],
    url: '/recurring-invoices/monthly/{invoice}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::updateMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:387
 * @route '/recurring-invoices/monthly/{invoice}'
 */
updateMonthly.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateMonthly.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::updateMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:387
 * @route '/recurring-invoices/monthly/{invoice}'
 */
updateMonthly.put = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateMonthly.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroyMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:434
 * @route '/recurring-invoices/monthly/{invoice}'
 */
export const destroyMonthly = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyMonthly.url(args, options),
    method: 'delete',
})

destroyMonthly.definition = {
    methods: ["delete"],
    url: '/recurring-invoices/monthly/{invoice}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroyMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:434
 * @route '/recurring-invoices/monthly/{invoice}'
 */
destroyMonthly.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroyMonthly.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::destroyMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:434
 * @route '/recurring-invoices/monthly/{invoice}'
 */
destroyMonthly.delete = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyMonthly.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::publishMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:445
 * @route '/recurring-invoices/monthly/{invoice}/publish'
 */
export const publishMonthly = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publishMonthly.url(args, options),
    method: 'post',
})

publishMonthly.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly/{invoice}/publish',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::publishMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:445
 * @route '/recurring-invoices/monthly/{invoice}/publish'
 */
publishMonthly.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return publishMonthly.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::publishMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:445
 * @route '/recurring-invoices/monthly/{invoice}/publish'
 */
publishMonthly.post = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publishMonthly.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkDestroyMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:474
 * @route '/recurring-invoices/monthly/bulk-destroy'
 */
export const bulkDestroyMonthly = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkDestroyMonthly.url(options),
    method: 'post',
})

bulkDestroyMonthly.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly/bulk-destroy',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkDestroyMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:474
 * @route '/recurring-invoices/monthly/bulk-destroy'
 */
bulkDestroyMonthly.url = (options?: RouteQueryOptions) => {
    return bulkDestroyMonthly.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkDestroyMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:474
 * @route '/recurring-invoices/monthly/bulk-destroy'
 */
bulkDestroyMonthly.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkDestroyMonthly.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkPublishMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:488
 * @route '/recurring-invoices/monthly/bulk-publish'
 */
export const bulkPublishMonthly = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkPublishMonthly.url(options),
    method: 'post',
})

bulkPublishMonthly.definition = {
    methods: ["post"],
    url: '/recurring-invoices/monthly/bulk-publish',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkPublishMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:488
 * @route '/recurring-invoices/monthly/bulk-publish'
 */
bulkPublishMonthly.url = (options?: RouteQueryOptions) => {
    return bulkPublishMonthly.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RecurringInvoiceController::bulkPublishMonthly
 * @see app/Http/Controllers/RecurringInvoiceController.php:488
 * @route '/recurring-invoices/monthly/bulk-publish'
 */
bulkPublishMonthly.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkPublishMonthly.url(options),
    method: 'post',
})
const RecurringInvoiceController = { index, createTemplate, editTemplate, storeTemplate, updateTemplate, destroyTemplate, restoreTemplate, generateMonthly, storeMonthly, updateMonthly, destroyMonthly, publishMonthly, bulkDestroyMonthly, bulkPublishMonthly }

export default RecurringInvoiceController