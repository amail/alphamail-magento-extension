<?php

	$this->startSetup();

	Mage::log("Creating tables for AlphaMail...");

	Mage::log("Dropping any existing tables...");
	$this->run("DROP TABLE IF EXISTS `{$this->getTable('alphamail_send_log')}`");
	$this->run("DROP TABLE IF EXISTS `{$this->getTable('alphamail_event_log')}`");
	$this->run("DROP TABLE IF EXISTS `{$this->getTable('alphamail_project_map')}`");

	Mage::log("Creating Send Log table");

	$this->run(
	    "CREATE TABLE `{$this->getTable('alphamail_send_log')}` (
	      `send_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	      `am_queue_id` varchar(128) DEFAULT NULL,
	      `template_name` varchar(128) DEFAULT NULL,
	      `raw_payload` text,
	      `status` tinyint(2) NOT NULL DEFAULT '0',
	      `sent_at` timestamp NULL DEFAULT NULL,
	      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	      PRIMARY KEY (`send_id`)
	      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
	);

	Mage::log("Creating Event Log table");

	$this->run(
	    "CREATE TABLE `{$this->getTable('alphamail_event_log')}` (
	        `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	        `send_id` int(10) DEFAULT NULL,
	        `message` varchar(4096) NOT NULL,
	        `type` tinyint(2) NOT NULL,
	        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	        PRIMARY KEY (`event_id`),
	        KEY `created_at` (`created_at`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
	);

	Mage::log("Creating Project Map table");

	$this->run(
	    "CREATE TABLE `{$this->getTable('alphamail_project_map')}` (
	        `project_map_id` int(10) NOT NULL AUTO_INCREMENT,
	        `am_project_id` int(10) NOT NULL DEFAULT '0',
	        `template_name` varchar(128) NOT NULL,
	        PRIMARY KEY (`project_map_id`),
	        UNIQUE KEY `template_name` (`template_name`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
	);

	$this->endSetup();

?>