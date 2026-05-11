import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Livewire\RecurringInvoices\Monthly\CreateInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/CreateInvoice.php:7
 * @route '/recurring-invoices/monthly/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/monthly/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\RecurringInvoices\Monthly\CreateInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/CreateInvoice.php:7
 * @route '/recurring-invoices/monthly/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\RecurringInvoices\Monthly\CreateInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/CreateInvoice.php:7
 * @route '/recurring-invoices/monthly/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\Monthly\CreateInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/CreateInvoice.php:7
 * @route '/recurring-invoices/monthly/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\RecurringInvoices\Monthly\EditInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/EditInvoice.php:7
 * @route '/recurring-invoices/monthly/{invoice}/edit'
 */
export const edit = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/monthly/{invoice}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\RecurringInvoices\Monthly\EditInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/EditInvoice.php:7
 * @route '/recurring-invoices/monthly/{invoice}/edit'
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
* @see \App\Livewire\RecurringInvoices\Monthly\EditInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/EditInvoice.php:7
 * @route '/recurring-invoices/monthly/{invoice}/edit'
 */
edit.get = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\Monthly\EditInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/EditInvoice.php:7
 * @route '/recurring-invoices/monthly/{invoice}/edit'
 */
edit.head = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})
const monthly = {
    create: Object.assign(create, create),
edit: Object.assign(edit, edit),
}

export default monthly