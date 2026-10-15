-- ============================================================================
-- KINETICA BIOMECHANICS CLUB INITIAL SEED DATA
-- Matches the 4 membership plans on the landing page, default positions,
-- staff accounts (Manager, Trainer), client memberships, and CRM records.
-- ============================================================================

USE `kinetica_db`;

-- 1. Insert 4 Membership Plans (Standart, StandartPlus, Premium, Max)
INSERT INTO `membership_plans` 
(`id`, `code`, `name`, `badge`, `description`, `price_monthly`, `price_annual_monthly`, `duration_days`, `is_24_7_access`, `has_group_classes`, `has_sauna`, `cryo_sessions_limit`, `guest_passes_per_month`, `personal_trainings_included`, `has_fuel_bar_daily`, `sort_order`)
VALUES
(1, 'standart', 'Standart', 'CORE FOUNDATION', 'Full gym floor & free weights access from 6:00 to 23:00', 179.00, 149.00, 30, 0, 0, 0, 0, 0, 0, 0, 1),
(2, 'standart_plus', 'StandartPlus', 'ENERGY & GROUP', 'Everything in Standart + unlimited group fitness classes and sauna access', 239.00, 199.00, 30, 0, 1, 1, 0, 0, 0, 0, 2),
(3, 'premium', 'Premium', '🔥 MOST POPULAR', '24/7 biometric turnstile access, -110°C cryo, fresh towel, 2 free guest passes/month', 319.00, 269.00, 30, 1, 1, 1, 4, 2, 1, 0, 3),
(4, 'max', 'Max', '👑 ALL-INCLUSIVE', 'All-inclusive privileges, 4 personal training sessions/month, daily fuel bar smoothie', 499.00, 419.00, 30, 1, 1, 1, 999, 4, 4, 1, 4)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 2. Insert Staff Positions
INSERT INTO `positions` (`id`, `title`, `department`, `default_salary_type`) VALUES
(1, 'Lead Biomechanics Coach', 'coaching', 'hybrid'),
(2, 'Sports Physiotherapist', 'medical', 'per_session'),
(3, 'Front Desk Concierge', 'reception', 'hourly'),
(4, 'Chief Accountant', 'management', 'fixed_monthly'),
(5, 'General Club Manager', 'management', 'fixed_monthly')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 3. Insert Users (Password for all test accounts is: password123)
-- Bcrypt hash below corresponds to: password123
INSERT INTO `users` 
(`id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `lead_status`, `lead_source`, `fitness_goal`, `preferred_language`, `free_courses_unlocked`) 
VALUES
(1, 'Alex Morgan', 'member@kinetica.gym', '+48 500 123 456', '$2y$12$pCfHacBo0UhDvrYkHMGvS.Ab8/38OyMl8E87934EIwk/QAdpNkjtW', 'client', 'converted', 'website_registration', 'Biomechanics & Hypertrophy', 'en', 1),
(2, 'Marek Kowalski', 'trainer@kinetica.gym', '+48 500 999 111', '$2y$12$pCfHacBo0UhDvrYkHMGvS.Ab8/38OyMl8E87934EIwk/QAdpNkjtW', 'employee', 'converted', 'staff_hire', 'Olympic Biomechanics Coach', 'pl', 1),
(3, 'Katarzyna Wiśniewska', 'manager@kinetica.gym', '+48 500 888 777', '$2y$12$pCfHacBo0UhDvrYkHMGvS.Ab8/38OyMl8E87934EIwk/QAdpNkjtW', 'admin', 'converted', 'management', 'Club General Management', 'pl', 1),
(4, 'Piotr Zieliński', 'lead@kinetica.gym', '+48 501 222 333', '$2y$12$pCfHacBo0UhDvrYkHMGvS.Ab8/38OyMl8E87934EIwk/QAdpNkjtW', 'lead', 'new', 'free_courses', 'Fix Posture & Spine Rehab', 'pl', 1)
ON DUPLICATE KEY UPDATE `full_name`=VALUES(`full_name`);

-- 4. Insert Employees (Staff Details & Compensation)
INSERT INTO `employees` 
(`id`, `user_id`, `position_id`, `first_name`, `last_name`, `email`, `phone`, `iban_account`, `salary_type`, `base_salary`, `rate_per_session`, `salary_payout_day`, `hire_date`, `status`)
VALUES
(1, 2, 1, 'Marek', 'Kowalski', 'trainer@kinetica.gym', '+48 500 999 111', 'PL84 1050 1445 1000 0090 8124 5510', 'hybrid', 6500.00, 85.00, 10, '2024-03-01', 'active'),
(2, 3, 5, 'Katarzyna', 'Wiśniewska', 'manager@kinetica.gym', '+48 500 888 777', 'PL61 1090 1014 0000 0007 1219 8128', 'fixed_monthly', 12000.00, 0.00, 10, '2024-01-15', 'active')
ON DUPLICATE KEY UPDATE `first_name`=VALUES(`first_name`);

-- 5. Insert Active Membership for Demo Client (Alex Morgan)
INSERT INTO `user_memberships`
(`id`, `user_id`, `plan_id`, `billing_cycle`, `purchased_price`, `start_date`, `end_date`, `status`, `freeze_days_remaining`, `cryo_sessions_used`, `guest_passes_used`, `auto_renew`)
VALUES
(1, 1, 3, 'monthly', 319.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 28 DAY), 'active', 30, 1, 0, 1)
ON DUPLICATE KEY UPDATE `status`='active';

-- 6. Insert Recent Turnstile Visits
INSERT INTO `gym_visits` (`user_id`, `entry_time`, `exit_time`, `access_method`) VALUES
(1, DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_SUB(NOW(), INTERVAL 30 MINUTE), 'nfc'),
(1, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 46 HOUR), 'face_id');

-- 7. Insert Monthly Payroll Record for Coach Marek
INSERT INTO `payroll_records`
(`employee_id`, `period_month`, `period_year`, `base_amount`, `bonus_amount`, `deductions_amount`, `total_amount`, `payment_due_date`, `status`, `accountant_notes`)
VALUES
(1, MONTH(CURDATE()), YEAR(CURDATE()), 6500.00, 3390.00, 0.00, 9890.00, DATE(CONCAT(YEAR(CURDATE()), '-', LPAD(MONTH(CURDATE()), 2, '0'), '-10')), 'calculated', 'Includes 34 completed PT sessions @ 85 PLN + 500 PLN performance bonus.')
ON DUPLICATE KEY UPDATE `total_amount`=VALUES(`total_amount`);
