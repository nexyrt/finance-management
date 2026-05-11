import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Livewire\Invoices\Edit::__invoke
 * @see app/Livewire/Invoices/Edit.php:7
 * @route '/invoices/{invoice}/edit'
 */
const Edit = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Edit.url(args, options),
    method: 'get',
})

Edit.definition = {
    methods: ["get","head"],
    url: '/invoices/{invoice}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Invoices\Edit::__invoke
 * @see app/Livewire/Invoices/Edit.php:7
 * @route '/invoices/{invoice}/edit'
 */
Edit.url = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return Edit.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Livewire\Invoices\Edit::__invoke
 * @see app/Livewire/Invoices/Edit.php:7
 * @route '/invoices/{invoice}/edit'
 */
Edit.get = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Livewire\Invoices\Edit::__invoke
 * @see app/Livewire/Invoices/Edit.php:7
 * @route '/invoices/{invoice}/edit'
 */
Edit.head = (args: { invoice: string | number } | [invoice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Edit.url(args, options),
    method: 'head',
})
export default Edit