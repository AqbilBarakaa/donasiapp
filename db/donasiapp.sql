-- SKEMA DATABASE FINAL UNTUK APLIKASI DONASI (donasiapp)
-- VERSI 7 - DENGAN FITUR UBAH PASSWORD
-- FITUR: VIEW, TRIGGER, CURSOR, STORED PROCEDURE

DROP DATABASE IF EXISTS donasiapp;
CREATE DATABASE donasiapp;
USE donasiapp;

-- ----------------------------------
-- 1. STRUKTUR TABEL
-- ----------------------------------
CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY, `username` VARCHAR(50) UNIQUE NOT NULL, `email` VARCHAR(100) UNIQUE NOT NULL, `password` VARCHAR(255) NOT NULL, `full_name` VARCHAR(100) NOT NULL, `user_type` ENUM('donor', 'admin') DEFAULT 'donor', `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE `donation_categories` (
  `category_id` INT AUTO_INCREMENT PRIMARY KEY, `category_name` VARCHAR(50) NOT NULL
);
CREATE TABLE `campaigns` (
  `campaign_id` INT AUTO_INCREMENT PRIMARY KEY, `title` VARCHAR(255) NOT NULL, `description` TEXT NOT NULL, `image_url` VARCHAR(255) DEFAULT 'assets/img/placeholder.png', `target_amount` DECIMAL(15, 2) NOT NULL, `collected_amount` DECIMAL(15, 2) DEFAULT 0.00, `creator_id` INT NOT NULL, `category_id` INT NOT NULL, `status` ENUM('active', 'completed', 'expired') DEFAULT 'active', `end_date` DATE NOT NULL, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (`creator_id`) REFERENCES `users`(`user_id`), FOREIGN KEY (`category_id`) REFERENCES `donation_categories`(`category_id`) ON DELETE RESTRICT ON UPDATE CASCADE
);
CREATE TABLE `donations` (
  `donation_id` INT AUTO_INCREMENT PRIMARY KEY, `campaign_id` INT NOT NULL, `donor_id` INT NOT NULL, `amount` DECIMAL(15, 2) NOT NULL, `message` TEXT, `is_anonymous` BOOLEAN DEFAULT FALSE, `payment_status` ENUM('pending', 'completed', 'failed') DEFAULT 'pending', `donation_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`campaign_id`) ON DELETE CASCADE, FOREIGN KEY (`donor_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
);

-- ----------------------------------
-- 2. VIEWS
-- ----------------------------------
CREATE VIEW V_CampaignSummary AS SELECT c.campaign_id, c.title, c.description, c.image_url, c.target_amount, c.collected_amount, IF(c.target_amount > 0, (c.collected_amount / c.target_amount * 100), 0) AS completion_percentage, DATEDIFF(c.end_date, CURDATE()) AS days_remaining, c.end_date, c.status, u.full_name AS creator_name, dc.category_name, c.created_at FROM campaigns c JOIN users u ON c.creator_id = u.user_id JOIN donation_categories dc ON c.category_id = dc.category_id;
CREATE VIEW V_DonationHistory AS SELECT d.donation_id, d.donor_id, d.amount, d.donation_date, d.payment_status, d.is_anonymous, d.message, u.full_name AS donor_name, c.campaign_id, c.title AS campaign_title FROM donations d JOIN campaigns c ON d.campaign_id = c.campaign_id JOIN users u ON d.donor_id = u.user_id;

-- ----------------------------------
-- 3. TRIGGERS
-- ----------------------------------
DELIMITER $$
CREATE TRIGGER T_AfterDonationCompleted AFTER UPDATE ON donations FOR EACH ROW
BEGIN
    IF NEW.payment_status = 'completed' AND OLD.payment_status != 'completed' THEN
        UPDATE campaigns SET collected_amount = collected_amount + NEW.amount WHERE campaign_id = NEW.campaign_id;
    END IF;
END$$
DELIMITER ;

-- ----------------------------------
-- 4. STORED PROCEDURES
-- ----------------------------------
DELIMITER $$
CREATE PROCEDURE SP_ProcessDonation(IN p_campaign_id INT, IN p_donor_id INT, IN p_amount DECIMAL(15,2), IN p_message TEXT, IN p_is_anonymous BOOLEAN, OUT p_donation_id INT)
BEGIN
    INSERT INTO donations (campaign_id, donor_id, amount, message, is_anonymous, payment_status) VALUES (p_campaign_id, p_donor_id, p_amount, p_message, p_is_anonymous, 'pending');
    SET p_donation_id = LAST_INSERT_ID();
    UPDATE donations SET payment_status = 'completed' WHERE donation_id = p_donation_id;
END$$
CREATE PROCEDURE SP_AddCampaign(IN p_title VARCHAR(255), IN p_description TEXT, IN p_target_amount DECIMAL(15,2), IN p_creator_id INT, IN p_category_id INT, IN p_end_date DATE, IN p_image_url VARCHAR(255), OUT p_campaign_id INT)
BEGIN
    INSERT INTO campaigns (title, description, target_amount, creator_id, category_id, end_date, image_url) VALUES (p_title, p_description, p_target_amount, p_creator_id, p_category_id, p_end_date, p_image_url);
    SET p_campaign_id = LAST_INSERT_ID();
END$$
CREATE PROCEDURE SP_EditCampaign(IN p_campaign_id INT, IN p_title VARCHAR(255), IN p_description TEXT, IN p_target_amount DECIMAL(15,2), IN p_category_id INT, IN p_end_date DATE, IN p_image_url VARCHAR(255))
BEGIN
    UPDATE campaigns SET title=p_title, description=p_description, target_amount=p_target_amount, category_id=p_category_id, end_date=p_end_date, image_url=p_image_url WHERE campaign_id = p_campaign_id;
END$$
CREATE PROCEDURE SP_DeleteCampaign(IN p_campaign_id INT)
BEGIN
    DELETE FROM donations WHERE campaign_id = p_campaign_id;
    DELETE FROM campaigns WHERE campaign_id = p_campaign_id;
END$$
CREATE PROCEDURE SP_AddCategory(IN p_category_name VARCHAR(50))
BEGIN
    INSERT INTO donation_categories (category_name) VALUES (p_category_name);
END$$
CREATE PROCEDURE SP_EditCategory(IN p_category_id INT, IN p_category_name VARCHAR(50))
BEGIN
    UPDATE donation_categories SET category_name = p_category_name WHERE category_id = p_category_id;
END$$
CREATE PROCEDURE SP_DeleteCategory(IN p_category_id INT, OUT p_message VARCHAR(255))
BEGIN
    DECLARE campaign_count INT;
    SELECT COUNT(*) INTO campaign_count FROM campaigns WHERE category_id = p_category_id;
    IF campaign_count > 0 THEN SET p_message = 'ERROR: Kategori tidak dapat dihapus.'; ELSE DELETE FROM donation_categories WHERE category_id = p_category_id; SET p_message = 'SUCCESS: Kategori berhasil dihapus.'; END IF;
END$$
CREATE PROCEDURE SP_UpdateProfile(IN p_user_id INT, IN p_full_name VARCHAR(100), IN p_username VARCHAR(50), IN p_email VARCHAR(100))
BEGIN
    UPDATE users SET full_name = p_full_name, username = p_username, email = p_email WHERE user_id = p_user_id;
END$$
CREATE PROCEDURE SP_UpdatePassword(IN p_user_id INT, IN p_new_password VARCHAR(255))
BEGIN
    UPDATE users SET password = p_new_password WHERE user_id = p_user_id;
END$$

-- ----------------------------------
-- 5. CURSOR
-- ----------------------------------
CREATE PROCEDURE SP_UpdateExpiredCampaigns()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_campaign_id INT;
    DECLARE campaign_cursor CURSOR FOR SELECT campaign_id FROM campaigns WHERE end_date < CURDATE() AND status = 'active';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN campaign_cursor;
    campaign_loop: LOOP
        FETCH campaign_cursor INTO v_campaign_id;
        IF done THEN LEAVE campaign_loop; END IF;
        UPDATE campaigns SET status = 'expired' WHERE campaign_id = v_campaign_id;
    END LOOP;
    CLOSE campaign_cursor;
END$$
DELIMITER ;

-- ----------------------------------
-- 6. DATA CONTOH
-- ----------------------------------
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `user_type`) VALUES
('admin', 'admin@gmail.com', 'admin1', 'Sigma Skibiddi', 'admin'),
('paldok', 'paldok@gmail.com', 'pal123', 'Rypaldho', 'donor'),
('fcyacr', 'aqbil@gmail.com', 'lavandula', 'Aqbil', 'donor');
INSERT INTO `donation_categories` (`category_name`) VALUES ('Pendidikan'), ('Kesehatan'), ('Bencana Alam'), ('Keagamaan');
INSERT INTO `campaigns` (`title`, `description`, `image_url`, `target_amount`, `creator_id`, `category_id`, `end_date`) VALUES
('Bantu Renovasi Sekolah untuk Wilayah Terpencil Indonesia', 'Bersekolah di tempat yang layak adalah impian semua siswa, termasuk mereka yang tinggal di wilayah Pelosok Indonesia. Karena ketersediaan sekolah layak dan fasilitas yang lengkap dapat membantu meningkatkan kualitas Pendidikan. Namun sayangnya, masih banyak siswa di pelosok Indonesia yang harus menimba ilmu di tempat yang hanya terbangun dari papan kayu, serta fasilitas belajarnya sudah rapuh dan lapuk.', 'assets/uploads/1750155210_sekolah.jpeg', 50000000, 1, 1, '2025-12-31'),
('Tebar Quran Untuk Pelosok Nusantara', 'Sebuah lembaga yang bergerak di bidang pendidikan dan kesehatan anak, serta berbagai kegiatan sosial lainnya. Kami hadir untuk menjadi jembatan kebaikan antara para dermawan dan mereka yang membutuhkan, khususnya anak-anak yatim dan dhuafa.', 'assets/uploads/1750155222_agama.avif', 75000000, 1, 2, '2025-09-30');
CALL SP_ProcessDonation(1, 2, 5000000, 'Semoga bermanfaat', 0, @donation_id);
CALL SP_ProcessDonation(1, 3, 5000000, '', 0, @donation_id);
CALL SP_ProcessDonation(1, 3, 10000000, 'Semangat Belajarnya', 1, @donation_id);