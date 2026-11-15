import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ShowAssessmentController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ShowAssessmentController.php:17
* @route '/api/v1/astronauts/{astronaut}/assessment'
*/
const ShowAssessmentController = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ShowAssessmentController.url(args, options),
    method: 'get',
})

ShowAssessmentController.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/assessment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ShowAssessmentController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ShowAssessmentController.php:17
* @route '/api/v1/astronauts/{astronaut}/assessment'
*/
ShowAssessmentController.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { astronaut: args }
    }

    if (Array.isArray(args)) {
        args = {
            astronaut: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        astronaut: args.astronaut,
    }

    return ShowAssessmentController.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ShowAssessmentController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ShowAssessmentController.php:17
* @route '/api/v1/astronauts/{astronaut}/assessment'
*/
ShowAssessmentController.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ShowAssessmentController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ShowAssessmentController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ShowAssessmentController.php:17
* @route '/api/v1/astronauts/{astronaut}/assessment'
*/
ShowAssessmentController.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ShowAssessmentController.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ShowAssessmentController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ShowAssessmentController.php:17
* @route '/api/v1/astronauts/{astronaut}/assessment'
*/
const ShowAssessmentControllerForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ShowAssessmentController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ShowAssessmentController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ShowAssessmentController.php:17
* @route '/api/v1/astronauts/{astronaut}/assessment'
*/
ShowAssessmentControllerForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ShowAssessmentController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ShowAssessmentController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ShowAssessmentController.php:17
* @route '/api/v1/astronauts/{astronaut}/assessment'
*/
ShowAssessmentControllerForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ShowAssessmentController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ShowAssessmentController.form = ShowAssessmentControllerForm

export default ShowAssessmentController