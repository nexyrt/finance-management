import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Settings\Password::__invoke
 * @see app/Livewire/Settings/Password.php:7
 * @route '/settings/password'
 */
const Password = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Password.url(options),
    method: 'get',
})

Password.definition = {
    methods: ["get","head"],
    url: '/settings/password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Settings\Password::__invoke
 * @see app/Livewire/Settings/Password.php:7
 * @route '/settings/password'
 */
Password.url = (options?: RouteQueryOptions) => {
    return Password.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Settings\Password::__invoke
 * @see app/Livewire/Settings/Password.php:7
 * @route '/settings/password'
 */
Password.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Password.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Settings\Password::__invoke
 * @see app/Livewire/Settings/Password.php:7
 * @route '/settings/password'
 */
Password.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Password.url(options),
    method: 'head',
})
export default Password