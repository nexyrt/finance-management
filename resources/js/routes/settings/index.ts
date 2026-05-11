import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Livewire\Settings\Profile::__invoke
 * @see app/Livewire/Settings/Profile.php:7
 * @route '/settings/profile'
 */
export const profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/settings/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Settings\Profile::__invoke
 * @see app/Livewire/Settings/Profile.php:7
 * @route '/settings/profile'
 */
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Settings\Profile::__invoke
 * @see app/Livewire/Settings/Profile.php:7
 * @route '/settings/profile'
 */
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Settings\Profile::__invoke
 * @see app/Livewire/Settings/Profile.php:7
 * @route '/settings/profile'
 */
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Settings\Password::__invoke
 * @see app/Livewire/Settings/Password.php:7
 * @route '/settings/password'
 */
export const password = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: password.url(options),
    method: 'get',
})

password.definition = {
    methods: ["get","head"],
    url: '/settings/password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Settings\Password::__invoke
 * @see app/Livewire/Settings/Password.php:7
 * @route '/settings/password'
 */
password.url = (options?: RouteQueryOptions) => {
    return password.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Settings\Password::__invoke
 * @see app/Livewire/Settings/Password.php:7
 * @route '/settings/password'
 */
password.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: password.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Settings\Password::__invoke
 * @see app/Livewire/Settings/Password.php:7
 * @route '/settings/password'
 */
password.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: password.url(options),
    method: 'head',
})

/**
* @see \App\Livewire\Settings\CompanyProfileSettings::__invoke
 * @see app/Livewire/Settings/CompanyProfileSettings.php:7
 * @route '/settings/company'
 */
export const company = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: company.url(options),
    method: 'get',
})

company.definition = {
    methods: ["get","head"],
    url: '/settings/company',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Settings\CompanyProfileSettings::__invoke
 * @see app/Livewire/Settings/CompanyProfileSettings.php:7
 * @route '/settings/company'
 */
company.url = (options?: RouteQueryOptions) => {
    return company.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Settings\CompanyProfileSettings::__invoke
 * @see app/Livewire/Settings/CompanyProfileSettings.php:7
 * @route '/settings/company'
 */
company.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: company.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Settings\CompanyProfileSettings::__invoke
 * @see app/Livewire/Settings/CompanyProfileSettings.php:7
 * @route '/settings/company'
 */
company.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: company.url(options),
    method: 'head',
})
const settings = {
    profile: Object.assign(profile, profile),
password: Object.assign(password, password),
company: Object.assign(company, company),
}

export default settings