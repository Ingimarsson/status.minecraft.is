CREATE TABLE `minequery_servers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(256) NOT NULL,
    `ip` varchar(256) NOT NULL,
    `port` int(11) NOT NULL,
    `query_port` int(11) NOT NULL,
    `display_plugins` int(11) NOT NULL,
    `registrant` varchar(256),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
