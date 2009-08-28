-- Note you will need a dump of the AdEngine tables too:
--  mysqldump -u wikicities -pxxxxxxx -h db4 --database wikicities --tables ad_provider ad_slot ad_slot_override ad_provider_value > adengine.mysql
CREATE DATABASE IF NOT EXISTS athena;
USE athena;

CREATE TABLE IF NOT EXISTS network (
	network_id smallint unsigned not null auto_increment,
	network_name varchar(255),
	notes text,
	enabled enum('Yes', 'No') default 'Yes',
	supports_threshold enum('Yes', 'No') default 'No',
	pay_type enum('Per Click', 'Per Impression') default 'Per Impression',
	guaranteed_fill enum('Yes', 'No') default 'No',
	webui_login_url varchar(255),	
	webui_username varchar(255),	
	webui_password varchar(255),	
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
	tag_id smallint unsigned not null auto_increment,
	tag_name varchar(255),
	notes text,
	enabled enum('Yes', 'No') default 'Yes',
	guaranteed_fill enum('Yes', 'No') default 'No',
	sample_rate tinyint unsigned default '0',
	network_id smallint unsigned, -- if null, then 'tag' will be filled
	auto_update_ecpm enum('Yes', 'No') default 'No',
	reported_date datetime,
	reported_ecpm float unsigned, 
	estimated_cpm float unsigned,
	threshold float unsigned, 
	tier tinyint unsigned not null default '0',
	freq_cap tinyint unsigned default null,
	rej_cap tinyint unsigned default null,
	rej_time smallint unsigned default null,
	tag text, -- mutually exclusive with network_id
	PRIMARY KEY (tag_id),
	KEY (tag_name),
	KEY (network_id)
) ENGINE=InnoDB;

-- Different networks have different tags
CREATE TABLE IF NOT EXISTS tag_option (
	tag_id smallint unsigned not null,
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

CREATE TABLE IF NOT EXISTS config_archive (
	config_id int unsigned not null auto_increment,
	config_date datetime,
	targeting_profile varchar(255),
	config text,
	PRIMARY KEY (config_id)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS fills_minute (
	tag_id smallint unsigned not null,
	minute datetime,
	attempts int unsigned default 0,
	loads int unsigned default 0,
	rejects int unsigned default 0,
	load_pl_0 int default 0,
	load_pl_1 int default 0,
	load_pl_2 int default 0,
	load_pl_3 int default 0,
	load_pl_4 int default 0,
	load_pl_5 int default 0,
	reject_pl_0 int default 0,
	reject_pl_1 int default 0,
	reject_pl_2 int default 0,
	reject_pl_3 int default 0,
	reject_pl_4 int default 0,
	reject_pl_5 int default 0,
	PRIMARY KEY (tag_id, minute),
	KEY (minute)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fills_hour (
	tag_id smallint unsigned not null,
	hour datetime,
	attempts int unsigned default 0,
	loads int unsigned default 0,
	rejects int unsigned default 0,
	load_pl_0 int default 0,
	load_pl_1 int default 0,
	load_pl_2 int default 0,
	load_pl_3 int default 0,
	load_pl_4 int default 0,
	load_pl_5 int default 0,
	reject_pl_0 int default 0,
	reject_pl_1 int default 0,
	reject_pl_2 int default 0,
	reject_pl_3 int default 0,
	reject_pl_4 int default 0,
	reject_pl_5 int default 0,
	PRIMARY KEY (tag_id, hour),
	KEY (hour)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fills_day (
	tag_id smallint unsigned not null,
	day date,
	attempts int unsigned default 0,
	loads int unsigned default 0,
	rejects int unsigned default 0,
	load_pl_0 int default 0,
	load_pl_1 int default 0,
	load_pl_2 int default 0,
	load_pl_3 int default 0,
	load_pl_4 int default 0,
	load_pl_5 int default 0,
	reject_pl_0 int default 0,
	reject_pl_1 int default 0,
	reject_pl_2 int default 0,
	reject_pl_3 int default 0,
	reject_pl_4 int default 0,
	reject_pl_5 int default 0,
	PRIMARY KEY (tag_id, day),
	KEY (day)
) ENGINE=InnoDB;

CREATE VIEW IF NOT EXISTS big_movers_minute (time_slot, tag_id, fill_rate, attempts) AS SELECT IF(minute>(NOW()-INTERVAL 5 MINUTE),'5m', IF(minute>(NOW()-INTERVAL 10 MINUTE),'10m', IF(minute>(NOW()-INTERVAL 15 MINUTE),'15m', IF(minute>(NOW()-INTERVAL 30 MINUTE),'30m','other')))) AS time_slot, tag_id, SUM(loads)/SUM(attempts) AS fill_rate, SUM(attempts) AS attempts FROM fills_minute WHERE minute>(NOW()-INTERVAL 30 MINUTE) GROUP BY tag_id, time_slot;
CREATE VIEW IF NOT EXISTS big_movers_hour (time_slot, tag_id, fill_rate, attempts) AS SELECT IF(hour>=(NOW()-INTERVAL 2 HOUR),'1h', IF(hour>=(NOW()-INTERVAL 3 HOUR),'2h','other')) AS time_slot, tag_id, SUM(loads)/SUM(attempts) AS fill_rate, SUM(attempts) AS attempts FROM fills_hour WHERE hour>(NOW()-INTERVAL 3 HOUR) GROUP BY tag_id, time_slot;
CREATE VIEW IF NOT EXISTS big_movers_day (time_slot, tag_id, fill_rate, attempts) AS SELECT IF(day>=(NOW()-INTERVAL 1 DAY),'1d', IF(day>=(NOW()-INTERVAL 2 DAY),'2d','other')) AS time_slot, tag_id, SUM(loads)/SUM(attempts) AS fill_rate, SUM(attempts) AS attempts FROM fills_day WHERE day>(NOW()-INTERVAL 2 DAY) GROUP BY tag_id, time_slot;
