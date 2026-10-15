-- ============================================================================
-- KINETICA BIOMECHANICS CLUB & CRM DATABASE SCHEMA (WARSAW, POLAND)
-- RDBMS: MySQL 8.0 | Charset: utf8mb4_unicode_ci
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `kinetica_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `kinetica_db`;

-- Temporarily disable foreign key constraints for safe recreation
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `crm_notifications`;
DROP TABLE IF EXISTS `payroll_records`;
DROP TABLE IF EXISTS `work_shifts`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `positions`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `gym_visits`;
DROP TABLE IF EXISTS `user_memberships`;
DROP TABLE IF EXISTS `membership_plans`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. UNIFIED USERS & MARKETING SALES FUNNEL TABLE
-- Stores both registered leads (free video courses) and paying gym members.
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(30) NULL,
    `password_hash` VARCHAR(255) NULL COMMENT 'Nullable for leads without customer portal login credentials',
    
    -- Role-based access control
    `role` ENUM('lead', 'client', 'employee', 'trainer', 'admin') NOT NULL DEFAULT 'lead',
    
    -- Marketing lead acquisition funnel
    `lead_status` ENUM('new', 'contacted', 'trial_scheduled', 'converted', 'unqualified') NOT NULL DEFAULT 'new',
    `lead_source` VARCHAR(50) DEFAULT 'free_courses' COMMENT 'Origin source: free_courses, website_order, instagram, walk_in',
    `fitness_goal` VARCHAR(100) NULL COMMENT 'Primary interest from registration modal: posture, hypertrophy, fat_loss',
    `preferred_language` ENUM('pl', 'en') NOT NULL DEFAULT 'pl',
    `free_courses_unlocked` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Indicates access to 12+ free video masterclasses',
    
    -- CRM sales follow-up notes
    `manager_notes` TEXT NULL COMMENT 'Internal sales notes: call history, offered discounts, client feedback',
    `last_contacted_at` DATETIME NULL,
    `next_follow_up_date` DATE NULL COMMENT 'Scheduled callback date for sales representative',
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_lead_status` (`lead_status`),
    INDEX `idx_users_phone` (`phone`)
) ENGINE=InnoDB COMMENT='Unified users and CRM marketing leads table';

-- ----------------------------------------------------------------------------
-- 2. MEMBERSHIP PLANS CATALOG
-- Allows dynamic pricing and feature changes without modifying application code.
-- ----------------------------------------------------------------------------
CREATE TABLE `membership_plans` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'System plan identifier: standart, standart_plus, premium, max',
    `name` VARCHAR(100) NOT NULL,
    `badge` VARCHAR(50) NULL COMMENT 'Marketing label: Best Value, Most Popular, Core Foundation',
    `description` TEXT NULL,
    
    -- Pricing structure (PLN)
    `price_monthly` DECIMAL(10, 2) NOT NULL COMMENT 'Standard monthly recurring price in PLN',
    `price_annual_monthly` DECIMAL(10, 2) NOT NULL COMMENT 'Discounted monthly rate when billed annually (-20%)',
    `duration_days` INT NOT NULL DEFAULT 30,
    
    -- Membership perks & entitlements
    `is_24_7_access` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Enables round-the-clock biometric turnstile entry',
    `has_group_classes` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Unlimited group fitness classes: Hyrox, Yoga, Spin',
    `has_sauna` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Access to Finnish dry sauna and eucalyptus steam bath',
    `cryo_sessions_limit` INT NOT NULL DEFAULT 0 COMMENT 'Monthly sub-zero cryotherapy quota (0=none, 4=Premium, 999=unlimited)',
    `guest_passes_per_month` INT NOT NULL DEFAULT 0 COMMENT 'Complimentary monthly guest passes to bring friends',
    `personal_trainings_included` INT NOT NULL DEFAULT 0 COMMENT 'Number of 1-on-1 PT sessions included per month',
    `has_fuel_bar_daily` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Includes daily signature protein shake at Kinetica Fuel Bar',
    
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Soft-deletion flag for archived membership tiers',
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Gym membership pricing plans catalog';

-- ----------------------------------------------------------------------------
-- 3. ACTIVE & HISTORICAL CLIENT MEMBERSHIPS
-- Tracks expiration dates, freeze allowances, and renewals.
-- ----------------------------------------------------------------------------
CREATE TABLE `user_memberships` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `plan_id` INT UNSIGNED NOT NULL,
    
    `billing_cycle` ENUM('monthly', 'annual') NOT NULL DEFAULT 'monthly',
    `purchased_price` DECIMAL(10, 2) NOT NULL COMMENT 'Historical locked purchase price at time of order',
    
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL COMMENT 'Membership expiration date for CRM retention campaigns and alerts',
    `status` ENUM('active', 'frozen', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    
    -- Vacation / Injury freeze allowance
    `freeze_days_remaining` INT NOT NULL DEFAULT 30 COMMENT 'Complimentary yearly freeze allowance (up to 30 days)',
    `frozen_until` DATE NULL,
    
    -- Monthly quota consumption
    `cryo_sessions_used` INT NOT NULL DEFAULT 0,
    `guest_passes_used` INT NOT NULL DEFAULT 0,
    `pt_sessions_used` INT NOT NULL DEFAULT 0,
    
    `auto_renew` BOOLEAN NOT NULL DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`id`),
    INDEX `idx_membership_dates` (`status`, `end_date`),
    INDEX `idx_membership_user` (`user_id`)
) ENGINE=InnoDB COMMENT='Client active and historical gym memberships';

-- ----------------------------------------------------------------------------
-- 4. PAYMENTS & FINANCIAL TRANSACTIONS
-- ----------------------------------------------------------------------------
CREATE TABLE `payments` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `membership_id` BIGINT UNSIGNED NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'PLN',
    `payment_method` ENUM('blik', 'apple_pay', 'card', 'cash', 'transfer') NOT NULL,
    `payment_status` ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'completed',
    `transaction_reference` VARCHAR(100) NULL COMMENT 'External payment gateway transaction ID (e.g. Stripe, PayU, BLIK)',
    `paid_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`membership_id`) REFERENCES `user_memberships` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Payment transactions log';

-- ----------------------------------------------------------------------------
-- 5. GYM ACCESS LOGS & TURNSTILE TELEMETRY
-- ----------------------------------------------------------------------------
CREATE TABLE `gym_visits` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `entry_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `exit_time` DATETIME NULL,
    `access_method` ENUM('nfc', 'face_id', 'qr_app', 'reception_manual') NOT NULL DEFAULT 'nfc' COMMENT 'Authentication method at Warsaw turnstile',
    
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_visit_user` (`user_id`, `entry_time`)
) ENGINE=InnoDB COMMENT='Club entry and exit access logs';

-- ----------------------------------------------------------------------------
-- 6. EMPLOYEE POSITIONS & DEPARTMENTS
-- ----------------------------------------------------------------------------
CREATE TABLE `positions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL COMMENT 'Personal Trainer, Physiotherapist, Receptionist, Chief Accountant',
    `department` ENUM('coaching', 'reception', 'management', 'medical', 'maintenance') NOT NULL,
    `default_salary_type` ENUM('fixed_monthly', 'hourly', 'per_session', 'hybrid') NOT NULL DEFAULT 'fixed_monthly'
) ENGINE=InnoDB COMMENT='Staff positions and departments catalog';

-- ----------------------------------------------------------------------------
-- 7. CLUB EMPLOYEES & SALARY CALCULATION RULES
-- ----------------------------------------------------------------------------
CREATE TABLE `employees` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL COMMENT 'Optional link to users table for CRM authentication',
    `position_id` INT UNSIGNED NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(30) NOT NULL,
    `iban_account` VARCHAR(34) NULL COMMENT 'Polish bank account number (IBAN) for payroll bank transfers',
    
    -- Compensation model
    `salary_type` ENUM('fixed_monthly', 'hourly', 'per_session', 'hybrid') NOT NULL DEFAULT 'fixed_monthly',
    `base_salary` DECIMAL(10, 2) NOT NULL COMMENT 'Base monthly salary or hourly base rate in PLN',
    `rate_per_session` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Commission per completed personal training session in PLN',
    `salary_payout_day` TINYINT UNSIGNED NOT NULL DEFAULT 10 COMMENT 'Day of the month when salary is due (e.g. 10th of every month)',
    
    `hire_date` DATE NOT NULL,
    `status` ENUM('active', 'on_leave', 'terminated') NOT NULL DEFAULT 'active',
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`)
) ENGINE=InnoDB COMMENT='Club employees and compensation settings';

-- ----------------------------------------------------------------------------
-- 8. WORK SHIFTS & ATTENDANCE SCHEDULE
-- ----------------------------------------------------------------------------
CREATE TABLE `work_shifts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `shift_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `hours_worked` DECIMAL(4, 2) DEFAULT 0.00,
    `status` ENUM('scheduled', 'completed', 'cancelled', 'absent') NOT NULL DEFAULT 'scheduled',
    
    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
    INDEX `idx_shift_date` (`shift_date`, `employee_id`)
) ENGINE=InnoDB COMMENT='Staff scheduled and completed work shifts';

-- ----------------------------------------------------------------------------
-- 9. MONTHLY PAYROLL ACCRUALS (FOR ACCOUNTANT AUTOMATION)
-- ----------------------------------------------------------------------------
CREATE TABLE `payroll_records` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `period_month` TINYINT UNSIGNED NOT NULL COMMENT 'Payroll period month (1 - 12)',
    `period_year` SMALLINT UNSIGNED NOT NULL COMMENT 'Payroll period year (e.g. 2026)',
    
    `base_amount` DECIMAL(10, 2) NOT NULL,
    `bonus_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Performance bonuses and PT commissions',
    `deductions_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Taxes, social security ZUS, or penalties',
    `total_amount` DECIMAL(10, 2) NOT NULL COMMENT 'Final net payout amount in PLN',
    
    -- Due date and accountant workflow status
    `payment_due_date` DATE NOT NULL COMMENT 'Deadline date by which accountant must disburse funds',
    `status` ENUM('calculated', 'pending_approval', 'paid', 'cancelled') NOT NULL DEFAULT 'calculated',
    `paid_at` DATETIME NULL,
    `accountant_notes` TEXT NULL,
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_employee_period` (`employee_id`, `period_year`, `period_month`),
    INDEX `idx_payroll_due` (`status`, `payment_due_date`)
) ENGINE=InnoDB COMMENT='Monthly employee payroll statements and disbursement statuses';

-- ----------------------------------------------------------------------------
-- 10. SYSTEM CRM NOTIFICATIONS
-- Triggers alerts for accountants (payroll due), managers (callback leads), etc.
-- ----------------------------------------------------------------------------
CREATE TABLE `crm_notifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `target_role` ENUM('accountant', 'sales_manager', 'admin', 'trainer') NOT NULL,
    `type` ENUM('payroll_due', 'membership_expiring', 'new_lead', 'shift_reminder') NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `link` VARCHAR(255) NULL COMMENT 'Target CRM route URL (e.g. /crm/payroll/2026-10 or /crm/leads/42)',
    `is_read` BOOLEAN NOT NULL DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_notifications_unread` (`target_role`, `is_read`)
) ENGINE=InnoDB COMMENT='Internal CRM notifications and task alerts';
