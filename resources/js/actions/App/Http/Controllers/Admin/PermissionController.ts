import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\PermissionController::index
 * @see app/Http/Controllers/Admin/PermissionController.php:17
 * @route '/admin/permissions'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/permissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\PermissionController::index
 * @see app/Http/Controllers/Admin/PermissionController.php:17
 * @route '/admin/permissions'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PermissionController::index
 * @see app/Http/Controllers/Admin/PermissionController.php:17
 * @route '/admin/permissions'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\PermissionController::index
 * @see app/Http/Controllers/Admin/PermissionController.php:17
 * @route '/admin/permissions'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\PermissionController::togglePermission
 * @see app/Http/Controllers/Admin/PermissionController.php:60
 * @route '/admin/permissions/toggle'
 */
export const togglePermission = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: togglePermission.url(options),
    method: 'post',
})

togglePermission.definition = {
    methods: ["post"],
    url: '/admin/permissions/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\PermissionController::togglePermission
 * @see app/Http/Controllers/Admin/PermissionController.php:60
 * @route '/admin/permissions/toggle'
 */
togglePermission.url = (options?: RouteQueryOptions) => {
    return togglePermission.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PermissionController::togglePermission
 * @see app/Http/Controllers/Admin/PermissionController.php:60
 * @route '/admin/permissions/toggle'
 */
togglePermission.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: togglePermission.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\PermissionController::syncModule
 * @see app/Http/Controllers/Admin/PermissionController.php:83
 * @route '/admin/permissions/sync-module'
 */
export const syncModule = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncModule.url(options),
    method: 'post',
})

syncModule.definition = {
    methods: ["post"],
    url: '/admin/permissions/sync-module',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\PermissionController::syncModule
 * @see app/Http/Controllers/Admin/PermissionController.php:83
 * @route '/admin/permissions/sync-module'
 */
syncModule.url = (options?: RouteQueryOptions) => {
    return syncModule.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PermissionController::syncModule
 * @see app/Http/Controllers/Admin/PermissionController.php:83
 * @route '/admin/permissions/sync-module'
 */
syncModule.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncModule.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\PermissionController::syncAll
 * @see app/Http/Controllers/Admin/PermissionController.php:120
 * @route '/admin/permissions/sync-all'
 */
export const syncAll = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncAll.url(options),
    method: 'post',
})

syncAll.definition = {
    methods: ["post"],
    url: '/admin/permissions/sync-all',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\PermissionController::syncAll
 * @see app/Http/Controllers/Admin/PermissionController.php:120
 * @route '/admin/permissions/sync-all'
 */
syncAll.url = (options?: RouteQueryOptions) => {
    return syncAll.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PermissionController::syncAll
 * @see app/Http/Controllers/Admin/PermissionController.php:120
 * @route '/admin/permissions/sync-all'
 */
syncAll.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncAll.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\PermissionController::destroy
 * @see app/Http/Controllers/Admin/PermissionController.php:142
 * @route '/admin/permissions/{permission}'
 */
export const destroy = (args: { permission: number | { id: number } } | [permission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/permissions/{permission}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\PermissionController::destroy
 * @see app/Http/Controllers/Admin/PermissionController.php:142
 * @route '/admin/permissions/{permission}'
 */
destroy.url = (args: { permission: number | { id: number } } | [permission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { permission: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { permission: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    permission: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        permission: typeof args.permission === 'object'
                ? args.permission.id
                : args.permission,
                }

    return destroy.definition.url
            .replace('{permission}', parsedArgs.permission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PermissionController::destroy
 * @see app/Http/Controllers/Admin/PermissionController.php:142
 * @route '/admin/permissions/{permission}'
 */
destroy.delete = (args: { permission: number | { id: number } } | [permission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const PermissionController = { index, togglePermission, syncModule, syncAll, destroy }

export default PermissionController