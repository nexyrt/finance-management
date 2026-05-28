import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CashFlowExportController::exportPdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/bank-account/export/pdf'
 */
const exportPdf5c3245070552335381f3c23fab34af06 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportPdf5c3245070552335381f3c23fab34af06.url(options),
    method: 'get',
})

exportPdf5c3245070552335381f3c23fab34af06.definition = {
    methods: ["get","head"],
    url: '/bank-account/export/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CashFlowExportController::exportPdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/bank-account/export/pdf'
 */
exportPdf5c3245070552335381f3c23fab34af06.url = (options?: RouteQueryOptions) => {
    return exportPdf5c3245070552335381f3c23fab34af06.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowExportController::exportPdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/bank-account/export/pdf'
 */
exportPdf5c3245070552335381f3c23fab34af06.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportPdf5c3245070552335381f3c23fab34af06.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CashFlowExportController::exportPdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/bank-account/export/pdf'
 */
exportPdf5c3245070552335381f3c23fab34af06.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportPdf5c3245070552335381f3c23fab34af06.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CashFlowExportController::exportPdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/cash-flow/export/pdf'
 */
const exportPdfc0e77c6c9234ceadf550c4a94b783a03 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportPdfc0e77c6c9234ceadf550c4a94b783a03.url(options),
    method: 'get',
})

exportPdfc0e77c6c9234ceadf550c4a94b783a03.definition = {
    methods: ["get","head"],
    url: '/cash-flow/export/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CashFlowExportController::exportPdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/cash-flow/export/pdf'
 */
exportPdfc0e77c6c9234ceadf550c4a94b783a03.url = (options?: RouteQueryOptions) => {
    return exportPdfc0e77c6c9234ceadf550c4a94b783a03.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowExportController::exportPdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/cash-flow/export/pdf'
 */
exportPdfc0e77c6c9234ceadf550c4a94b783a03.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportPdfc0e77c6c9234ceadf550c4a94b783a03.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CashFlowExportController::exportPdf
 * @see app/Http/Controllers/CashFlowExportController.php:17
 * @route '/cash-flow/export/pdf'
 */
exportPdfc0e77c6c9234ceadf550c4a94b783a03.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportPdfc0e77c6c9234ceadf550c4a94b783a03.url(options),
    method: 'head',
})

/**
* Multiple routes resolve to \App\Http\Controllers\CashFlowExportController::exportPdf, so this export is a
* dictionary keyed by URI rather than a callable. Call a specific route with `exportPdf['<uri>'](...)`,
* or import the route by name from your generated `routes/` directory.
*/
export const exportPdf = {
    '/bank-account/export/pdf': exportPdf5c3245070552335381f3c23fab34af06,
    '/cash-flow/export/pdf': exportPdfc0e77c6c9234ceadf550c4a94b783a03,
}

/**
* @see \App\Http\Controllers\CashFlowExportController::previewPdf
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/bank-account/export/pdf/preview'
 */
const previewPdfd6236da67381a26ae3e1ccb60107fb18 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: previewPdfd6236da67381a26ae3e1ccb60107fb18.url(options),
    method: 'get',
})

previewPdfd6236da67381a26ae3e1ccb60107fb18.definition = {
    methods: ["get","head"],
    url: '/bank-account/export/pdf/preview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CashFlowExportController::previewPdf
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/bank-account/export/pdf/preview'
 */
previewPdfd6236da67381a26ae3e1ccb60107fb18.url = (options?: RouteQueryOptions) => {
    return previewPdfd6236da67381a26ae3e1ccb60107fb18.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowExportController::previewPdf
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/bank-account/export/pdf/preview'
 */
previewPdfd6236da67381a26ae3e1ccb60107fb18.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: previewPdfd6236da67381a26ae3e1ccb60107fb18.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CashFlowExportController::previewPdf
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/bank-account/export/pdf/preview'
 */
previewPdfd6236da67381a26ae3e1ccb60107fb18.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: previewPdfd6236da67381a26ae3e1ccb60107fb18.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CashFlowExportController::previewPdf
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/cash-flow/export/pdf/preview'
 */
const previewPdf30b141321f62b9f10d739606bd67f5d9 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: previewPdf30b141321f62b9f10d739606bd67f5d9.url(options),
    method: 'get',
})

previewPdf30b141321f62b9f10d739606bd67f5d9.definition = {
    methods: ["get","head"],
    url: '/cash-flow/export/pdf/preview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CashFlowExportController::previewPdf
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/cash-flow/export/pdf/preview'
 */
previewPdf30b141321f62b9f10d739606bd67f5d9.url = (options?: RouteQueryOptions) => {
    return previewPdf30b141321f62b9f10d739606bd67f5d9.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CashFlowExportController::previewPdf
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/cash-flow/export/pdf/preview'
 */
previewPdf30b141321f62b9f10d739606bd67f5d9.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: previewPdf30b141321f62b9f10d739606bd67f5d9.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CashFlowExportController::previewPdf
 * @see app/Http/Controllers/CashFlowExportController.php:56
 * @route '/cash-flow/export/pdf/preview'
 */
previewPdf30b141321f62b9f10d739606bd67f5d9.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: previewPdf30b141321f62b9f10d739606bd67f5d9.url(options),
    method: 'head',
})

/**
* Multiple routes resolve to \App\Http\Controllers\CashFlowExportController::previewPdf, so this export is a
* dictionary keyed by URI rather than a callable. Call a specific route with `previewPdf['<uri>'](...)`,
* or import the route by name from your generated `routes/` directory.
*/
export const previewPdf = {
    '/bank-account/export/pdf/preview': previewPdfd6236da67381a26ae3e1ccb60107fb18,
    '/cash-flow/export/pdf/preview': previewPdf30b141321f62b9f10d739606bd67f5d9,
}

const CashFlowExportController = { exportPdf, previewPdf }

export default CashFlowExportController