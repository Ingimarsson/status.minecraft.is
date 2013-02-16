CREATE TABLE `minequery` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `server_id` int(11) NOT NULL,
    `query_response` int(11) NOT NULL,
    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `server_motd` varchar(100) NOT NULL,
    `server_version` varchar(100),
    `server_online_players` int(11) NOT NULL,
    `server_max_players` int(11) NOT NULL,
    `server_gamemode` varchar(100),
    `server_software` varchar(100),
    `server_plugins` varchar(1000),
    `server_players` varchar(1000),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
