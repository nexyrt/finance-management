import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import pdf81d01d from './pdf'
/**
 * @see routes/web.php:326
 * @route '/fund-requests/export/pdf'
 */
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/fund-requests/export/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:326
 * @route '/fund-requests/export/pdf'
 */
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:326
 * @route '/fund-requests/export/pdf'
 */
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:326
 * @route '/fund-requests/export/pdf'
 */
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})
const exportMethod = {
    pdf: Object.assign(pdf, pdf81d01d),
}

export default exportMethod