CREATE TABLE `minequery` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `server_id` int(11) NOT NULL,
    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `server_motd` varchar(100) NOT NULL,
    `server_version` varchar(100),
    `server_players` int(11) NOT NULL,
    `server_max_players` int(11) NOT NULL,
    `server_gamemode` varchar(100),
    `server_software` varchar(100),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
