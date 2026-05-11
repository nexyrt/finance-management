import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Livewire\TestingPage::__invoke
 * @see app/Livewire/TestingPage.php:7
 * @route '/test'
 */
const TestingPage = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: TestingPage.url(options),
    method: 'get',
})

TestingPage.definition = {
    methods: ["get","head"],
    url: '/test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Livewire\TestingPage::__invoke
 * @see app/Livewire/TestingPage.php:7
 * @route '/test'
 */
TestingPage.url = (options?: RouteQueryOptions) => {
    return TestingPage.definition.url + queryParams(options)
}

/**
* @see \App\Livewire\TestingPage::__invoke
 * @see app/Livewire/TestingPage.php:7
 * @route '/test'
 */
TestingPage.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: TestingPage.url(options),
    method: 'get',
})
/**
* @see \App\Livewire\TestingPage::__invoke
 * @see app/Livewire/TestingPage.php:7
 * @route '/test'
 */
TestingPage.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: TestingPage.url(options),
    method: 'head',
})
export default TestingPage