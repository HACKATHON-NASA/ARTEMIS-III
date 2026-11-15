import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../wayfinder'
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
export const acceso = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: acceso.url(options),
    method: 'get',
})

acceso.definition = {
    methods: ["get","head"],
    url: '/acceso',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
acceso.url = (options?: RouteQueryOptions) => {
    return acceso.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
acceso.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: acceso.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
acceso.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: acceso.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
const accesoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: acceso.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
accesoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: acceso.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
accesoForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: acceso.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

acceso.form = accesoForm

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
export const admin = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

admin.definition = {
    methods: ["get","head","post","put","patch","delete","options","query"],
    url: '/admin',
} satisfies RouteDefinition<["get","head","post","put","patch","delete","options","query"]>

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
admin.url = (options?: RouteQueryOptions) => {
    return admin.definition.url + queryParams(options)
}

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
admin.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
admin.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: admin.url(options),
    method: 'head',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
admin.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: admin.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
admin.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: admin.url(options),
    method: 'put',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
admin.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: admin.url(options),
    method: 'patch',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
admin.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: admin.url(options),
    method: 'delete',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
admin.options = (options?: RouteQueryOptions): RouteDefinition<'options'> => ({
    url: admin.url(options),
    method: 'options',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
admin.query = (options?: RouteQueryOptions): RouteDefinition<'query'> => ({
    url: admin.url(options),
    method: 'query',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
const adminForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
adminForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
adminForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
adminForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: admin.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
adminForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: admin.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
adminForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: admin.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
adminForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: admin.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
adminForm.options = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: admin.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'OPTIONS',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/admin'
*/
adminForm.query = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: admin.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'QUERY',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

admin.form = adminForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
export const usuarios = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: usuarios.url(options),
    method: 'get',
})

usuarios.definition = {
    methods: ["get","head"],
    url: '/admin/usuarios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
usuarios.url = (options?: RouteQueryOptions) => {
    return usuarios.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
usuarios.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: usuarios.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
usuarios.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: usuarios.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
const usuariosForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: usuarios.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
usuariosForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: usuarios.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
usuariosForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: usuarios.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

usuarios.form = usuariosForm

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head","post","put","patch","delete","options","query"],
    url: '/',
} satisfies RouteDefinition<["get","head","post","put","patch","delete","options","query"]>

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
home.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: home.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
home.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: home.url(options),
    method: 'put',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
home.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: home.url(options),
    method: 'patch',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
home.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: home.url(options),
    method: 'delete',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
home.options = (options?: RouteQueryOptions): RouteDefinition<'options'> => ({
    url: home.url(options),
    method: 'options',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
home.query = (options?: RouteQueryOptions): RouteDefinition<'query'> => ({
    url: home.url(options),
    method: 'query',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
const homeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
homeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
homeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
homeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: home.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
homeForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: home.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
homeForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: home.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
homeForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: home.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
homeForm.options = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'OPTIONS',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/'
*/
homeForm.query = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: home.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'QUERY',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

home.form = homeForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
export const inicio = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: inicio.url(options),
    method: 'get',
})

inicio.definition = {
    methods: ["get","head"],
    url: '/inicio',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
inicio.url = (options?: RouteQueryOptions) => {
    return inicio.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
inicio.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: inicio.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
inicio.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: inicio.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
const inicioForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: inicio.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
inicioForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: inicio.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
inicioForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: inicio.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

inicio.form = inicioForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
export const salud = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: salud.url(options),
    method: 'get',
})

salud.definition = {
    methods: ["get","head"],
    url: '/salud',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
salud.url = (options?: RouteQueryOptions) => {
    return salud.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
salud.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: salud.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
salud.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: salud.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
const saludForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: salud.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
saludForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: salud.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
saludForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: salud.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

salud.form = saludForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
export const tripulacion = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tripulacion.url(options),
    method: 'get',
})

tripulacion.definition = {
    methods: ["get","head"],
    url: '/tripulacion',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
tripulacion.url = (options?: RouteQueryOptions) => {
    return tripulacion.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
tripulacion.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tripulacion.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
tripulacion.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tripulacion.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
const tripulacionForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tripulacion.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
tripulacionForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tripulacion.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
tripulacionForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tripulacion.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

tripulacion.form = tripulacionForm

/**
* @see routes/web.php:29
* @route '/tripulacion/{astronaut}'
*/
export const tripulante = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tripulante.url(args, options),
    method: 'get',
})

tripulante.definition = {
    methods: ["get","head"],
    url: '/tripulacion/{astronaut}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:29
* @route '/tripulacion/{astronaut}'
*/
tripulante.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return tripulante.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:29
* @route '/tripulacion/{astronaut}'
*/
tripulante.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tripulante.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:29
* @route '/tripulacion/{astronaut}'
*/
tripulante.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tripulante.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:29
* @route '/tripulacion/{astronaut}'
*/
const tripulanteForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tripulante.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:29
* @route '/tripulacion/{astronaut}'
*/
tripulanteForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tripulante.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:29
* @route '/tripulacion/{astronaut}'
*/
tripulanteForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tripulante.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

tripulante.form = tripulanteForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
export const misiones = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: misiones.url(options),
    method: 'get',
})

misiones.definition = {
    methods: ["get","head"],
    url: '/admin/misiones',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
misiones.url = (options?: RouteQueryOptions) => {
    return misiones.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
misiones.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: misiones.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
misiones.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: misiones.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
const misionesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: misiones.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
misionesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: misiones.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
misionesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: misiones.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

misiones.form = misionesForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
export const registrar = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: registrar.url(options),
    method: 'get',
})

registrar.definition = {
    methods: ["get","head"],
    url: '/registrar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
registrar.url = (options?: RouteQueryOptions) => {
    return registrar.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
registrar.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: registrar.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
registrar.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: registrar.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
const registrarForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: registrar.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
registrarForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: registrar.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
registrarForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: registrar.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

registrar.form = registrarForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
export const guia = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: guia.url(options),
    method: 'get',
})

guia.definition = {
    methods: ["get","head"],
    url: '/guia',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
guia.url = (options?: RouteQueryOptions) => {
    return guia.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
guia.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: guia.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
guia.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: guia.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
const guiaForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: guia.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
guiaForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: guia.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
guiaForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: guia.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

guia.form = guiaForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
export const psicologia = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: psicologia.url(options),
    method: 'get',
})

psicologia.definition = {
    methods: ["get","head"],
    url: '/guia/psicologica',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
psicologia.url = (options?: RouteQueryOptions) => {
    return psicologia.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
psicologia.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: psicologia.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
psicologia.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: psicologia.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
const psicologiaForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: psicologia.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
psicologiaForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: psicologia.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
psicologiaForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: psicologia.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

psicologia.form = psicologiaForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
export const psicologiaTripulacion = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: psicologiaTripulacion.url(options),
    method: 'get',
})

psicologiaTripulacion.definition = {
    methods: ["get","head"],
    url: '/guia/resultados',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
psicologiaTripulacion.url = (options?: RouteQueryOptions) => {
    return psicologiaTripulacion.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
psicologiaTripulacion.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: psicologiaTripulacion.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
psicologiaTripulacion.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: psicologiaTripulacion.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
const psicologiaTripulacionForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: psicologiaTripulacion.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
psicologiaTripulacionForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: psicologiaTripulacion.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
psicologiaTripulacionForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: psicologiaTripulacion.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

psicologiaTripulacion.form = psicologiaTripulacionForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
export const evolucion = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: evolucion.url(options),
    method: 'get',
})

evolucion.definition = {
    methods: ["get","head"],
    url: '/evolucion',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
evolucion.url = (options?: RouteQueryOptions) => {
    return evolucion.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
evolucion.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: evolucion.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
evolucion.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: evolucion.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
const evolucionForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: evolucion.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
evolucionForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: evolucion.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
evolucionForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: evolucion.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

evolucion.form = evolucionForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
export const logros = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logros.url(options),
    method: 'get',
})

logros.definition = {
    methods: ["get","head"],
    url: '/logros',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
logros.url = (options?: RouteQueryOptions) => {
    return logros.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
logros.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logros.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
logros.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logros.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
const logrosForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logros.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
logrosForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logros.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
logrosForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logros.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

logros.form = logrosForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
export const mision = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mision.url(options),
    method: 'get',
})

mision.definition = {
    methods: ["get","head"],
    url: '/mision',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
mision.url = (options?: RouteQueryOptions) => {
    return mision.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
mision.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mision.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
mision.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mision.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
const misionForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mision.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
misionForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mision.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
misionForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mision.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

mision.form = misionForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
export const indicaciones = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indicaciones.url(options),
    method: 'get',
})

indicaciones.definition = {
    methods: ["get","head"],
    url: '/indicaciones',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
indicaciones.url = (options?: RouteQueryOptions) => {
    return indicaciones.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
indicaciones.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indicaciones.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
indicaciones.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indicaciones.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
const indicacionesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indicaciones.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
indicacionesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indicaciones.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
indicacionesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indicaciones.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indicaciones.form = indicacionesForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
export const alertas = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: alertas.url(options),
    method: 'get',
})

alertas.definition = {
    methods: ["get","head"],
    url: '/alertas',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
alertas.url = (options?: RouteQueryOptions) => {
    return alertas.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
alertas.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: alertas.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
alertas.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: alertas.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
const alertasForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: alertas.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
alertasForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: alertas.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
alertasForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: alertas.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

alertas.form = alertasForm
