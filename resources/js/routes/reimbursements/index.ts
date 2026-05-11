import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Livewire\Reimbursements\Index::__invoke
 * @see app/Livewire/Reimbursements/Index.php:7
 * @route '/reimbursements'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/reimbursements',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Reimbursements\Index::__invoke
 * @see app/Livewire/Reimbursements/Index.php:7
 * @route '/reimbursements'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Reimbursements\Index::__invoke
 * @see app/Livewire/Reimbursements/Index.php:7
 * @route '/reimbursements'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Reimbursements\Index::__invoke
 * @see app/Livewire/Reimbursements/Index.php:7
 * @route '/reimbursements'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const reimbursements = {
    index: Object.assign(index, index),
}

export default reimbursements