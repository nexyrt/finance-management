import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Auth\ConfirmPassword::__invoke
 * @see app/Livewire/Auth/ConfirmPassword.php:7
 * @route '/confirm-password'
 */
const ConfirmPassword = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ConfirmPassword.url(options),
    method: 'get',
})

ConfirmPassword.definition = {
    methods: ["get","head"],
    url: '/confirm-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Auth\ConfirmPassword::__invoke
 * @see app/Livewire/Auth/ConfirmPassword.php:7
 * @route '/confirm-password'
 */
ConfirmPassword.url = (options?: RouteQueryOptions) => {
    return ConfirmPassword.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Auth\ConfirmPassword::__invoke
 * @see app/Livewire/Auth/ConfirmPassword.php:7
 * @route '/confirm-password'
 */
ConfirmPassword.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ConfirmPassword.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Auth\ConfirmPassword::__invoke
 * @see app/Livewire/Auth/ConfirmPassword.php:7
 * @route '/confirm-password'
 */
ConfirmPassword.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ConfirmPassword.url(options),
    method: 'head',
})
export default ConfirmPassword