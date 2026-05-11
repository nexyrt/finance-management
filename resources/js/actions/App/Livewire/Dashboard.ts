import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Livewire\Dashboard::__invoke
 * @see app/Livewire/Dashboard.php:7
 * @route '/dashboard'
 */
const Dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Dashboard.url(options),
    method: 'get',
})

Dashboard.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Dashboard::__invoke
 * @see app/Livewire/Dashboard.php:7
 * @route '/dashboard'
 */
Dashboard.url = (options?: RouteQueryOptions) => {
    return Dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Dashboard::__invoke
 * @see app/Livewire/Dashboard.php:7
 * @route '/dashboard'
 */
Dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Dashboard.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Dashboard::__invoke
 * @see app/Livewire/Dashboard.php:7
 * @route '/dashboard'
 */
Dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Dashboard.url(options),
    method: 'head',
})
export default Dashboard