import ProfileController from './ProfileController'
import PasswordController from './PasswordController'
import CompanyController from './CompanyController'
const Settings = {
    ProfileController: Object.assign(ProfileController, ProfileController),
PasswordController: Object.assign(PasswordController, PasswordController),
CompanyController: Object.assign(CompanyController, CompanyController),
}

export default Settings