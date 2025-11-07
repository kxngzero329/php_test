-- Database Creation + Selection
-- -----------------------------------------------------------------------------------------------------------

CREATE SCHEMA IF NOT EXISTS tracker_db;
USE tracker_db;

-- -----------------------------------------------------------------------------------------------------------
-- Table Creations:
-- -----------------------------------------------------------------------------------------------------------

CREATE TABLE employees (
    employee_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,  
    first_name VARCHAR(50) NOT NULL,                
    last_name VARCHAR(50) NOT NULL,                  
    contact_no VARCHAR(10),               
    email VARCHAR(100) UNIQUE NOT NULL,                
    date_hired DATE NOT NULL,                           
    supervisor_name VARCHAR(100),                    
    leave_balance DECIMAL(5,2) DEFAULT 0.00                 
);

CREATE TABLE `tracker_db`.`emp_classification` (
  `classification_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `department` VARCHAR(50) NOT NULL,
  `position` VARCHAR(50) NOT NULL,
  `role` ENUM('Admin', 'HR', 'Employee', 'Manager') NULL DEFAULT 'Employee', -- Used as display only, no functionality need to be implemented
  `employment_type` ENUM('Full-time', 'Part-time', 'Contract', 'Intern') NULL DEFAULT 'Full-time',
  `employee_level` ENUM('Junior', 'Mid', 'Senior', 'Lead', 'Manager', 'Executive') NULL DEFAULT 'Junior',
  
  PRIMARY KEY (`classification_id`));
  
CREATE TABLE `tracker_db`.`record_backups` (
  `record_id` INT NOT NULL AUTO_INCREMENT,
  `employee_id` INT UNSIGNED NOT NULL,
  `clockin_time` TIME NULL,
  `clockout_time` TIME NULL,
  `status` ENUM('Active','Inactive','Absent') NULL DEFAULT 'Inactive',
  `date` DATE NULL,
  PRIMARY KEY (`record_id`),
  UNIQUE INDEX `record_id_UNIQUE` (`record_id` ASC) VISIBLE);
  
CREATE TABLE `tracker_db`.`notifications_records` (
  `notification_id` INT NOT NULL AUTO_INCREMENT,
  `employee_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(45) NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `date_created` DATE NOT NULL,
  PRIMARY KEY (`notification_id`),
  UNIQUE INDEX `notification_id_UNIQUE` (`notification_id` ASC) VISIBLE);
  
  CREATE TABLE `tracker_db`.`qr_storage` (
  `qr_id` INT NOT NULL AUTO_INCREMENT,
  `employee_id` INT UNSIGNED NOT NULL,
  `token` VARCHAR(500) NOT NULL,
  `scan_url` VARCHAR(1000) NOT NULL,
  `qr_image_data` LONGTEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  PRIMARY KEY (`qr_id`),
  UNIQUE INDEX `qr_id_UNIQUE` (`qr_id` ASC) VISIBLE);
  
  CREATE TABLE `tracker_db`.`hours_management` (
  `hrs_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `week_start` DATE NOT NULL,
  `week_end` DATE NOT NULL,
  `expected_hours` INT UNSIGNED NOT NULL,
  `total_worked_hours` INT UNSIGNED NOT NULL,
  `hours_owed` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`hrs_id`));

ALTER TABLE `tracker_db`.`hours_management` 
ADD UNIQUE INDEX `hrs_id_UNIQUE` (`hrs_id` ASC) VISIBLE;
;

CREATE TABLE `tracker_db`.`account_auth` (
  `auth_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` INT UNSIGNED NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `failed_login_attempts` INT UNSIGNED NOT NULL DEFAULT 0,
  `lock_until` DATETIME NOT NULL,
  `reset_token_hash` VARBINARY(64) NOT NULL,
  `reset_expires` DATETIME NOT NULL,
  `created_at` TIMESTAMP NOT NULL,
  `backup_email` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`auth_id`),
  UNIQUE INDEX `auth_id_UNIQUE` (`auth_id` ASC) VISIBLE);


-- ----------------------------------------------------------------------------------------------------------- 
-- Table Alterations + Updates:
-- -----------------------------------------------------------------------------------------------------------
  
-- Employee Table:

ALTER TABLE `tracker_db`.`employees` 
ADD COLUMN `address` VARCHAR(255) NOT NULL AFTER `leave_balance`;
ALTER TABLE `tracker_db`.`employees` 
CHANGE COLUMN `address` `address` VARCHAR(255) NOT NULL AFTER `email`;

	
ALTER TABLE `tracker_db`.`employees` 
ADD COLUMN `is_admin` TINYINT NULL DEFAULT 0 AFTER `address`; -- Must be 0 OR 1 , 0 = False 1 = True

ALTER TABLE `tracker_db`.`employees` 
ADD COLUMN `id` VARCHAR(13) NOT NULL AFTER `address`,
ADD UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE;
;

-- record_backups Table:

ALTER TABLE `tracker_db`.`record_backups` 
ADD COLUMN `type` ENUM('Work', 'Tea', 'Lunch') NULL AFTER `status`;

ALTER TABLE `tracker_db`.`employees` 
ADD COLUMN `classification_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `leave_balance`;

-- notifications_record Table:

ALTER TABLE `tracker_db`.`notifications_records` 
ADD COLUMN `is_broadcast` TINYINT NOT NULL DEFAULT 0 AFTER `date_created`;

-- emp_classification Table:

ALTER TABLE `tracker_db`.`emp_classification` 
CHANGE COLUMN `role` `role` ENUM('HR', 'Employee', 'Manager') NULL DEFAULT 'Employee' ;

-- -----------------------------------------------------------------------------------------------------------
-- Dummy Data Additions:
-- -----------------------------------------------------------------------------------------------------------

-- Insertion into employees

INSERT INTO employees 
(employee_id, first_name, last_name, contact_no, email, address, id, is_admin, date_hired, supervisor_name, leave_balance)
VALUES
(1, 'Sarah', 'Daniels', '0821234567', 'sarah.daniels@example.com', '12 Main Rd, Cape Town', 8306123993068, 1, '2020-01-15', 'N/A', 12.50),
(2, 'Michael', 'Smith', '0822345678', 'michael.smith@example.com', '45 Loop St, Cape Town',0012257873021, 0, '2021-03-10', 'Sarah Daniels', 8.00),
(3, 'Aisha', 'Khan', '0823456789', 'aisha.khan@example.com', '78 Long St, Cape Town',9202080806014, 0, '2019-07-22', 'Sarah Daniels', 5.50),
(4, 'David', 'Mokoena', '0824567890', 'david.mokoena@example.com', '101 Bree St, Cape Town',9511176099049, 0,'2022-05-01', 'Michael Smith', 10.00),
(5, 'Emily', 'Johnson', '0825678901', 'emily.johnson@example.com', '22 Kloof St, Cape Town', 0311220367024, 0, '2018-11-11', 'Sarah Daniels', 0.00),
(6, 'Thabo', 'Nkosi', '0826789012', 'thabo.nkosi@example.com', '33 Strand St, Cape Town', 9604025460072, 0, '2020-09-14', 'Michael Smith', 7.25),
(7, 'Jessica', 'Williams', '0827890123', 'jessica.williams@example.com','56 Adderley St, Cape Town', 9104021410080,  0, '2021-12-01', 'Sarah Daniels', 9.75),
(8, 'Ahmed', 'Patel', '0828901234', 'ahmed.patel@example.com', '77 Buitengracht St, Cape Town', 9908196611037, 0, '2017-06-30', 'Sarah Daniels', 0.00),
(9, 'Lerato', 'Dlamini', '0829012345', 'lerato.dlamini@example.com', '88 Harrington St, Cape Town',9901291123032, 0,'2022-08-20', 'Michael Smith', 4.00),
(10, 'Daniel', 'Brown', '0820123456', 'daniel.brown@example.com', '99 Roeland St, Cape Town', 9411205676075, 0,'2023-02-05', 'Jessica Williams', 6.50);

-- Updated the empty classification columns:

UPDATE `tracker_db`.`employees` SET `classification_id` = '8' WHERE (`employee_id` = '1');
UPDATE `tracker_db`.`employees` SET `classification_id` = '9' WHERE (`employee_id` = '2');
UPDATE `tracker_db`.`employees` SET `classification_id` = '6' WHERE (`employee_id` = '3');
UPDATE `tracker_db`.`employees` SET `classification_id` = '5' WHERE (`employee_id` = '4');
UPDATE `tracker_db`.`employees` SET `classification_id` = '4' WHERE (`employee_id` = '5');
UPDATE `tracker_db`.`employees` SET `classification_id` = '3' WHERE (`employee_id` = '6');
UPDATE `tracker_db`.`employees` SET `classification_id` = '7' WHERE (`employee_id` = '7');
UPDATE `tracker_db`.`employees` SET `classification_id` = '8' WHERE (`employee_id` = '8');
UPDATE `tracker_db`.`employees` SET `classification_id` = '2' WHERE (`employee_id` = '9');
UPDATE `tracker_db`.`employees` SET `classification_id` = '10' WHERE (`employee_id` = '10');

-- Insertion into emp_classification

INSERT INTO emp_classification 
(classification_id,  department, position, role, employment_type, employee_level)
VALUES
(1,  'Human Resources', 'HR Specialist', 'HR', 'Full-time', 'Mid'),
(2,  'IT', 'Software Developer', 'Employee', 'Full-time', 'Junior'),
(3,  'Finance', 'Accountant', 'Employee', 'Part-time', 'Mid'),
(4,  'IT', 'Team Lead', 'Manager', 'Full-time', 'Senior'),
(5,  'Marketing', 'Marketing Coordinator', 'Employee', 'Intern', 'Junior'),
(6,  'Operations', 'Operations Manager', 'Manager', 'Full-time', 'Manager'),
(7,  'IT', 'System Administrator', 'Employee', 'Contract', 'Mid'),
(8,  'Finance', 'Finance Director', 'Manager', 'Full-time', 'Executive'),
(9,  'Sales', 'Sales Representative', 'Employee', 'Full-time', 'Junior'),
(10,  'Customer Support', 'Support Agent', 'Employee', 'Part-time', 'Junior');

-- Insertion into record_backups

INSERT INTO `tracker_db`.`record_backups` (`record_id`, `employee_id`, `clockin_time`, `clockout_time`, `status`,`type`, `date`) VALUES
 ('1', '1', '09:00', '17:05', 'Inactive','Work', '2025-10-23');
INSERT INTO `tracker_db`.`record_backups` (`record_id`, `employee_id`, `clockin_time`, `clockout_time`, `status`,`type`, `date`) VALUES 
 ('2', '5', '08:45', '17:00', 'Inactive','Work', '2025-10-23');
INSERT INTO `tracker_db`.`record_backups` (`record_id`, `employee_id`, `clockin_time`, `clockout_time`, `status`, `type`, `date`) VALUES
 ('3', '5', '10:30:00', '11:00:00', 'Inactive', 'Tea', '2025-10-23');
INSERT INTO `tracker_db`.`record_backups` (`record_id`, `employee_id`, `clockin_time`, `clockout_time`, `status`, `type`, `date`) VALUES 
 ('4', '5', '13:00:00', '14:00:00', 'Inactive', 'Lunch', '2025-10-23');
INSERT INTO `tracker_db`.`record_backups` (`record_id`, `employee_id`, `clockin_time`, `clockout_time`, `status`, `type`, `date`) VALUES
 ('5', '1', '10:15:00', '10:50:00', 'Inactive', 'Tea', '2025-10-23');
INSERT INTO `tracker_db`.`record_backups` (`record_id`, `employee_id`, `clockin_time`, `clockout_time`, `status`, `type`, `date`) VALUES
 ('6', '1', '13:20:00', '14:00:00', 'Inactive', 'Lunch', '2025-10-23');
INSERT INTO `tracker_db`.`record_backups` (`record_id`, `employee_id`, `clockin_time`, `clockout_time`, `status`, `type`, `date`) VALUES
 ('7', '8', '09:00:00', '16:30:00', 'Inactive', 'Work', '2025-10-23');
INSERT INTO `tracker_db`.`record_backups` (`record_id`, `employee_id`, `clockin_time`, `clockout_time`, `status`, `type`, `date`) VALUES
 ('8', '8', '10:30:00', '10:50:00', 'Inactive', 'Tea', '2025-10-23');
INSERT INTO `tracker_db`.`record_backups` (`record_id`, `employee_id`, `clockin_time`, `clockout_time`, `status`, `type`, `date`) VALUES
 ('9', '8', '12:45:00', '14:00:00', 'Inactive', 'Lunch', '2025-10-23');

UPDATE `tracker_db`.`record_backups` SET `clockin_time` = '08:30:00', `clockout_time` = '16:05:00' WHERE (`record_id` = '1');


 -- Insertion into notifications_records
 
INSERT INTO `tracker_db`.`notifications_records` (`notification_id`, `employee_id`, `title`, `message`, `date_created`) 
VALUES ('1', '1', 'Test', 'Lorem Ipsum', '2025-10-28');
INSERT INTO `tracker_db`.`notifications_records` (`notification_id`, `employee_id`, `title`, `message`, `date_created`) 
VALUES ('2', '4', 'Test2', 'Lorem Ipsum2', '2025-10-28');
INSERT INTO `tracker_db`.`notifications_records` (`notification_id`, `employee_id`, `title`, `message`, `date_created`) 
VALUES ('3', '8', 'Test3', 'Lorem Ipsum3', '2025-10-28');
INSERT INTO `tracker_db`.`notifications_records` (`notification_id`, `employee_id`, `title`, `message`, `date_created`) 
VALUES ('4', '2', 'Test4', 'Lorem Ipsum4', '2025-01-11');
INSERT INTO `tracker_db`.`notifications_records` (`notification_id`, `employee_id`, `title`, `message`, `date_created`) 
VALUES ('5', '6', 'Test5', 'Lorem Ipsum5', '2024-05-05');

 
 -- Insertion into qr_storage
 
 INSERT INTO qr_storage (`qr_id`,`employee_id`,`token`,`scan_url`,`qr_image_data`,`created_at`,`expires_at`) 
 VALUES (1,1,'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMjkzNjk0ODEwLCJpYXQiOjE3NjEyOTM2OTQsImV4cCI6MTc2MTI5NzI5NH0.Siyd-6uezUzJetWY5dJ5bHoxYgz48SwFMosONOaVHjc','http://localhost:8080/api/attendance/scan?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMjkzNjk0ODEwLCJpYXQiOjE3NjEyOTM2OTQsImV4cCI6MTc2MTI5NzI5NH0.Siyd-6uezUzJetWY5dJ5bHoxYgz48SwFMosONOaVHjc','data:image/png;base64,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','2025-10-24 10:14:55','2025-10-24 11:14:55');
INSERT INTO qr_storage (`qr_id`,`employee_id`,`token`,`scan_url`,`qr_image_data`,`created_at`,`expires_at`) 
VALUES (2,2,'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjUsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMjkzNzEzNzA2LCJpYXQiOjE3NjEyOTM3MTMsImV4cCI6MTc2MTI5NzMxM30.i2P8HEplzU2Ttjg7KpzJol97DEMZWZjnXppBNJioPwI','http://localhost:8080/api/attendance/scan?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjUsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMjkzNzEzNzA2LCJpYXQiOjE3NjEyOTM3MTMsImV4cCI6MTc2MTI5NzMxM30.i2P8HEplzU2Ttjg7KpzJol97DEMZWZjnXppBNJioPwI','data:image/png;base64,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','2025-10-24 10:15:13','2025-10-24 11:15:14');
INSERT INTO qr_storage (`qr_id`,`employee_id`,`token`,`scan_url`,`qr_image_data`,`created_at`,`expires_at`) 
VALUES (3,3,'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjQsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMzAwNTIzMDc3LCJpYXQiOjE3NjEzMDA1MjMsImV4cCI6MTc2MTMwNDEyM30.0GHeKGsMTbDlNUgytXcCspPhURkpH0iHQysjUZ4IekU','http://localhost:8080/api/attendance/scan?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjQsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMzAwNTIzMDc3LCJpYXQiOjE3NjEzMDA1MjMsImV4cCI6MTc2MTMwNDEyM30.0GHeKGsMTbDlNUgytXcCspPhURkpH0iHQysjUZ4IekU','data:image/png;base64,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','2025-10-24 12:08:43','2025-10-24 13:08:43');
INSERT INTO qr_storage (`qr_id`,`employee_id`,`token`,`scan_url`,`qr_image_data`,`created_at`,`expires_at`) 
VALUES (4,4,'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMzAzMTYxMjg1LCJpYXQiOjE3NjEzMDMxNjEsImV4cCI6MTc2MTMwNjc2MX0.ttOpE4BVUa0d0ykIpjK7kCDSKoEdrjMlHTPfVJrq4bo','http://localhost:8080/api/attendance/scan?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMzAzMTYxMjg1LCJpYXQiOjE3NjEzMDMxNjEsImV4cCI6MTc2MTMwNjc2MX0.ttOpE4BVUa0d0ykIpjK7kCDSKoEdrjMlHTPfVJrq4bo','data:image/png;base64,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','2025-10-24 12:52:41','2025-10-24 13:52:41');
INSERT INTO qr_storage (`qr_id`,`employee_id`,`token`,`scan_url`,`qr_image_data`,`created_at`,`expires_at`) 
VALUES (5,5,'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMzAzMTg4ODM2LCJpYXQiOjE3NjEzMDMxODgsImV4cCI6MTc2MTMwNjc4OH0.Hwp8TJ9UtSy-jB2yqbH2opW4nJOt3AWaZ8n_5ZmRsd8','http://localhost:8080/api/attendance/scan?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEsInR5cGUiOiJhdHRlbmRhbmNlIiwidGltZXN0YW1wIjoxNzYxMzAzMTg4ODM2LCJpYXQiOjE3NjEzMDMxODgsImV4cCI6MTc2MTMwNjc4OH0.Hwp8TJ9UtSy-jB2yqbH2opW4nJOt3AWaZ8n_5ZmRsd8','data:image/png;base64,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','2025-10-24 12:53:08','2025-10-24 13:53:09');

-- Insertion into hours_management Table:

INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) 
VALUES ('1', '1', '2025-10-27', '2025-10-31', '40', '40', '0');
INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) VALUES
 ('2', '2', '2025-10-27', '2025-10-31', '0', '42', '0');
INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) VALUES
 ('3', '3', '2025-10-27', '2025-10-31', '40', '40', '0');
INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) VALUES
 ('4', '4', '2025-10-27', '2025-10-31', '40', '35', '5');
INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) VALUES
 ('5', '5', '2025-10-27', '2025-10-31', '40', '40', '0');
INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) VALUES
 ('6', '6', '2025-10-27', '2025-10-31', '0', '0', '0');
INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) VALUES
 ('7', '7', '2025-10-27', '2025-10-31', '40', '44', '0');
INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) VALUES
('8', '8', '2025-10-27', '2025-10-31', '40', '40', '0');
INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) VALUES 
('9', '9', '2025-10-27', '2025-10-31', '40', '33', '7');
INSERT INTO `tracker_db`.`hours_management` (`hrs_id`, `employee_id`, `week_start`, `week_end`, `expected_hours`, `total_worked_hours`, `hours_owed`) VALUES
 ('10', '10', '2025-10-27', '2025-10-31', '0', '0', '0');


-- -----------------------------------------------------------------------------------------------------------
-- Foreign Key Additions:
-- -----------------------------------------------------------------------------------------------------------

-- Foreign Key added to Employee Tables

ALTER TABLE `tracker_db`.`employees` 
ADD INDEX `FKEmployee_idx` (`classification_id` ASC) VISIBLE;
;
ALTER TABLE `tracker_db`.`employees` 
ADD CONSTRAINT `FKEmployee`
  FOREIGN KEY (`classification_id`)
  REFERENCES `tracker_db`.`emp_classification` (`classification_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

  
-- Foreign Key added to Records Backup Table:

ALTER TABLE `tracker_db`.`record_backups` 
ADD INDEX `FKRecord_Backups_idx` (`employee_id` ASC) VISIBLE;
;
ALTER TABLE `tracker_db`.`record_backups` 
ADD CONSTRAINT `FKRecord_Backups`
  FOREIGN KEY (`employee_id`)
  REFERENCES `tracker_db`.`employees` (`employee_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
-- Foreign Key added to QR Storage Table:

ALTER TABLE `tracker_db`.`qr_storage` 
ADD INDEX `FKQR_Storage_idx` (`employee_id` ASC) VISIBLE;
;
ALTER TABLE `tracker_db`.`qr_storage` 
ADD CONSTRAINT `FKQR_Storage`
  FOREIGN KEY (`employee_id`)
  REFERENCES `tracker_db`.`employees` (`employee_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

-- Foreign Key added to notifications record Table:

ALTER TABLE `tracker_db`.`notifications_records` 
ADD INDEX `FKNotification_idx` (`employee_id` ASC) VISIBLE;
;
ALTER TABLE `tracker_db`.`notifications_records` 
ADD CONSTRAINT `FKNotification`
  FOREIGN KEY (`employee_id`)
  REFERENCES `tracker_db`.`employees` (`employee_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

-- Foreign Key added to hours management Table:

ALTER TABLE `tracker_db`.`hours_management` 
ADD INDEX `FKHrsManage_idx` (`employee_id` ASC) VISIBLE;
;
ALTER TABLE `tracker_db`.`hours_management` 
ADD CONSTRAINT `FKHrsManage`
  FOREIGN KEY (`employee_id`)
  REFERENCES `tracker_db`.`employees` (`employee_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
-- Foreign Key added to account_auth Table:

ALTER TABLE `tracker_db`.`account_auth` 
ADD INDEX `FKAuth_idx` (`employee_id` ASC) VISIBLE;
;
ALTER TABLE `tracker_db`.`account_auth` 
ADD CONSTRAINT `FKAuth`
  FOREIGN KEY (`employee_id`)
  REFERENCES `tracker_db`.`employees` (`employee_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
  -- Allow NULL for lock_until
ALTER TABLE `tracker_db`.`account_auth` 
MODIFY COLUMN `lock_until` DATETIME NULL;

-- Allow NULL for reset_token_hash  
ALTER TABLE `tracker_db`.`account_auth` 
MODIFY COLUMN `reset_token_hash` VARBINARY(64) NULL;

-- Allow NULL for reset_expires
ALTER TABLE `tracker_db`.`account_auth` 
MODIFY COLUMN `reset_expires` DATETIME NULL;
