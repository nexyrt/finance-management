import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Reimbursements\Index::__invoke
 * @see app/Livewire/Reimbursements/Index.php:7
 * @route '/reimbursements'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/reimbursements',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Reimbursements\Index::__invoke
 * @see app/Livewire/Reimbursements/Index.php:7
 * @route '/reimbursements'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Reimbursements\Index::__invoke
 * @see app/Livewire/Reimbursements/Index.php:7
 * @route '/reimbursements'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Reimbursements\Index::__invoke
 * @see app/Livewire/Reimbursements/Index.php:7
 * @route '/reimbursements'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index