import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:20
* @route '/api/v1/psych-results'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/psych-results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:20
* @route '/api/v1/psych-results'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:20
* @route '/api/v1/psych-results'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:20
* @route '/api/v1/psych-results'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:20
* @route '/api/v1/psych-results'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:20
* @route '/api/v1/psych-results'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::index
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:20
* @route '/api/v1/psych-results'
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
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:30
* @route '/api/v1/psych-results'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/psych-results',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:30
* @route '/api/v1/psych-results'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:30
* @route '/api/v1/psych-results'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:30
* @route '/api/v1/psych-results'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController::store
* @see app/Modules/Evaluacion/Http/Controllers/Api/PsychResultController.php:30
* @route '/api/v1/psych-results'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const PsychResultController = { index, store }

export default PsychResultController