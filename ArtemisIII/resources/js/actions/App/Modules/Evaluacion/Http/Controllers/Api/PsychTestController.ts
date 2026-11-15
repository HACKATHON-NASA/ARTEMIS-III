import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:20
* @route '/api/v1/psych-tests'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/psych-tests',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:20
* @route '/api/v1/psych-tests'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:20
* @route '/api/v1/psych-tests'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:20
* @route '/api/v1/psych-tests'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:20
* @route '/api/v1/psych-tests'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:20
* @route '/api/v1/psych-tests'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:20
* @route '/api/v1/psych-tests'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:25
* @route '/api/v1/psych-tests'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/psych-tests',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:25
* @route '/api/v1/psych-tests'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:25
* @route '/api/v1/psych-tests'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:25
* @route '/api/v1/psych-tests'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:25
* @route '/api/v1/psych-tests'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::update
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:30
* @route '/api/v1/psych-tests/{test}'
*/
export const update = (args: { test: string | number } | [test: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/v1/psych-tests/{test}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::update
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:30
* @route '/api/v1/psych-tests/{test}'
*/
update.url = (args: { test: string | number } | [test: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { test: args }
    }

    if (Array.isArray(args)) {
        args = {
            test: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        test: args.test,
    }

    return update.definition.url
            .replace('{test}', parsedArgs.test.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::update
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:30
* @route '/api/v1/psych-tests/{test}'
*/
update.put = (args: { test: string | number } | [test: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::update
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:30
* @route '/api/v1/psych-tests/{test}'
*/
const updateForm = (args: { test: string | number } | [test: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController::update
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychTestController.php:30
* @route '/api/v1/psych-tests/{test}'
*/
updateForm.put = (args: { test: string | number } | [test: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const PsychTestController = { index, store, update }

export default PsychTestController