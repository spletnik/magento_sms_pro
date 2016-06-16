<?php
$installer = $this;

$installer->startSetup();

$this->run("

     DROP TABLE IF EXISTS `{$this->getTable('smsnotify/smsnotify')}`;
    CREATE TABLE {$this->getTable('smsnotify/smsnotify')} (
      `smsnotify_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `store_id` smallint(5) unsigned NOT NULL,
      `template` VARCHAR(180),
      `status` TINYINT(1) NOT NULL DEFAULT 0,
      `event` INT(10),
      `sendingto` INT(10),
      PRIMARY KEY (`smsnotify_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


     DROP TABLE IF EXISTS {$this->getTable('smsnotify/smses')};
    CREATE TABLE {$this->getTable('smsnotify/smses')} (
        `sms_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `store_id` SMALLINT(5) UNSIGNED NOT NULL,
        `to` VARCHAR(40) NOT NULL,
        `from` VARCHAR(40),
        `content` VARCHAR(180),
        `status` TINYINT(1) NOT NULL DEFAULT 0,
        `message_id` VARCHAR(255),
        `error_number` INT(10) UNSIGNED,
        `error_description` VARCHAR(255),
        `cost` VARCHAR(40),
        `created_at` DATETIME NULL,
        `updated_at` DATETIME NULL,
        PRIMARY KEY  (`sms_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$this->run("
    ALTER TABLE `{$this->getTable('smsnotify/smses')}`
        ADD KEY `FK_SMSES_STORE` (`store_id`),
        ADD CONSTRAINT `FK_SMSES_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();