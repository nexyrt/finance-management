import Profile from './Profile'
import Password from './Password'
import CompanyProfileSettings from './CompanyProfileSettings'
const Settings = {
    Profile: Object.assign(Profile, Profile),
Password: Object.assign(Password, Password),
CompanyProfileSettings: Object.assign(CompanyProfileSettings, CompanyProfileSettings),
}

export default Settings