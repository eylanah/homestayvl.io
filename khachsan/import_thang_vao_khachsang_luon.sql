-- =====================================================
-- MIGRATION: Thay t_expire (DATE) → pin_expire (DATETIME)
-- Chạy file này 1 lần trong phpMyAdmin hoặc terminal
-- =====================================================

ALTER TABLE `user_cred`
  DROP COLUMN `t_expire`,
  MODIFY COLUMN `token` VARCHAR(10) DEFAULT NULL COMMENT 'Lưu mã PIN 6 số',
  ADD COLUMN `pin_expire` DATETIME DEFAULT NULL AFTER `token`;
