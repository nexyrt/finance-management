import DashboardController from './DashboardController'
import ClientController from './ClientController'
import ServiceController from './ServiceController'
import InvoiceController from './InvoiceController'
import PaymentController from './PaymentController'
import CashFlowExportController from './CashFlowExportController'
import TransactionCategoryController from './TransactionCategoryController'
import Auth from './Auth'
const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
ClientController: Object.assign(ClientController, ClientController),
ServiceController: Object.assign(ServiceController, ServiceController),
InvoiceController: Object.assign(InvoiceController, InvoiceController),
PaymentController: Object.assign(PaymentController, PaymentController),
CashFlowExportController: Object.assign(CashFlowExportController, CashFlowExportController),
TransactionCategoryController: Object.assign(TransactionCategoryController, TransactionCategoryController),
Auth: Object.assign(Auth, Auth),
}

export default Controllers