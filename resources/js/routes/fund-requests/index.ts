import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import exportMethod from './export'
/**
* @see \App\Livewire\FundRequests\Index::__invoke
 * @see app/Livewire/FundRequests/Index.php:7
 * @route '/fund-requests'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/fund-requests',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\FundRequests\Index::__invoke
 * @see app/Livewire/FundRequests/Index.php:7
 * @route '/fund-requests'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\FundRequests\Index::__invoke
 * @see app/Livewire/FundRequests/Index.php:7
 * @route '/fund-requests'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\FundRequests\Index::__invoke
 * @see app/Livewire/FundRequests/Index.php:7
 * @route '/fund-requests'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const fundRequests = {
    index: Object.assign(index, index),
export: Object.assign(exportMethod, exportMethod),
}

export default fundRequests