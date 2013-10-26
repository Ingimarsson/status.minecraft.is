CREATE TABLE `minequery_plugins` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `server_id` int(11) NOT NULL,
    `server_status_id` int(11) NOT NULL,
    `plugin` varchar(64),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
