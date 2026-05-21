import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\CompanyController::update
 * @see app/Http/Controllers/Settings/CompanyController.php:44
 * @route '/settings/company'
 */
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/settings/company',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\CompanyController::update
 * @see app/Http/Controllers/Settings/CompanyController.php:44
 * @route '/settings/company'
 */
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\CompanyController::update
 * @see app/Http/Controllers/Settings/CompanyController.php:44
 * @route '/settings/company'
 */
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\CompanyController::deleteAsset
 * @see app/Http/Controllers/Settings/CompanyController.php:95
 * @route '/settings/company/assets/{asset}'
 */
export const deleteAsset = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteAsset.url(args, options),
    method: 'delete',
})

deleteAsset.definition = {
    methods: ["delete"],
    url: '/settings/company/assets/{asset}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Settings\CompanyController::deleteAsset
 * @see app/Http/Controllers/Settings/CompanyController.php:95
 * @route '/settings/company/assets/{asset}'
 */
deleteAsset.url = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { asset: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    asset: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        asset: args.asset,
                }

    return deleteAsset.definition.url
            .replace('{asset}', parsedArgs.asset.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\CompanyController::deleteAsset
 * @see app/Http/Controllers/Settings/CompanyController.php:95
 * @route '/settings/company/assets/{asset}'
 */
deleteAsset.delete = (args: { asset: string | number } | [asset: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteAsset.url(args, options),
    method: 'delete',
})
const company = {
    update: Object.assign(update, update),
deleteAsset: Object.assign(deleteAsset, deleteAsset),
}

export default company