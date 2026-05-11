import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Settings\Profile::__invoke
 * @see app/Livewire/Settings/Profile.php:7
 * @route '/settings/profile'
 */
const Profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Profile.url(options),
    method: 'get',
})

Profile.definition = {
    methods: ["get","head"],
    url: '/settings/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Settings\Profile::__invoke
 * @see app/Livewire/Settings/Profile.php:7
 * @route '/settings/profile'
 */
Profile.url = (options?: RouteQueryOptions) => {
    return Profile.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Settings\Profile::__invoke
 * @see app/Livewire/Settings/Profile.php:7
 * @route '/settings/profile'
 */
Profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Profile.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Settings\Profile::__invoke
 * @see app/Livewire/Settings/Profile.php:7
 * @route '/settings/profile'
 */
Profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Profile.url(options),
    method: 'head',
})
export default Profile