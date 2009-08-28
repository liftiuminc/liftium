CREATE DATABASE IF NOT EXISTS liftium;
USE liftium;

CREATE TABLE `ad_slot` (
  `as_id` smallint(5) unsigned NOT NULL auto_increment,
  `slot` varchar(50) NOT NULL,
  `skin` varchar(25) NOT NULL,
  `size` varchar(25) default NULL,
  `load_priority` tinyint(3) unsigned default NULL,
  `default_provider_id` tinyint(3) unsigned NOT NULL,
  `default_enabled` enum('Yes','No') default 'Yes',
  PRIMARY KEY  (`as_id`),
  UNIQUE KEY `slot` (`slot`,`skin`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 

CREATE TABLE publisher (
	pub_id int unsigned not null auto_increment,
	contact_name varchar(255),
	contact_email varchar(255),
	website varchar(255),
) ENGINE=InnoDB;

CREATE TABLE publisher_network_options (
	pub_id int unsigned not null,
	network_id smallint unsigned not null,
	option_keyname varchar(25) NOT NULL,
	option_value varchar(255) NOT NULL,
	KEY (pub_id),
	UNIQUE KEY (network_id, option_keyname)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS network (
	network_id smallint unsigned not null auto_increment,
	network_name varchar(255),
	notes text,
	enabled enum('Yes', 'No') default 'Yes',
	supports_threshold enum('Yes', 'No') default 'No',
	pay_type enum('Per Click', 'Per Impression', 'Per Acquisition') default 'Per Impression',
	guaranteed_fill enum('Yes', 'No') default 'No',
	PRIMARY KEY (network_id),
	UNIQUE KEY (network_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS target_network_linking (
	network_id smallint unsigned,
	target_value_id smallint unsigned,
	PRIMARY KEY(network_id, target_value_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS target_tag_linking (
	tag_id smallint unsigned,
	target_value_id smallint unsigned,
	PRIMARY KEY(tag_id, target_value_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS target_key (
	target_key_id smallint unsigned not null auto_increment,
	target_keyname varchar(25) NOT NULL,
	PRIMARY KEY (target_key_id),
	UNIQUE KEY (target_keyname)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS target_value (
	target_value_id smallint unsigned not null auto_increment,
	target_key_id smallint unsigned not null,
	target_keyvalue varchar(25) NOT NULL,
	PRIMARY KEY (target_value_id),
	UNIQUE KEY (target_key_id, target_keyvalue(25))
) ENGINE=InnoDB;
	
CREATE TABLE IF NOT EXISTS tag (
	tag_id int unsigned not null auto_increment,
	pub_id int unsigned not null,
	tag_name varchar(255),
	notes text,
	enabled enum('Yes', 'No') default 'Yes',
	guaranteed_fill enum('Yes', 'No') default 'No',
	sample_rate tinyint unsigned default '0',
	network_id smallint unsigned, -- if null, then 'tag' will be filled
	auto_update_ecpm enum('Yes', 'No') default 'No',
	reported_date datetime,
	reported_ecpm float unsigned, 
	value float unsigned, 
	tier tinyint unsigned not null default '0',
	freq_cap tinyint unsigned default null,
	rej_cap tinyint unsigned default null,
	rej_time smallint unsigned default null,
	tag text, -- mutually exclusive with network_id
	PRIMARY KEY (tag_id),
	KEY (pub_id),
	KEY (tag_name),
	KEY (network_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tag_option (
	tag_id int unsigned not null,
	option_name varchar(25) NOT NULL,
	option_value varchar(255),
	PRIMARY KEY (tag_id,option_name(25)),
	KEY (option_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tag_slot_linking (
	tag_id smallint unsigned not null,
	as_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
	PRIMARY KEY (tag_id,as_id),
	KEY (as_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fills_minute (
	tag_id smallint unsigned not null,
	minute datetime,
	attempts int unsigned default 0,
	loads int unsigned default 0,
	rejects int unsigned default 0,
	PRIMARY KEY (tag_id, minute),
	KEY (minute)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fills_hour (
	tag_id smallint unsigned not null,
	hour datetime,
	attempts int unsigned default 0,
	loads int unsigned default 0,
	rejects int unsigned default 0,
	PRIMARY KEY (tag_id, hour),
	KEY (hour)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fills_day (
	tag_id smallint unsigned not null,
	day date,
	attempts int unsigned default 0,
	loads int unsigned default 0,
	rejects int unsigned default 0,
	PRIMARY KEY (tag_id, day),
	KEY (day)
) ENGINE=InnoDB;
