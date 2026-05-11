import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\FundRequests\Index::__invoke
 * @see app/Livewire/FundRequests/Index.php:7
 * @route '/fund-requests'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/fund-requests',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\FundRequests\Index::__invoke
 * @see app/Livewire/FundRequests/Index.php:7
 * @route '/fund-requests'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\FundRequests\Index::__invoke
 * @see app/Livewire/FundRequests/Index.php:7
 * @route '/fund-requests'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\FundRequests\Index::__invoke
 * @see app/Livewire/FundRequests/Index.php:7
 * @route '/fund-requests'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index