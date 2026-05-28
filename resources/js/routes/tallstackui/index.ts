import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults, validateParameters } from './../../wayfinder'
/**
* @see \TallStackUi\Foundation\Http\Controllers\TallStackUiAssetsController::script
 * @see vendor/tallstackui/tallstackui/src/Foundation/Http/Controllers/TallStackUiAssetsController.php:18
 * @route '/tallstackui/script/{file?}'
 */
export const script = (args?: { file?: string | number } | [file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: script.url(args, options),
    method: 'get',
})

script.definition = {
    methods: ["get","head"],
    url: '/tallstackui/script/{file?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \TallStackUi\Foundation\Http\Controllers\TallStackUiAssetsController::script
 * @see vendor/tallstackui/tallstackui/src/Foundation/Http/Controllers/TallStackUiAssetsController.php:18
 * @route '/tallstackui/script/{file?}'
 */
script.url = (args?: { file?: string | number } | [file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
            "file",
        ])

    const parsedArgs = {
                        file: args?.file,
                }

    return script.definition.url
            .replace('{file?}', parsedArgs.file?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \TallStackUi\Foundation\Http\Controllers\TallStackUiAssetsController::script
 * @see vendor/tallstackui/tallstackui/src/Foundation/Http/Controllers/TallStackUiAssetsController.php:18
 * @route '/tallstackui/script/{file?}'
 */
script.get = (args?: { file?: string | number } | [file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: script.url(args, options),
    method: 'get',
})
/**
* @see \TallStackUi\Foundation\Http\Controllers\TallStackUiAssetsController::script
 * @see vendor/tallstackui/tallstackui/src/Foundation/Http/Controllers/TallStackUiAssetsController.php:18
 * @route '/tallstackui/script/{file?}'
 */
script.head = (args?: { file?: string | number } | [file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: script.url(args, options),
    method: 'head',
})

/**
* @see \TallStackUi\Foundation\Http\Controllers\TallStackUiAssetsController::style
 * @see vendor/tallstackui/tallstackui/src/Foundation/Http/Controllers/TallStackUiAssetsController.php:26
 * @route '/tallstackui/style/{file?}'
 */
export const style = (args?: { file?: string | number } | [file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: style.url(args, options),
    method: 'get',
})

style.definition = {
    methods: ["get","head"],
    url: '/tallstackui/style/{file?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \TallStackUi\Foundation\Http\Controllers\TallStackUiAssetsController::style
 * @see vendor/tallstackui/tallstackui/src/Foundation/Http/Controllers/TallStackUiAssetsController.php:26
 * @route '/tallstackui/style/{file?}'
 */
style.url = (args?: { file?: string | number } | [file: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { file: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    file: args[0],
                }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
            "file",
        ])

    const parsedArgs = {
                        file: args?.file,
                }

    return style.definition.url
            .replace('{file?}', parsedArgs.file?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \TallStackUi\Foundation\Http\Controllers\TallStackUiAssetsController::style
 * @see vendor/tallstackui/tallstackui/src/Foundation/Http/Controllers/TallStackUiAssetsController.php:26
 * @route '/tallstackui/style/{file?}'
 */
style.get = (args?: { file?: string | number } | [file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: style.url(args, options),
    method: 'get',
})
/**
* @see \TallStackUi\Foundation\Http\Controllers\TallStackUiAssetsController::style
 * @see vendor/tallstackui/tallstackui/src/Foundation/Http/Controllers/TallStackUiAssetsController.php:26
 * @route '/tallstackui/style/{file?}'
 */
style.head = (args?: { file?: string | number } | [file: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: style.url(args, options),
    method: 'head',
})
const tallstackui = {
    script: Object.assign(script, script),
style: Object.assign(style, style),
}

export default tallstackui