import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Settings\CompanyProfileSettings::__invoke
 * @see app/Livewire/Settings/CompanyProfileSettings.php:7
 * @route '/settings/company'
 */
const CompanyProfileSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CompanyProfileSettings.url(options),
    method: 'get',
})

CompanyProfileSettings.definition = {
    methods: ["get","head"],
    url: '/settings/company',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Settings\CompanyProfileSettings::__invoke
 * @see app/Livewire/Settings/CompanyProfileSettings.php:7
 * @route '/settings/company'
 */
CompanyProfileSettings.url = (options?: RouteQueryOptions) => {
    return CompanyProfileSettings.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Settings\CompanyProfileSettings::__invoke
 * @see app/Livewire/Settings/CompanyProfileSettings.php:7
 * @route '/settings/company'
 */
CompanyProfileSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CompanyProfileSettings.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Settings\CompanyProfileSettings::__invoke
 * @see app/Livewire/Settings/CompanyProfileSettings.php:7
 * @route '/settings/company'
 */
CompanyProfileSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CompanyProfileSettings.url(options),
    method: 'head',
})
export default CompanyProfileSettings