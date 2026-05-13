import Accounts from './Accounts'
import CashFlow from './CashFlow'
import Reimbursements from './Reimbursements'
import FundRequests from './FundRequests'
import Feedbacks from './Feedbacks'
import Loans from './Loans'
import Receivables from './Receivables'
import Permissions from './Permissions'
import Users from './Users'
import Settings from './Settings'
import TestingPage from './TestingPage'
const Livewire = {
    Accounts: Object.assign(Accounts, Accounts),
CashFlow: Object.assign(CashFlow, CashFlow),
Reimbursements: Object.assign(Reimbursements, Reimbursements),
FundRequests: Object.assign(FundRequests, FundRequests),
Feedbacks: Object.assign(Feedbacks, Feedbacks),
Loans: Object.assign(Loans, Loans),
Receivables: Object.assign(Receivables, Receivables),
Permissions: Object.assign(Permissions, Permissions),
Users: Object.assign(Users, Users),
Settings: Object.assign(Settings, Settings),
TestingPage: Object.assign(TestingPage, TestingPage),
}

export default Livewire