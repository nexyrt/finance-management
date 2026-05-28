import PermissionController from './PermissionController'
import RoleController from './RoleController'
import UserController from './UserController'
const Admin = {
    PermissionController: Object.assign(PermissionController, PermissionController),
RoleController: Object.assign(RoleController, RoleController),
UserController: Object.assign(UserController, UserController),
}

export default Admin