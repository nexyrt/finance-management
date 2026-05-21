import permissions from './permissions'
import roles from './roles'
import users from './users'
const admin = {
    permissions: Object.assign(permissions, permissions),
roles: Object.assign(roles, roles),
users: Object.assign(users, users),
}

export default admin