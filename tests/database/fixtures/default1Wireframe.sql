USE sgql_unittests_data_1;

CREATE TABLE IF NOT EXISTS `customers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `vip` TINYINT(1) NOT NULL DEFAULT '0',
    `type` TINYINT(1) NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cost` DOUBLE NOT NULL,
    `shipped` TINYINT(1) NOT NULL DEFAULT '0',
    `customer` INT UNSIGNED NULL,
    PRIMARY KEY (`id`)
);
