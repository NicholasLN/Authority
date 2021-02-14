CREATE TABLE `users`
(
    `id`              INT(11)       NOT NULL AUTO_INCREMENT,
    `admin`           INT(11)       NULL     DEFAULT '0',
    `username`        TEXT          NOT NULL COLLATE 'utf8_general_ci',
    `password`        TEXT          NOT NULL COLLATE 'utf8_general_ci',
    `regCookie`       VARCHAR(255)  NOT NULL COLLATE 'utf8_general_ci',
    `currentCookie`   VARCHAR(255)  NOT NULL COLLATE 'utf8_general_ci',
    `regIP`           TEXT          NOT NULL COLLATE 'utf8_general_ci',
    `currentIP`       TEXT          NOT NULL COLLATE 'utf8_general_ci',
    `hsi`             DOUBLE        NOT NULL DEFAULT '10',
    `politicianName`  VARCHAR(55)   NOT NULL COLLATE 'utf8_unicode_ci',
    `lastOnline`      VARCHAR(500)  NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci',
    `profilePic`      VARCHAR(2500) NULL     DEFAULT 'images/userPics/default.jpg' COLLATE 'utf8_general_ci',
    `bio`             VARCHAR(2500) NOT NULL DEFAULT 'I am gay!' COLLATE 'utf8_general_ci',
    `state`           VARCHAR(255)  NOT NULL COLLATE 'utf8_general_ci',
    `nation`          VARCHAR(255)  NOT NULL COLLATE 'utf8_general_ci',
    `ecoPos`          DOUBLE        NOT NULL DEFAULT '0',
    `socPos`          DOUBLE        NOT NULL DEFAULT '0',
    `authority`       DOUBLE        NULL     DEFAULT '50',
    `campaignFinance` BIGINT(20)    NULL     DEFAULT '50000',
    `party`           INT(11)       NULL     DEFAULT '0',
    `partyInfluence`  DOUBLE        NULL     DEFAULT '0',
    `partyVotingFor`  INT(11)       NULL     DEFAULT '0',
    PRIMARY KEY (`id`) USING BTREE
)
    COLLATE = 'utf8_general_ci'
    ENGINE = InnoDB
    AUTO_INCREMENT = 139
;
CREATE TABLE `states`
(
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
    `abbreviation` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
    `active`       INT(11)      NULL DEFAULT '0',
    `country`      VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
    `flag`         TEXT         NULL DEFAULT NULL COLLATE 'utf8_general_ci',
    PRIMARY KEY (`id`) USING BTREE
)
    COLLATE = 'utf8_general_ci'
    ENGINE = InnoDB
    AUTO_INCREMENT = 51
;
CREATE TABLE `partyVotes`
(
    `id`        INT(11)      NOT NULL AUTO_INCREMENT,
    `author`    INT(11)      NULL DEFAULT NULL,
    `party`     INT(11)      NULL DEFAULT NULL,
    `name`      VARCHAR(65)  NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
    `actions`   LONGTEXT     NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
    `ayes`      VARCHAR(900) NULL DEFAULT '[]' COLLATE 'latin1_swedish_ci',
    `nays`      VARCHAR(900) NULL DEFAULT '[]' COLLATE 'latin1_swedish_ci',
    `passed`    INT(11)      NULL DEFAULT '0',
    `expiresAt` INT(11)      NULL DEFAULT NULL,
    `delay`     INT(11)      NULL DEFAULT '0',
    PRIMARY KEY (`id`) USING BTREE
)
    COLLATE = 'latin1_swedish_ci'
    ENGINE = InnoDB
    AUTO_INCREMENT = 27
;
CREATE TABLE `fundRequests`
(
    `id`         INT(11)     NOT NULL AUTO_INCREMENT,
    `party`      INT(11)     NOT NULL DEFAULT '0',
    `requester`  INT(11)     NOT NULL DEFAULT '0',
    `requesting` DOUBLE      NOT NULL DEFAULT '0',
    `reason`     VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'latin1_swedish_ci',
    `fulfilled`  INT(11)     NULL     DEFAULT '0',
    `secret`     VARCHAR(50) NULL     DEFAULT NULL COLLATE 'latin1_swedish_ci',
    PRIMARY KEY (`id`) USING BTREE
)
    COLLATE = 'latin1_swedish_ci'
    ENGINE = InnoDB
    AUTO_INCREMENT = 60
;
CREATE TABLE `parties`
(
    `id`            INT(11)         NOT NULL AUTO_INCREMENT,
    `partyBio`      VARCHAR(1000)   NULL     DEFAULT '' COLLATE 'utf8_general_ci',
    `partyPic`      VARCHAR(900)    NULL     DEFAULT 'img/partyPics/default.png' COLLATE 'utf8_general_ci',
    `nation`        VARCHAR(50)     NULL     DEFAULT NULL COLLATE 'utf8_general_ci',
    `name`          VARCHAR(60)     NULL     DEFAULT NULL COLLATE 'utf8_general_ci',
    `initialEcoPos` DOUBLE          NULL     DEFAULT NULL,
    `initialSocPos` DOUBLE          NULL     DEFAULT NULL,
    `ecoPos`        DOUBLE          NULL     DEFAULT NULL,
    `socPos`        DOUBLE          NULL     DEFAULT NULL,
    `partyRoles`    MEDIUMTEXT      NULL     DEFAULT NULL COLLATE 'utf8_general_ci',
    `discord`       VARCHAR(16)     NULL     DEFAULT '0' COLLATE 'utf8_general_ci',
    `partyTreasury` DOUBLE          NULL     DEFAULT '0',
    `fees`          DOUBLE UNSIGNED NOT NULL DEFAULT '0',
    `votes`         INT(11)         NULL     DEFAULT '250',
    PRIMARY KEY (`id`) USING BTREE
)
    COMMENT ='table for political parties'
    COLLATE = 'utf8_general_ci'
    ENGINE = InnoDB
    AUTO_INCREMENT = 14
;
CREATE TABLE `demoPositions`
(
    `id`     INT(11) NOT NULL AUTO_INCREMENT,
    `demoID` INT(11) NULL DEFAULT NULL,
    `type`   TEXT    NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
    `-5`     FLOAT   NULL DEFAULT NULL,
    `-4`     DOUBLE  NULL DEFAULT NULL,
    `-3`     DOUBLE  NULL DEFAULT NULL,
    `-2`     DOUBLE  NULL DEFAULT NULL,
    `-1`     DOUBLE  NULL DEFAULT NULL,
    `0`      DOUBLE  NULL DEFAULT NULL,
    `1`      DOUBLE  NULL DEFAULT NULL,
    `2`      DOUBLE  NULL DEFAULT NULL,
    `3`      DOUBLE  NULL DEFAULT NULL,
    `4`      DOUBLE  NULL DEFAULT NULL,
    `5`      FLOAT   NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
)
    COLLATE = 'latin1_swedish_ci'
    ENGINE = InnoDB
    AUTO_INCREMENT = 1801
;
CREATE TABLE `demographics`
(
    `demoID`     INT(11)    NOT NULL AUTO_INCREMENT,
    `State`      TEXT       NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
    `Race`       TEXT       NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
    `Population` BIGINT(20) NULL DEFAULT NULL,
    `Gender`     TEXT       NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
    `EcoPosMean` DOUBLE     NULL DEFAULT '0',
    `SocPosMean` DOUBLE     NULL DEFAULT '0',
    PRIMARY KEY (`demoID`) USING BTREE
)
    COLLATE = 'latin1_swedish_ci'
    ENGINE = InnoDB
    ROW_FORMAT = COMPACT
    AUTO_INCREMENT = 901
;
CREATE TABLE `countries`
(
    `id`   INT(11)      NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
    PRIMARY KEY (`id`) USING BTREE
)
    COLLATE = 'utf8_general_ci'
    ENGINE = InnoDB
    AUTO_INCREMENT = 2
;
