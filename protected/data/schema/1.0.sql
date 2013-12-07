SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

SET AUTOCOMMIT=0;

START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `fortuna_acl_privilege`;
DROP TABLE IF EXISTS `fortuna_acl_resource`;
DROP TABLE IF EXISTS `fortuna_acl_role`;
DROP TABLE IF EXISTS `fortuna_acl_role_privilege`;
DROP TABLE IF EXISTS `fortuna_article`;
DROP TABLE IF EXISTS `fortuna_article_category`;
DROP TABLE IF EXISTS `fortuna_category`;
DROP TABLE IF EXISTS `fortuna_navigation`;
DROP TABLE IF EXISTS `fortuna_user`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE IF NOT EXISTS `fortuna_acl_privilege` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `acl_resource_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`acl_resource_id`),
  KEY `acl_resource_id` (`acl_resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `fortuna_acl_privilege` VALUES(18, 'account', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(3, 'articles', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(23, 'categories', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(17, 'delete-article', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(25, 'delete-category', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(5, 'delete-role', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(9, 'delete-user', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(26, 'faq', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(1, 'index', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(10, 'navigation', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(2, 'roles', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(22, 'settings', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(16, 'show-article', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(24, 'show-category', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(4, 'show-role', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(8, 'show-user', 1);
INSERT INTO `fortuna_acl_privilege` VALUES(21, 'users', 1);

CREATE TABLE IF NOT EXISTS `fortuna_acl_resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `fortuna_acl_resource` VALUES(1, 'admin');

CREATE TABLE IF NOT EXISTS `fortuna_acl_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `standard` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `fortuna_acl_role` VALUES(1, 'Administrator', 1);


CREATE TABLE IF NOT EXISTS `fortuna_acl_role_privilege` (
  `acl_role_id` int(10) unsigned NOT NULL,
  `acl_privilege_id` int(10) unsigned NOT NULL,
  `allowed` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`acl_role_id`,`acl_privilege_id`),
  KEY `acl_privilege_id` (`acl_privilege_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 1, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 2, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 3, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 4, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 5, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 8, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 9, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 10, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 16, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 17, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 18, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 21, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 22, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 23, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 24, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 25, 1);
INSERT INTO `fortuna_acl_role_privilege` VALUES(1, 26, 1);


CREATE TABLE IF NOT EXISTS `fortuna_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `is_home` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `fortuna_article` VALUES(1, 'Home', 'home', 1339346707, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 1);
INSERT INTO `fortuna_article` VALUES(2, 'Just one cool article', 'just-one-cool-article', 1340221575, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 0);
INSERT INTO `fortuna_article` VALUES(3, 'Definately cool', 'definately-cool', 1340222402, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 0);

CREATE TABLE IF NOT EXISTS `fortuna_article_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `article_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`,`article_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `fortuna_article_category` VALUES(6, 14, 2);
INSERT INTO `fortuna_article_category` VALUES(3, 15, 2);
INSERT INTO `fortuna_article_category` VALUES(7, 15, 3);
INSERT INTO `fortuna_article_category` VALUES(5, 16, 2);
INSERT INTO `fortuna_article_category` VALUES(2, 17, 1);
INSERT INTO `fortuna_article_category` VALUES(4, 17, 2);

CREATE TABLE IF NOT EXISTS `fortuna_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `fortuna_category` VALUES(14, 'Test category', 'test-category');
INSERT INTO `fortuna_category` VALUES(15, 'About the World', 'about-the-world');
INSERT INTO `fortuna_category` VALUES(16, 'History Stuff', 'history-stuff');
INSERT INTO `fortuna_category` VALUES(17, 'Everything else', 'everything-else');


CREATE TABLE IF NOT EXISTS `fortuna_navigation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) unsigned DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `article_id` (`article_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `fortuna_navigation` VALUES(1, 1, NULL, NULL);
INSERT INTO `fortuna_navigation` VALUES(2, NULL, 14, NULL);
INSERT INTO `fortuna_navigation` VALUES(3, 1, NULL, 2);
INSERT INTO `fortuna_navigation` VALUES(4, 2, NULL, 2);
INSERT INTO `fortuna_navigation` VALUES(5, NULL, 15, NULL);
INSERT INTO `fortuna_navigation` VALUES(6, 2, NULL, 5);
INSERT INTO `fortuna_navigation` VALUES(7, NULL, 17, NULL);
INSERT INTO `fortuna_navigation` VALUES(8, 3, NULL, 7);


CREATE TABLE IF NOT EXISTS `fortuna_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `password` char(32) NOT NULL,
  `firstname` varchar(150) NOT NULL,
  `lastname` varchar(150) NOT NULL,
  `acl_role_id` int(10) unsigned NOT NULL,
  `password_link` char(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_link` (`password_link`),
  KEY `acl_role_id` (`acl_role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `fortuna_user` VALUES(1, 'admin@fortuna.cms', '16d7a4fca7442dda3ad93c9a726597e4', 'Admin', 'Aministrator', 1, NULL);


ALTER IGNORE TABLE `fortuna_acl_privilege`
  ADD CONSTRAINT `fortuna_acl_privilege_ibfk_1` FOREIGN KEY (`acl_resource_id`) REFERENCES `fortuna_acl_resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER IGNORE TABLE `fortuna_acl_role_privilege`
  ADD CONSTRAINT `acl_role_action_ibfk_1` FOREIGN KEY (`acl_role_id`) REFERENCES `fortuna_acl_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fortuna_acl_role_privilege_ibfk_1` FOREIGN KEY (`acl_privilege_id`) REFERENCES `fortuna_acl_privilege` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER IGNORE TABLE `fortuna_article_category`
  ADD CONSTRAINT `fortuna_article_category_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `fortuna_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fortuna_article_category_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `fortuna_article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER IGNORE TABLE `fortuna_navigation`
  ADD CONSTRAINT `fortuna_navigation_ibfk_7` FOREIGN KEY (`article_id`) REFERENCES `fortuna_article` (`id`),
  ADD CONSTRAINT `fortuna_navigation_ibfk_8` FOREIGN KEY (`category_id`) REFERENCES `fortuna_category` (`id`),
  ADD CONSTRAINT `fortuna_navigation_ibfk_9` FOREIGN KEY (`parent_id`) REFERENCES `fortuna_navigation` (`id`);

ALTER IGNORE TABLE `fortuna_user`
  ADD CONSTRAINT `fortuna_user_ibfk_1` FOREIGN KEY (`acl_role_id`) REFERENCES `fortuna_acl_role` (`id`);
COMMIT;