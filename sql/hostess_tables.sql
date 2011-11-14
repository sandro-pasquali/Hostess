

CREATE TABLE `hostess_assignments` (
  `id` int(11) NOT NULL auto_increment,
  `moderatorId` int(11) NOT NULL default '0',
  `hostessId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `hostess_history` (
  `id` int(11) NOT NULL auto_increment,
  `messageId` int(11) NOT NULL default '0',
  `responseId` int(11) NOT NULL default '0',
  `hostesslId` int(11) NOT NULL default '0',
  `moderatorId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `messages` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `sender` bigint(8) unsigned NOT NULL default '0',
  `recipient` bigint(8) unsigned NOT NULL default '0',
  `text` mediumtext NOT NULL,
  `new` tinyint NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `sender` (`sender`),
  KEY `recipient` (`recipient`),
  KEY `date` (`date`),
  KEY `new` (`new`)
) TYPE=MyISAM;

CREATE TABLE `moderator_payouts` (
  `moderatorId` int(11) NOT NULL default '0',
  `payout` decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY  (`moderatorId`)
) TYPE=MyISAM;

CREATE TABLE `moderators` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(32) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `status` tinyint not null default 1,
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


CREATE TABLE `profiles` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(32) default NULL,
  `password` varchar(32) NOT NULL default '',
  `gender` tinyint not null default 1,
  `status` tinyint not null default 0,
  `last_login` datetime,
  `state` varchar(64) not null default '',
  `country` varchar(64) not null default '',
  `zip` char(10),
  `lat` decimal(10,6),
  `lon` decimal(10,6),
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `state` (`state`),
  KEY `country` (`country`),
  KEY `gender` (`gender`),
  KEY `status` (`status`),
  KEY `last_login` (`last_login`),
  KEY `lat` (`lat`),
  KEY `lon` (`lon`)
) TYPE=MyISAM;



