import DashboardController from './DashboardController'
import ClientController from './ClientController'
import ServiceController from './ServiceController'
import InvoiceController from './InvoiceController'
import PaymentController from './PaymentController'
import RecurringInvoiceController from './RecurringInvoiceController'
import BankAccountController from './BankAccountController'
import BankTransactionController from './BankTransactionController'
import CashFlowExportController from './CashFlowExportController'
import CashFlowController from './CashFlowController'
import TransactionCategoryController from './TransactionCategoryController'
import ReimbursementController from './ReimbursementController'
import FundRequestController from './FundRequestController'
import Auth from './Auth'
const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
ClientController: Object.assign(ClientController, ClientController),
ServiceController: Object.assign(ServiceController, ServiceController),
InvoiceController: Object.assign(InvoiceController, InvoiceController),
PaymentController: Object.assign(PaymentController, PaymentController),
RecurringInvoiceController: Object.assign(RecurringInvoiceController, RecurringInvoiceController),
BankAccountController: Object.assign(BankAccountController, BankAccountController),
BankTransactionController: Object.assign(BankTransactionController, BankTransactionController),
CashFlowExportController: Object.assign(CashFlowExportController, CashFlowExportController),
CashFlowController: Object.assign(CashFlowController, CashFlowController),
TransactionCategoryController: Object.assign(TransactionCategoryController, TransactionCategoryController),
ReimbursementController: Object.assign(ReimbursementController, ReimbursementController),
FundRequestController: Object.assign(FundRequestController, FundRequestController),
Auth: Object.assign(Auth, Auth),
}

export default Controllers