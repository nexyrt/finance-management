import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Livewire\Invoices\Index::__invoke
 * @see app/Livewire/Invoices/Index.php:7
 * @route '/invoices'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/invoices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Invoices\Index::__invoke
 * @see app/Livewire/Invoices/Index.php:7
 * @route '/invoices'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Invoices\Index::__invoke
 * @see app/Livewire/Invoices/Index.php:7
 * @route '/invoices'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Invoices\Index::__invoke
 * @see app/Livewire/Invoices/Index.php:7
 * @route '/invoices'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Invoices\Create::__invoke
 * @see app/Livewire/Invoices/Create.php:7
 * @route '/invoices/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/invoices/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Invoices\Create::__invoke
 * @see app/Livewire/Invoices/Create.php:7
 * @route '/invoices/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Invoices\Create::__invoke
 * @see app/Livewire/Invoices/Create.php:7
 * @route '/invoices/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Invoices\Create::__invoke
 * @see app/Livewire/Invoices/Create.php:7
 * @route '/invoices/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Invoices\Edit::__invoke
 * @see app/Livewire/Invoices/Edit.php:7
 * @route '/invoices/{invoice}/edit'
 */
export const edit = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/invoices/{invoice}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Invoices\Edit::__invoke
 * @see app/Livewire/Invoices/Edit.php:7
 * @route '/invoices/{invoice}/edit'
 */
edit.url = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: args.invoice,
                }

    return edit.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Livewire\Invoices\Edit::__invoke
 * @see app/Livewire/Invoices/Edit.php:7
 * @route '/invoices/{invoice}/edit'
 */
edit.get = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Livewire\Invoices\Edit::__invoke
 * @see app/Livewire/Invoices/Edit.php:7
 * @route '/invoices/{invoice}/edit'
 */
edit.head = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})
const invoices = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
edit: Object.assign(edit, edit),
}

export default invoices