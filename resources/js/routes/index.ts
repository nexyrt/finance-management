import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../wayfinder'
/**
* @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/'
 */
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head","post","put","patch","delete","options"],
    url: '/',
} satisfies RouteDefinition<["get","head","post","put","patch","delete","options"]>

/**
* @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/'
 */
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/'
 */
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})
/**
* @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/'
 */
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})
/**
* @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/'
 */
home.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: home.url(options),
    method: 'post',
})
/**
* @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/'
 */
home.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: home.url(options),
    method: 'put',
})
/**
* @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/'
 */
home.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: home.url(options),
    method: 'patch',
})
/**
* @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/'
 */
home.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: home.url(options),
    method: 'delete',
})
/**
* @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/'
 */
home.options = (options?: RouteQueryOptions): RouteDefinition<'options'> => ({
    url: home.url(options),
    method: 'options',
})

/**
* @see \App\Livewire\Dashboard::__invoke
 * @see app/Livewire/Dashboard.php:7
 * @route '/dashboard'
 */
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Dashboard::__invoke
 * @see app/Livewire/Dashboard.php:7
 * @route '/dashboard'
 */
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Dashboard::__invoke
 * @see app/Livewire/Dashboard.php:7
 * @route '/dashboard'
 */
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Dashboard::__invoke
 * @see app/Livewire/Dashboard.php:7
 * @route '/dashboard'
 */
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Clients\Index::__invoke
 * @see app/Livewire/Clients/Index.php:7
 * @route '/clients'
 */
export const clients = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: clients.url(options),
    method: 'get',
})

clients.definition = {
    methods: ["get","head"],
    url: '/clients',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Clients\Index::__invoke
 * @see app/Livewire/Clients/Index.php:7
 * @route '/clients'
 */
clients.url = (options?: RouteQueryOptions) => {
    return clients.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Clients\Index::__invoke
 * @see app/Livewire/Clients/Index.php:7
 * @route '/clients'
 */
clients.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: clients.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Clients\Index::__invoke
 * @see app/Livewire/Clients/Index.php:7
 * @route '/clients'
 */
clients.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: clients.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Services\Index::__invoke
 * @see app/Livewire/Services/Index.php:7
 * @route '/services'
 */
export const services = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: services.url(options),
    method: 'get',
})

services.definition = {
    methods: ["get","head"],
    url: '/services',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Services\Index::__invoke
 * @see app/Livewire/Services/Index.php:7
 * @route '/services'
 */
services.url = (options?: RouteQueryOptions) => {
    return services.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Services\Index::__invoke
 * @see app/Livewire/Services/Index.php:7
 * @route '/services'
 */
services.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: services.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Services\Index::__invoke
 * @see app/Livewire/Services/Index.php:7
 * @route '/services'
 */
services.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: services.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\TestingPage::__invoke
 * @see app/Livewire/TestingPage.php:7
 * @route '/test'
 */
export const test = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(options),
    method: 'get',
})

test.definition = {
    methods: ["get","head"],
    url: '/test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\TestingPage::__invoke
 * @see app/Livewire/TestingPage.php:7
 * @route '/test'
 */
test.url = (options?: RouteQueryOptions) => {
    return test.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\TestingPage::__invoke
 * @see app/Livewire/TestingPage.php:7
 * @route '/test'
 */
test.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\TestingPage::__invoke
 * @see app/Livewire/TestingPage.php:7
 * @route '/test'
 */
test.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: test.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Auth\Login::__invoke
 * @see app/Livewire/Auth/Login.php:7
 * @route '/login'
 */
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Auth\Login::__invoke
 * @see app/Livewire/Auth/Login.php:7
 * @route '/login'
 */
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Auth\Login::__invoke
 * @see app/Livewire/Auth/Login.php:7
 * @route '/login'
 */
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Auth\Login::__invoke
 * @see app/Livewire/Auth/Login.php:7
 * @route '/login'
 */
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Auth\Register::__invoke
 * @see app/Livewire/Auth/Register.php:7
 * @route '/buat-akun'
 */
export const register = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '/buat-akun',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Auth\Register::__invoke
 * @see app/Livewire/Auth/Register.php:7
 * @route '/buat-akun'
 */
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Auth\Register::__invoke
 * @see app/Livewire/Auth/Register.php:7
 * @route '/buat-akun'
 */
register.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Auth\Register::__invoke
 * @see app/Livewire/Auth/Register.php:7
 * @route '/buat-akun'
 */
register.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Actions\Logout::__invoke
 * @see app/Livewire/Actions/Logout.php:13
 * @route '/logout'
 */
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Livewire\Actions\Logout::__invoke
 * @see app/Livewire/Actions/Logout.php:13
 * @route '/logout'
 */
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Actions\Logout::__invoke
 * @see app/Livewire/Actions/Logout.php:13
 * @route '/logout'
 */
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})