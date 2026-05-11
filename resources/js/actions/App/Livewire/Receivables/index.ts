import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Livewire\Receivables\Index::__invoke
 * @see app/Livewire/Receivables/Index.php:7
 * @route '/receivables'
 */
const Index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})

Index.definition = {
    methods: ["get","head"],
    url: '/receivables',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\Receivables\Index::__invoke
 * @see app/Livewire/Receivables/Index.php:7
 * @route '/receivables'
 */
Index.url = (options?: RouteQueryOptions) => {
    return Index.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\Receivables\Index::__invoke
 * @see app/Livewire/Receivables/Index.php:7
 * @route '/receivables'
 */
Index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Index.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\Receivables\Index::__invoke
 * @see app/Livewire/Receivables/Index.php:7
 * @route '/receivables'
 */
Index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Index.url(options),
    method: 'head',
})
export default Index