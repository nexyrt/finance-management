import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Actions\Logout::__invoke
 * @see app/Livewire/Actions/Logout.php:13
 * @route '/logout'
 */
const Logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: Logout.url(options),
    method: 'post',
})

Logout.definition = {
    methods: ["post"],
    url: '/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Livewire\Actions\Logout::__invoke
 * @see app/Livewire/Actions/Logout.php:13
 * @route '/logout'
 */
Logout.url = (options?: RouteQueryOptions) => {
    return Logout.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Actions\Logout::__invoke
 * @see app/Livewire/Actions/Logout.php:13
 * @route '/logout'
 */
Logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: Logout.url(options),
    method: 'post',
})
export default Logout