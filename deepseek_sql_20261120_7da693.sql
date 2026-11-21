CREATE DATABASE IF NOT EXISTS isp_billing CHARACTER SET utf8mb4;
USE isp_billing;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') DEFAULT 'staff',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE areas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE packages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  speed VARCHAR(50),
  price DECIMAL(10,2) NOT NULL,
  validity_days INT DEFAULT 30
);

CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) UNIQUE NOT NULL,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  address TEXT,
  area_id INT,
  package_id INT,
  status ENUM('active','inactive') DEFAULT 'active',
  join_date DATE,
  FOREIGN KEY (area_id) REFERENCES areas(id),
  FOREIGN KEY (package_id) REFERENCES packages(id)
);

CREATE TABLE invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  invoice_no VARCHAR(30) UNIQUE NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  month VARCHAR(7) NOT NULL,        -- যেমন 2026-11
  status ENUM('unpaid','paid','partial') DEFAULT 'unpaid',
  due_date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  method ENUM('cash','bkash','bank','other') DEFAULT 'cash',
  paid_at DATE,
  received_by INT,
  note VARCHAR(255),
  FOREIGN KEY (invoice_id) REFERENCES invoices(id),
  FOREIGN KEY (received_by) REFERENCES users(id)
);

-- ডিফল্ট অ্যাডমিন: admin@isp.local / password
INSERT INTO users (name,email,password,role) VALUES
('Admin','admin@isp.local','$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ18JuywdB9q0U7Gm0yQ0Q2W','admin');