import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import profile937a89 from './profile'
import password9cfa90 from './password'
import company890735 from './company'
/**
* @see \App\Http\Controllers\Settings\ProfileController::profile
 * @see app/Http/Controllers/Settings/ProfileController.php:17
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
* @see \App\Http\Controllers\Settings\ProfileController::profile
 * @see app/Http/Controllers/Settings/ProfileController.php:17
 * @route '/settings/profile'
 */
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\ProfileController::profile
 * @see app/Http/Controllers/Settings/ProfileController.php:17
 * @route '/settings/profile'
 */
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Settings\ProfileController::profile
 * @see app/Http/Controllers/Settings/ProfileController.php:17
 * @route '/settings/profile'
 */
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Settings\PasswordController::password
 * @see app/Http/Controllers/Settings/PasswordController.php:16
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
* @see \App\Http\Controllers\Settings\PasswordController::password
 * @see app/Http/Controllers/Settings/PasswordController.php:16
 * @route '/settings/password'
 */
password.url = (options?: RouteQueryOptions) => {
    return password.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\PasswordController::password
 * @see app/Http/Controllers/Settings/PasswordController.php:16
 * @route '/settings/password'
 */
password.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: password.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Settings\PasswordController::password
 * @see app/Http/Controllers/Settings/PasswordController.php:16
 * @route '/settings/password'
 */
password.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: password.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Settings\CompanyController::company
 * @see app/Http/Controllers/Settings/CompanyController.php:16
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
* @see \App\Http\Controllers\Settings\CompanyController::company
 * @see app/Http/Controllers/Settings/CompanyController.php:16
 * @route '/settings/company'
 */
company.url = (options?: RouteQueryOptions) => {
    return company.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\CompanyController::company
 * @see app/Http/Controllers/Settings/CompanyController.php:16
 * @route '/settings/company'
 */
company.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: company.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Settings\CompanyController::company
 * @see app/Http/Controllers/Settings/CompanyController.php:16
 * @route '/settings/company'
 */
company.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: company.url(options),
    method: 'head',
})
const settings = {
    profile: Object.assign(profile, profile937a89),
password: Object.assign(password, password9cfa90),
company: Object.assign(company, company890735),
}

export default settings