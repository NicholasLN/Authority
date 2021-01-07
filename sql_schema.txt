CREATE TABLE `countries` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
;
CREATE TABLE `parties` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`partyBio` VARCHAR(500) NULL COLLATE 'utf8mb4_0900_ai_ci',
	`partyPic` VARCHAR(900) NULL COLLATE 'utf8mb4_0900_ai_ci',
	`nation` VARCHAR(50) NULL COLLATE 'utf8mb4_0900_ai_ci',
	`name` VARCHAR(60) NULL COLLATE 'utf8mb4_0900_ai_ci',
	`ecoPos` DOUBLE NULL,
	`socPos` DOUBLE NULL,
	`partyRoles` MEDIUMTEXT NULL COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COMMENT='table for political parties'
COLLATE='utf8mb4_0900_ai_ci'
;
CREATE TABLE `states` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`abbreviation` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`active` INT NULL DEFAULT '0',
	`country` VARCHAR(255) NULL COLLATE 'utf8mb4_0900_ai_ci',
	`flag` TEXT NULL COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
;
CREATE TABLE `users` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`username` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`password` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`regCookie` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`currentCookie` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`regIP` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`currentIP` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`hsi` DOUBLE NOT NULL DEFAULT '10',
	`politicianName` VARCHAR(55) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`lastOnline` VARCHAR(500) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_0900_ai_ci',
	`profilePic` VARCHAR(2500) NULL DEFAULT 'images/userPics/default.jpg' COLLATE 'utf8mb4_0900_ai_ci',
	`bio` VARCHAR(2500) NOT NULL DEFAULT 'I am gay!' COLLATE 'utf8mb4_0900_ai_ci',
	`state` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`country` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`ecoPos` DOUBLE NOT NULL DEFAULT '0',
	`socPos` DOUBLE NOT NULL DEFAULT '0',
	`authority` DOUBLE NULL DEFAULT '50',
	`campaignFinance` BIGINT NULL DEFAULT '50000',
	`party` INT NULL DEFAULT '0',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
;
