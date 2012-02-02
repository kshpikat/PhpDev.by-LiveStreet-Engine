CREATE TABLE IF NOT EXISTS `prefix_loginza_identities` (
  `user_id` int(10) unsigned NOT NULL,
  `identity` varchar(255) NOT NULL,
  PRIMARY KEY  (`identity`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;