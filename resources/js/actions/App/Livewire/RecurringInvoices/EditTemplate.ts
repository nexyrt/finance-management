import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Livewire\RecurringInvoices\EditTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/EditTemplate.php:7
 * @route '/recurring-invoices/template/{template}/edit'
 */
const EditTemplate = (args: { template: string | number } | [template: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditTemplate.url(args, options),
    method: 'get',
})

EditTemplate.definition = {
    methods: ["get","head"],
    url: '/recurring-invoices/template/{template}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\RecurringInvoices\EditTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/EditTemplate.php:7
 * @route '/recurring-invoices/template/{template}/edit'
 */
EditTemplate.url = (args: { template: string | number } | [template: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditTemplate.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Livewire\RecurringInvoices\EditTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/EditTemplate.php:7
 * @route '/recurring-invoices/template/{template}/edit'
 */
EditTemplate.get = (args: { template: string | number } | [template: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditTemplate.url(args, options),
    method: 'get',
})
/**
* @see \App\Livewire\RecurringInvoices\EditTemplate::__invoke
 * @see app/Livewire/RecurringInvoices/EditTemplate.php:7
 * @route '/recurring-invoices/template/{template}/edit'
 */
EditTemplate.head = (args: { template: string | number } | [template: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditTemplate.url(args, options),
    method: 'head',
})
export default EditTemplate