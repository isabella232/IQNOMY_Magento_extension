<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
$installer = $this;
 
$installer->startSetup();
 
$installer->run("    
    CREATE TABLE IF NOT EXISTS {$this->getTable('iqnomy_extension_banners')} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(150) NOT NULL,
      `container_id` int(11) NOT NULL,
      `html_id` varchar(150) NOT NULL,
      `is_enabled` tinyint(1) NOT NULL,
      `is_iqnomy_banner` tinyint(1) NOT NULL,
      `height` int(11) NOT NULL,
      `animation_type` varchar(20) NOT NULL,
      `duration` int(11) NOT NULL,
      `pause` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `container_id` (`container_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

    CREATE TABLE IF NOT EXISTS {$this->getTable('iqnomy_extension_subscriptions')} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `email` varchar(255) NOT NULL,
      `hash` varchar(255) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

    CREATE TABLE IF NOT EXISTS {$this->getTable('iqnomy_extension_templates')} (
        `id` int(11) NOT NULL,
        `template` text NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  ");
    
$installer->getConnection()->addColumn($this->getTable('iqnomy_extension_banners'), "is_newsletter", "tinyint(1) NOT NULL");
 
  $installer->endSetup();