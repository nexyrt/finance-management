# Invoicing System Database Schema

This document explains the database schema for our invoicing system, which is designed to handle billing for both individuals and companies, track payments (including installments), and monitor financial transactions.

## Overview

The system is designed to solve the following key requirements:
- Bill individuals or companies directly
- Bill individual owners for services provided to multiple companies they own
- Track installment payments
- Monitor financial transactions and bank account balances

## Tables and Relationships

### 1. Clients Table
Stores information about all clients, whether they are individuals or companies.

**Fields:**
- `id` - Primary key
- `name` - Name of the individual or company
- `type` - Either 'individual' or 'company'
- `email` - Contact email
- `phone` - Contact phone number
- `address` - Physical address
- `tax_id` - Tax identification (for companies)
- `created_at` / `updated_at` - Timestamps

### 2. Client_Relationships Table
Establishes which individuals own which companies.

**Fields:**
- `id` - Primary key
- `owner_id` - Foreign key to Clients table (where type = 'individual')
- `company_id` - Foreign key to Clients table (where type = 'company')
- `created_at` / `updated_at` - Timestamps

### 3. Services Table
Lists all services that your business offers.

**Fields:**
- `id` - Primary key
- `name` - Name of the service
- `price` - Standard price for the service
- `created_at` / `updated_at` - Timestamps

### 4. Service_Clients Table
Records services provided to specific clients.

**Fields:**
- `id` - Primary key
- `service_id` - Foreign key to Services table
- `client_id` - Foreign key to Clients table
- `service_date` - Date the service was provided
- `amount` - The amount charged for this specific service instance
- `created_at` / `updated_at` - Timestamps

### 5. Bank_Accounts Table
Tracks your company's bank accounts.

**Fields:**
- `id` - Primary key
- `account_name` - Name of the account
- `account_number` - Bank account number
- `bank_name` - Name of the bank
- `branch` - Branch name/number
- `currency` - Currency code
- `initial_balance` - Starting balance
- `current_balance` - Current balance
- `created_at` / `updated_at` - Timestamps

### 6. Invoices Table
Stores invoice information.

**Fields:**
- `id` - Primary key
- `invoice_number` - Unique invoice identifier
- `billed_to_id` - Foreign key to Clients table (who receives the invoice)
- `total_amount` - Total invoice amount
- `issue_date` - When the invoice was issued
- `due_date` - When payment is due
- `status` - Status of the invoice (draft, sent, paid, partially_paid, overdue)
- `payment_terms` - Whether full payment or installment
- `installment_count` - Number of installments (if applicable)
- `created_at` / `updated_at` - Timestamps

### 7. Invoice_Items Table
Contains line items for each invoice.

**Fields:**
- `id` - Primary key
- `invoice_id` - Foreign key to Invoices table
- `service_client_id` - Foreign key to Service_Clients table
- `amount` - Amount for this line item
- `created_at` / `updated_at` - Timestamps

### 8. Payments Table
Records payments received against invoices.

**Fields:**
- `id` - Primary key
- `invoice_id` - Foreign key to Invoices table
- `bank_account_id` - Foreign key to Bank_Accounts table
- `amount` - Payment amount
- `payment_date` - Date payment was received
- `payment_method` - Method of payment (cash, bank_transfer, etc.)
- `reference_number` - Payment reference/transaction ID
- `installment_number` - Which installment this payment applies to
- `created_at` / `updated_at` - Timestamps

### 9. Bank_Transactions Table
Records all bank account transactions.

**Fields:**
- `id` - Primary key
- `bank_account_id` - Foreign key to Bank_Accounts table
- `amount` - Transaction amount
- `transaction_date` - Date of transaction
- `transaction_type` - Type (deposit, withdrawal, transfer, fee, interest)
- `description` - Transaction description
- `reference_number` - Transaction reference
- `created_at` / `updated_at` - Timestamps

## Common Scenarios

### 1. Billing a Company Directly

1. Check if the company exists in the Clients table
2. Record the service in Service_Clients table
3. Create an invoice in the Invoices table with billed_to_id pointing to the company
4. Add invoice items in Invoice_Items table

### 2. Billing an Individual for Multiple Companies They Own

1. Check if the individual exists in the Clients table
2. Find all companies owned by the individual using Client_Relationships table
3. Find all services provided to those companies
4. Create one invoice in the Invoices table with billed_to_id pointing to the individual
5. Add all service items to the invoice in Invoice_Items table

### 3. Recording a Full Payment

1. Insert a record in the Payments table
2. Update the invoice status in the Invoices table
3. Update the bank account balance in the Bank_Accounts table
4. Record the transaction in Bank_Transactions table

### 4. Recording an Installment Payment

1. Insert a record in the Payments table with the appropriate installment_number
2. Update the invoice status in the Invoices table (partially_paid or paid if final installment)
3. Update the bank account balance in the Bank_Accounts table
4. Record the transaction in Bank_Transactions table

## Query Examples

### Find all companies owned by an individual
```sql
SELECT c.* 
FROM clients c
JOIN client_relationships cr ON c.id = cr.company_id
WHERE cr.owner_id = [individual_id];
```

### Create a consolidated invoice for an individual owner
```sql
-- First, find all unbilled services for companies owned by this individual
SELECT sc.* 
FROM service_clients sc
JOIN clients c ON sc.client_id = c.id
JOIN client_relationships cr ON c.id = cr.company_id
WHERE cr.owner_id = [individual_id]
AND sc.id NOT IN (SELECT service_client_id FROM invoice_items);

-- Then create the invoice and add these services as line items
```

### Check installment payment status
```sql
SELECT 
    i.invoice_number,
    i.total_amount,
    i.installment_count,
    COUNT(p.id) AS installments_paid,
    SUM(p.amount) AS amount_paid,
    (i.total_amount - SUM(p.amount)) AS amount_remaining
FROM invoices i
LEFT JOIN payments p ON i.id = p.invoice_id
WHERE i.id = [invoice_id]
GROUP BY i.id;
```

## Maintenance Notes

- When creating invoices for companies with individual owners, check if they prefer consolidated billing
- Remember to update bank_account.current_balance whenever recording a payment
- Always create corresponding records in bank_transactions table when recording payments
- Regularly reconcile bank_accounts.current_balance with actual bank statements