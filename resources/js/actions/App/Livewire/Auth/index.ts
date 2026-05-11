import Login from './Login'
import Register from './Register'
import ForgotPassword from './ForgotPassword'
import ResetPassword from './ResetPassword'
import VerifyEmail from './VerifyEmail'
import ConfirmPassword from './ConfirmPassword'
const Auth = {
    Login: Object.assign(Login, Login),
Register: Object.assign(Register, Register),
ForgotPassword: Object.assign(ForgotPassword, ForgotPassword),
ResetPassword: Object.assign(ResetPassword, ResetPassword),
VerifyEmail: Object.assign(VerifyEmail, VerifyEmail),
ConfirmPassword: Object.assign(ConfirmPassword, ConfirmPassword),
}

export default Auth