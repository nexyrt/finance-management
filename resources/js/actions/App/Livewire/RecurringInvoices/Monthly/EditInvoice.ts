import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Livewire\RecurringInvoices\Monthly\EditInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/EditInvoice.php:7
 * @route '/recurring-invoices/monthly/{invoice}/edit'
 */
const EditInvoice = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditInvoice.url(args, options),
    method: 'get',
})

EditInvoice.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/monthly/{invoice}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\RecurringInvoices\Monthly\EditInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/EditInvoice.php:7
 * @route '/recurring-invoices/monthly/{invoice}/edit'
 */
EditInvoice.url = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditInvoice.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Livewire\RecurringInvoices\Monthly\EditInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/EditInvoice.php:7
 * @route '/recurring-invoices/monthly/{invoice}/edit'
 */
EditInvoice.get = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditInvoice.url(args, options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\Monthly\EditInvoice::__invoke
 * @see app/Livewire/RecurringInvoices/Monthly/EditInvoice.php:7
 * @route '/recurring-invoices/monthly/{invoice}/edit'
 */
EditInvoice.head = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditInvoice.url(args, options),
    method: 'head',
})
export default EditInvoice