import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\CompanyController::edit
 * @see app/Http/Controllers/Settings/CompanyController.php:15
 * @route '/settings/company'
 */
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/company',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\CompanyController::edit
 * @see app/Http/Controllers/Settings/CompanyController.php:15
 * @route '/settings/company'
 */
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\CompanyController::edit
 * @see app/Http/Controllers/Settings/CompanyController.php:15
 * @route '/settings/company'
 */
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Settings\CompanyController::edit
 * @see app/Http/Controllers/Settings/CompanyController.php:15
 * @route '/settings/company'
 */
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

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
const CompanyController = { edit, update, deleteAsset }

export default CompanyController