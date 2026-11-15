import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
const SetUserActiveController = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: SetUserActiveController.url(args, options),
    method: 'patch',
})

SetUserActiveController.definition = {
    methods: ["patch"],
    url: '/api/v1/users/{user}/active',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
SetUserActiveController.url = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: args.user,
    }

    return SetUserActiveController.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
SetUserActiveController.patch = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: SetUserActiveController.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
const SetUserActiveControllerForm = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SetUserActiveController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
SetUserActiveControllerForm.patch = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SetUserActiveController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

SetUserActiveController.form = SetUserActiveControllerForm

export default SetUserActiveController