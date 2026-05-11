import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
 * @see routes/web.php:47
 * @route '/api/transaction-categories'
 */
export const transactionCategories = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transactionCategories.url(options),
    method: 'get',
})

transactionCategories.definition = {
    methods: ["get","head"],
    url: '/api/transaction-categories',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:47
 * @route '/api/transaction-categories'
 */
transactionCategories.url = (options?: RouteQueryOptions) => {
    return transactionCategories.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:47
 * @route '/api/transaction-categories'
 */
transactionCategories.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transactionCategories.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:47
 * @route '/api/transaction-categories'
 */
transactionCategories.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: transactionCategories.url(options),
    method: 'head',
})

/**
 * @see routes/web.php:78
 * @route '/api/bank-accounts'
 */
export const bankAccounts = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: bankAccounts.url(options),
    method: 'get',
})

bankAccounts.definition = {
    methods: ["get","head"],
    url: '/api/bank-accounts',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:78
 * @route '/api/bank-accounts'
 */
bankAccounts.url = (options?: RouteQueryOptions) => {
    return bankAccounts.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:78
 * @route '/api/bank-accounts'
 */
bankAccounts.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: bankAccounts.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:78
 * @route '/api/bank-accounts'
 */
bankAccounts.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: bankAccounts.url(options),
    method: 'head',
})

/**
 * @see routes/web.php:88
 * @route '/api/clients'
 */
export const clients = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: clients.url(options),
    method: 'get',
})

clients.definition = {
    methods: ["get","head"],
    url: '/api/clients',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:88
 * @route '/api/clients'
 */
clients.url = (options?: RouteQueryOptions) => {
    return clients.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:88
 * @route '/api/clients'
 */
clients.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: clients.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:88
 * @route '/api/clients'
 */
clients.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: clients.url(options),
    method: 'head',
})
const api = {
    transactionCategories: Object.assign(transactionCategories, transactionCategories),
bankAccounts: Object.assign(bankAccounts, bankAccounts),
clients: Object.assign(clients, clients),
}

export default api