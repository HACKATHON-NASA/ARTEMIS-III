import LoginController from './LoginController'
import QuickAccessController from './QuickAccessController'
import RefreshController from './RefreshController'
import DeviceTokenController from './DeviceTokenController'
import LogoutController from './LogoutController'
import MeController from './MeController'
import ListUsersController from './ListUsersController'
import StoreUserController from './StoreUserController'
import UpdateUserController from './UpdateUserController'
import SetUserActiveController from './SetUserActiveController'
import ListDevicesController from './ListDevicesController'
import StoreDeviceController from './StoreDeviceController'
import SetDeviceActiveController from './SetDeviceActiveController'

const Api = {
    LoginController: Object.assign(LoginController, LoginController),
    QuickAccessController: Object.assign(QuickAccessController, QuickAccessController),
    RefreshController: Object.assign(RefreshController, RefreshController),
    DeviceTokenController: Object.assign(DeviceTokenController, DeviceTokenController),
    LogoutController: Object.assign(LogoutController, LogoutController),
    MeController: Object.assign(MeController, MeController),
    ListUsersController: Object.assign(ListUsersController, ListUsersController),
    StoreUserController: Object.assign(StoreUserController, StoreUserController),
    UpdateUserController: Object.assign(UpdateUserController, UpdateUserController),
    SetUserActiveController: Object.assign(SetUserActiveController, SetUserActiveController),
    ListDevicesController: Object.assign(ListDevicesController, ListDevicesController),
    StoreDeviceController: Object.assign(StoreDeviceController, StoreDeviceController),
    SetDeviceActiveController: Object.assign(SetDeviceActiveController, SetDeviceActiveController),
}

export default Api