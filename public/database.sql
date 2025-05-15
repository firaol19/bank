create database bank;

use bank;

-- Creating Users Table
CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Manager', 'Employee', 'Customer') NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Creating Customers Table
CREATE TABLE Customers (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age INT CHECK (age >= 18),
    salary DECIMAL(10,2),
    address TEXT,
    department VARCHAR(100),
    phone_number VARCHAR(20),
    account_number VARCHAR(20) UNIQUE NOT NULL,
    balance DECIMAL(10,2) DEFAULT 0,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Creating Employees Table
CREATE TABLE Employees (
    employee_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50),
    department VARCHAR(100),
    salary DECIMAL(10,2),
    phone_number VARCHAR(20),
    hire_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Creating Accounts Table
CREATE TABLE Accounts (
    account_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    account_type ENUM('Savings', 'Loan') NOT NULL,
    balance DECIMAL(10,2) DEFAULT 0,
    status ENUM('Active', 'Closed') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES Customers(customer_id)
);

-- Creating Loans Table
CREATE TABLE Loans (
    loan_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    loan_amount DECIMAL(10,2) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    duration INT NOT NULL, -- Number of months
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    approved_by INT,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approval_date TIMESTAMP NULL,
    FOREIGN KEY (customer_id) REFERENCES Customers(customer_id),
    FOREIGN KEY (approved_by) REFERENCES Employees(employee_id)
);

-- Creating Transactions Table
CREATE TABLE Transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    transaction_type ENUM('Deposit', 'Withdrawal', 'Transfer') NOT NULL,
    amount DECIMAL(10,2) NOT NULL CHECK(amount > 0),
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_by INT NOT NULL,
    FOREIGN KEY (account_id) REFERENCES Accounts(account_id),
    FOREIGN KEY (processed_by) REFERENCES Employees(employee_id)
);

-- Creating Financial Statements Table
CREATE TABLE FinancialStatements (
    statement_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    account_id INT NOT NULL,
    balance DECIMAL(10,2) NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES Customers(customer_id),
    FOREIGN KEY (account_id) REFERENCES Accounts(account_id)
);

-- Creating Complaints Table
CREATE TABLE Complaints (
    complaint_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Resolved') DEFAULT 'Pending',
    resolved_by INT NULL,
    FOREIGN KEY (customer_id) REFERENCES Customers(customer_id),
    FOREIGN KEY (resolved_by) REFERENCES Employees(employee_id)
);

-- Creating Employee Information Upload Table
CREATE TABLE EmployeeUploads (
    upload_id INT PRIMARY KEY AUTO_INCREMENT,
    file_name VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_by INT NOT NULL,
    FOREIGN KEY (processed_by) REFERENCES Employees(employee_id)
);

-- Creating Reports Table
CREATE TABLE Reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    generated_by INT NOT NULL,
    report_type VARCHAR(50),
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES Employees(employee_id)
);

CREATE TABLE notifications (
  notification_id INT AUTO_INCREMENT PRIMARY KEY,
  message TEXT NOT NULL,
  target_role ENUM('Employee','Manager','Customer') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE loan_reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id INT NOT NULL,
    employee_id INT NOT NULL,
    review_remarks TEXT NOT NULL,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (loan_id) REFERENCES loans(loan_id),
    FOREIGN KEY (employee_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO Users (username, password, role, email, phone) VALUES
-- Managers
('manager_john', '12345', 'Manager', 'john.manager@example.com', '123-456-7890'),
('manager_anna', '12345', 'Manager', 'anna.manager@example.com', '321-654-0987'),

-- Employees
('employee_mike', '12345', 'Employee', 'mike.employee@example.com', '987-654-3210'),
('employee_sara', '12345', 'Employee', 'sara.employee@example.com', '654-321-9870'),

-- Customers
('customer_alex', '12345', 'Customer', 'alex.customer@example.com', '555-111-2222'),
('customer_lisa', '12345', 'Customer', 'lisa.customer@example.com', '444-333-2222');





ALTER TABLE users ADD COLUMN full_name VARCHAR(255) NOT NULL AFTER user_id;


CREATE TABLE settings (
  setting_key VARCHAR(255) NOT NULL,
  setting_value VARCHAR(255) NOT NULL,
  PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 
 
 CREATE TABLE tasks (
  task_id INT AUTO_INCREMENT PRIMARY KEY,
  task_description VARCHAR(255) NOT NULL,
  due_date DATE,
  status ENUM('Pending','Completed') DEFAULT 'Pending',
  assigned_to INT NOT NULL,
  CONSTRAINT fk_assigned_employee FOREIGN KEY (assigned_to) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SELECT * FROM accounts;
SELECT * FROM customers;
SELECT * FROM users;

ALTER TABLE loans MODIFY status ENUM('Pending','Approved','Rejected','Repaid') NOT NULL;

ALTER TABLE loans
ADD COLUMN unpaid_amount DECIMAL(10,2);


delete  from loans where customer_id = 17;

-- Use the bank database:
USE bank;

--------------------------------------------------
-- 1. Insert sample rows into the Users table
--------------------------------------------------


INSERT INTO users (full_name, username, password, role, email, phone)
VALUES
  ('Alice Johnson', 'alicej', 'hashedpassword1', 'Manager', 'alice.johnson@example.com', '555-0101'),
  ('Bob Smith', 'bobsmith', 'hashedpassword2', 'Employee', 'bob.smith@example.com', '555-0202'),
  ('Charlie Brown', 'charlieb', 'hashedpassword3', 'Customer', 'charlie.brown@example.com', '555-0303'),
  ('Diana Prince', 'dianap', 'hashedpassword4', 'Customer', 'diana.prince@example.com', '555-0404'),
  ('Edward Norton', 'edwardn', 'hashedpassword5', 'Employee', 'edward.norton@example.com', '555-0505');

--------------------------------------------------
-- 2. Insert sample rows into the Customers table
--------------------------------------------------
INSERT INTO Customers (name, age, salary, address, department, phone_number, account_number, balance)
VALUES
('Alex Johnson', 25, 2000.00, '123 Main St, Jigjiga', 'Accounting', '9876543210', 'CUST1001', 0.00),
('Lisa Brown', 30, 2500.00, '456 Elm St, Jigjiga', 'Marketing', '9876543211', 'CUST1002', 0.00),
('Samuel Green', 22, 1800.00, '789 Oak St, Jigjiga', 'Sales', '9876543212', 'CUST1003', 0.00),
('Nina White', 28, 2300.00, '321 Pine St, Jigjiga', 'HR', '9876543213', 'CUST1004', 0.00),
('David Black', 35, 3000.00, '654 Cedar St, Jigjiga', 'IT', '9876543214', 'CUST1005', 0.00);

--------------------------------------------------
-- 3. Insert sample rows into the Employees table
--------------------------------------------------
INSERT INTO Employees (name, position, department, salary, phone_number)
VALUES
('Mike Thompson', 'Cashier', 'Finance', 1500.00, '5551230001'),
('Sara Connor', 'Accountant', 'Finance', 1800.00, '5551230002'),
('John Doe', 'Manager', 'Operations', 3000.00, '5551230003'),
('Alice Stone', 'Clerk', 'Administration', 1200.00, '5551230004'),
('Robert King', 'Supervisor', 'Operations', 2200.00, '5551230005');

--------------------------------------------------
-- 4. Insert sample rows into the Accounts table
--------------------------------------------------
-- Assume customer IDs 1 to 5 exist from the Customers table.
INSERT INTO Accounts (customer_id, account_type, balance, status)
VALUES
(1, 'Savings', 1000.00, 'Active'),
(2, 'Savings', 1500.00, 'Active'),
(3, 'Loan',    0.00,    'Active'),
(4, 'Savings', 2000.00, 'Active'),
(5, 'Loan',    0.00,    'Active');

--------------------------------------------------
-- 5. Insert sample rows into the Loans table
--------------------------------------------------
-- For approved_by, we reference employee IDs (e.g., 3 or 4) when applicable.
INSERT INTO Loans (customer_id, loan_amount, interest_rate, duration, status, approved_by)
VALUES
(1, 5000.00, 10.00, 12, 'Pending', NULL),
(2, 7000.00,  9.50, 24, 'Approved', 3),
(3, 3000.00, 11.00,  6, 'Rejected', 4),
(4, 8000.00, 10.50, 18, 'Pending', NULL),
(5, 10000.00, 8.75, 36, 'Approved', 3);

--------------------------------------------------
-- 6. Insert sample rows into the Transactions table
--------------------------------------------------
-- Reference account_ids from the Accounts table and processed_by from Employees
INSERT INTO Transactions (account_id, transaction_type, amount, processed_by)
VALUES
(1, 'Deposit', 500.00, 1),
(1, 'Withdrawal', 200.00, 2),
(2, 'Deposit', 1000.00, 1),
(4, 'Deposit', 1500.00, 2),
(2, 'Withdrawal', 300.00, 3);

--------------------------------------------------
-- 7. Insert sample rows into the FinancialStatements table
--------------------------------------------------
INSERT INTO FinancialStatements (customer_id, account_id, balance)
VALUES
(1, 1, 1000.00),
(2, 2, 1500.00),
(3, 3, 0.00),
(4, 4, 2000.00),
(5, 5, 0.00);

--------------------------------------------------
-- 8. Insert sample rows into the Complaints table
--------------------------------------------------
-- Use NULL for unresolved complaints and set resolved_by for resolved ones
INSERT INTO Complaints (customer_id, message, status, resolved_by)
VALUES
(1, 'Issue with account login', 'Pending', NULL),
(2, 'Incorrect balance displayed', 'Resolved', 2),
(3, 'Transaction not processed', 'Pending', NULL),
(4, 'Unable to view statement', 'Pending', NULL),
(5, 'Card activation issue', 'Resolved', 1);

--------------------------------------------------
-- 9. Insert sample rows into the EmployeeUploads table
--------------------------------------------------
INSERT INTO EmployeeUploads (file_name, processed_by)
VALUES
('upload1.pdf', 1),
('upload2.jpg', 2),
('upload3.docx', 3),
('upload4.pdf', 1),
('upload5.png', 2);

--------------------------------------------------
-- 10. Insert sample rows into the Reports table
--------------------------------------------------
INSERT INTO Reports (generated_by, report_type)
VALUES
(1, 'Monthly Financial'),
(2, 'Transaction Summary'),
(3, 'Loan Overview'),
(4, 'Customer Feedback'),
(5, 'Operational Report');

--------------------------------------------------
-- 11. Insert sample rows into the notifications table
--------------------------------------------------
INSERT INTO notifications (message, target_role)
VALUES
('System update scheduled for tonight.', 'Employee'),
('New policy update available.', 'Manager'),
('Your account statement is ready.', 'Customer'),
('Maintenance downtime tomorrow.', 'Employee'),
('Annual performance review coming soon.', 'Manager');

--------------------------------------------------
-- 12. Insert sample rows into the loan_reviews table
--------------------------------------------------
-- Note: Here, employee_id references the Users table as defined in our FK constraint.
INSERT INTO loan_reviews (loan_id, employee_id, review_remarks)
VALUES
(1, 1, 'Reviewed and pending further documentation.'),
(2, 2, 'Approved after verification.'),
(3, 3, 'Rejected due to low income.'),
(4, 4, 'Pending additional review.'),
(5, 5, 'Approved with conditions.');

--------------------------------------------------
-- 13. Insert sample rows into the settings table
--------------------------------------------------
INSERT INTO settings (setting_key, setting_value)
VALUES
('site_name', 'Jigjiga University Bank System'),
('currency', 'ብር'),
('timezone', 'Africa/Addis_Ababa'),
('maintenance_mode', 'off'),
('support_email', 'support@jibank.com');

--------------------------------------------------
-- 14. Insert sample rows into the tasks table
--------------------------------------------------
INSERT INTO tasks (task_description, due_date, status, assigned_to)
VALUES
('Verify loan application for customer Alex', '2025-06-15', 'Pending', 1),
('Update customer information for Lisa', '2025-06-20', 'Completed', 2),
('Review monthly financial report', '2025-06-30', 'Pending', 3),
('Backup database', '2025-07-01', 'Pending', 4),
('Schedule system maintenance', '2025-07-05', 'Completed', 5);
