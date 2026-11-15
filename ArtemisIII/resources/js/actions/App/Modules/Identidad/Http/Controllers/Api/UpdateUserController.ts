import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
const UpdateUserController = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: UpdateUserController.url(args, options),
    method: 'patch',
})

UpdateUserController.definition = {
    methods: ["patch"],
    url: '/api/v1/users/{user}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
UpdateUserController.url = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return UpdateUserController.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
UpdateUserController.patch = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: UpdateUserController.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
const UpdateUserControllerForm = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: UpdateUserController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
UpdateUserControllerForm.patch = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: UpdateUserController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

UpdateUserController.form = UpdateUserControllerForm

export default UpdateUserController