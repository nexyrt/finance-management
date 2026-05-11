import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Livewire\Auth\ForgotPassword::__invoke
 * @see app/Livewire/Auth/ForgotPassword.php:7
 * @route '/forgot-password'
 */
export const request = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: request.url(options),
    method: 'get',
})

request.definition = {
    methods: ["get","head"],
    url: '/forgot-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Auth\ForgotPassword::__invoke
 * @see app/Livewire/Auth/ForgotPassword.php:7
 * @route '/forgot-password'
 */
request.url = (options?: RouteQueryOptions) => {
    return request.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Auth\ForgotPassword::__invoke
 * @see app/Livewire/Auth/ForgotPassword.php:7
 * @route '/forgot-password'
 */
request.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: request.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Auth\ForgotPassword::__invoke
 * @see app/Livewire/Auth/ForgotPassword.php:7
 * @route '/forgot-password'
 */
request.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: request.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Auth\ResetPassword::__invoke
 * @see app/Livewire/Auth/ResetPassword.php:7
 * @route '/reset-password/{token}'
 */
export const reset = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(args, options),
    method: 'get',
})

reset.definition = {
    methods: ["get","head"],
    url: '/reset-password/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Auth\ResetPassword::__invoke
 * @see app/Livewire/Auth/ResetPassword.php:7
 * @route '/reset-password/{token}'
 */
reset.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    token: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        token: args.token,
                }

    return reset.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Livewire\Auth\ResetPassword::__invoke
 * @see app/Livewire/Auth/ResetPassword.php:7
 * @route '/reset-password/{token}'
 */
reset.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(args, options),
    method: 'get',
})
/**
* @see \App\Livewire\Auth\ResetPassword::__invoke
 * @see app/Livewire/Auth/ResetPassword.php:7
 * @route '/reset-password/{token}'
 */
reset.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reset.url(args, options),
    method: 'head',
})

/**
* @see \App\Livewire\Auth\ConfirmPassword::__invoke
 * @see app/Livewire/Auth/ConfirmPassword.php:7
 * @route '/confirm-password'
 */
export const confirm = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(options),
    method: 'get',
})

confirm.definition = {
    methods: ["get","head"],
    url: '/confirm-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Auth\ConfirmPassword::__invoke
 * @see app/Livewire/Auth/ConfirmPassword.php:7
 * @route '/confirm-password'
 */
confirm.url = (options?: RouteQueryOptions) => {
    return confirm.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Auth\ConfirmPassword::__invoke
 * @see app/Livewire/Auth/ConfirmPassword.php:7
 * @route '/confirm-password'
 */
confirm.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Auth\ConfirmPassword::__invoke
 * @see app/Livewire/Auth/ConfirmPassword.php:7
 * @route '/confirm-password'
 */
confirm.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirm.url(options),
    method: 'head',
})
const password = {
    request: Object.assign(request, request),
reset: Object.assign(reset, reset),
confirm: Object.assign(confirm, confirm),
}

export default password