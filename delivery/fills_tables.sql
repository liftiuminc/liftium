CREATE TABLE `fills_minute` (
  `tag_id` int(11) NOT NULL,
  `minute` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `attempts` int(10) unsigned DEFAULT '0',
  `loads` int(10) unsigned DEFAULT '0',
  `rejects` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`tag_id`,`minute`)
);
CREATE TABLE `fills_hour` (
  `tag_id` int(11) NOT NULL,
  `hour` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `attempts` int(10) unsigned DEFAULT '0',
  `loads` int(10) unsigned DEFAULT '0',
  `rejects` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`tag_id`,`hour`)
);
CREATE TABLE `fills_day` (
  `tag_id` int(11) NOT NULL,
  `day` date NOT NULL DEFAULT '0000-00-00',
  `attempts` int(10) unsigned DEFAULT '0',
  `loads` int(10) unsigned DEFAULT '0',
  `rejects` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`tag_id`,`day`)
);

