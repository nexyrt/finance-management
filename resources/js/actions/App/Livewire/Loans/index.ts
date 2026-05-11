import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Loans\Index::__invoke
 * @see app/Livewire/Loans/Index.php:7
 * @route '/loans'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/loans',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Loans\Index::__invoke
 * @see app/Livewire/Loans/Index.php:7
 * @route '/loans'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Loans\Index::__invoke
 * @see app/Livewire/Loans/Index.php:7
 * @route '/loans'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Loans\Index::__invoke
 * @see app/Livewire/Loans/Index.php:7
 * @route '/loans'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index