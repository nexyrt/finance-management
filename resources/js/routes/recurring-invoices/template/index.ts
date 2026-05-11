import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Livewire\RecurringInvoices\CreateTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/CreateTemplate.php:7
 * @route '/recurring-invoices/template/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/template/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\RecurringInvoices\CreateTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/CreateTemplate.php:7
 * @route '/recurring-invoices/template/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\RecurringInvoices\CreateTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/CreateTemplate.php:7
 * @route '/recurring-invoices/template/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\CreateTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/CreateTemplate.php:7
 * @route '/recurring-invoices/template/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\RecurringInvoices\EditTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/EditTemplate.php:7
 * @route '/recurring-invoices/template/{template}/edit'
 */
export const edit = (args: { template: string | number } | [template: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/template/{template}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\RecurringInvoices\EditTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/EditTemplate.php:7
 * @route '/recurring-invoices/template/{template}/edit'
 */
edit.url = (args: { template: string | number } | [template: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    template: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        template: args.template,
                }

    return edit.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Livewire\RecurringInvoices\EditTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/EditTemplate.php:7
 * @route '/recurring-invoices/template/{template}/edit'
 */
edit.get = (args: { template: string | number } | [template: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\EditTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/EditTemplate.php:7
 * @route '/recurring-invoices/template/{template}/edit'
 */
edit.head = (args: { template: string | number } | [template: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})
const template = {
    create: Object.assign(create, create),
edit: Object.assign(edit, edit),
}

export default template