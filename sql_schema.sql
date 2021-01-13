CREATE TABLE `countries` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
;
CREATE TABLE `parties` (
                           `id` INT(11) NOT NULL AUTO_INCREMENT,
                           `partyBio` VARCHAR(500) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                           `partyPic` VARCHAR(900) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                           `nation` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                           `name` VARCHAR(60) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                           `ecoPos` DOUBLE NULL DEFAULT NULL,
                           `socPos` DOUBLE NULL DEFAULT NULL,
                           `partyRoles` MEDIUMTEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                           `discord` VARCHAR(16) NULL DEFAULT '0' COLLATE 'utf8_general_ci',
                           PRIMARY KEY (`id`) USING BTREE
)
    COMMENT='table for political parties'
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=3
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
                         `id` INT(11) NOT NULL AUTO_INCREMENT,
                         `username` TEXT NOT NULL COLLATE 'utf8_general_ci',
                         `password` TEXT NOT NULL COLLATE 'utf8_general_ci',
                         `regCookie` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
                         `currentCookie` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
                         `regIP` TEXT NOT NULL COLLATE 'utf8_general_ci',
                         `currentIP` TEXT NOT NULL COLLATE 'utf8_general_ci',
                         `hsi` DOUBLE NOT NULL DEFAULT '10',
                         `politicianName` VARCHAR(55) NOT NULL COLLATE 'utf8_general_ci',
                         `lastOnline` VARCHAR(500) NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci',
                         `profilePic` VARCHAR(2500) NULL DEFAULT 'images/userPics/default.jpg' COLLATE 'utf8_general_ci',
                         `bio` VARCHAR(2500) NOT NULL DEFAULT 'I am gay!' COLLATE 'utf8_general_ci',
                         `state` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
                         `nation` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
                         `ecoPos` DOUBLE NOT NULL DEFAULT '0',
                         `socPos` DOUBLE NOT NULL DEFAULT '0',
                         `authority` DOUBLE NULL DEFAULT '50',
                         `campaignFinance` BIGINT(20) NULL DEFAULT '50000',
                         `party` INT(11) NULL DEFAULT '0',
                         `partyInfluence` DOUBLE NULL DEFAULT '0',
                         PRIMARY KEY (`id`) USING BTREE
)
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=55
;

COLLATE='utf8mb4_0900_ai_ci'
;
