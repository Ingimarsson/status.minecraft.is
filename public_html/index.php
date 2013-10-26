<!doctype html>
<html>
<head>

<meta charset="utf-8"/>
<meta http-equiv="refresh" content="60">
<title>Status.minecraft.is</title>
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
<h1>Status.minecraft.is</h1>
<?php
$include_path = "../";

set_include_path($include_path);

require('src/database.class.php');
require('config.php');

$mysql = new database(
    sprintf('mysql:host=%s;dbname=%s', 
        $config['mysql']['host'], 
        $config['mysql']['dbname']
    ), 
    $config['mysql']['username'], 
    $config['mysql']['password'], 
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

if(isset($_GET['player'])){
    $player = $mysql->fetch("SELECT COUNT(*) *5 AS `time_spent`, `minequery_servers`.`ip` AS `ip`, `minequery_players`.`player` AS `player`, MAX(`minequery`.`date`) AS `last_seen`
        FROM `minequery_servers`
        LEFT JOIN `minequery` ON `minequery`.`server_id` = `minequery_servers`.`id`                                                   
        LEFT JOIN `minequery_players` ON `minequery_players`.`server_status_id` = `minequery`.`id`
        WHERE `minequery_players`.`player` = ?
        ORDER BY `minequery`.`id` DESC 
        LIMIT 1", 
        
        array($_GET['player'])
    );
    
    printf("<p><a href='/'>Forsíða</a> &gt; Spilari &gt; <a href='/?player=%s'>%s</a></p>"
		, urlencode($_GET['player'])
		, htmlentities($_GET['player'])
	);
   
    if($player['time_spent']==0){
        echo "<div class='alert alert-danger'>Enginn spilari með þessu nafni hefur sést spila á íslenskum serverum</div>";
    }

    printf("<div class='row'><div class='col-lg-6'><h2>%s</h2>"
		, htmlentities($_GET['player'])
	);
    
    if(strtotime($player['last_seen'])<time()-600){ //If server was online less than ten minutes ago.
        $status = "<span class='label label-danger'>Offline</span>";
    } else {
        $status = "<span class='label label-success'>Online</span>";
    }
    
    printf("<table width=500>
        <tr><td>Klukkustundir</td><td>%s klst</td></tr>
        <tr><td>Status</td><td>%s</td></tr>
        <tr><td>Síðast online</td><td>%s</td></tr>
        <tr><td>Síðasti server</td><td>%s</td></tr>
        </table>", 
        
        floor($player['time_spent']/60), 
        $status, 
        $player['last_seen'], 
        $player['ip']

    );

    echo "<h2>Serverar</h2>";

    $servers = $mysql->fetchall("SELECT `minequery_servers`.`ip`, COUNT(*) *5 AS `time_spent`
        FROM `minequery_players`
        INNER JOIN `minequery_servers` ON `minequery_servers`.`id`=`minequery_players`.`server_id`
        WHERE `minequery_players`.`player`=?
        GROUP BY `minequery_players`.`server_id`
        ORDER BY COUNT(*) DESC;",

        array($_GET['player'])
    );

    echo "<table width=500><tr><th>Server</th><th>Klukkustundir</th></tr>";

    foreach($servers as $server){
        printf("<tr>
            <td>%s</td>
            <td>%s klst</td>
        </tr>",
        
            $server['ip'],
            floor($server['time_spent']/60)
        );
    }

    echo "</table></div><div class='col-lg-6'><br/><br/><br/>";

    printf("<img src='https://minotar.net/avatar/%s/256'/>"
		, urlencode($_GET['player'])
	);

    echo "</div></div>";

} elseif(isset($_GET['server'])){
    $server = $mysql->fetch("SELECT `minequery_servers`.`ip`, `minequery`.* 
        FROM `minequery` 
        INNER JOIN `minequery_servers` ON `minequery_servers`.`id`=`minequery`.`server_id` 
        WHERE `server_id`=? 
        ORDER BY `date` 
        DESC LIMIT 1;", 
    
        array($_GET['server'])
    );
    
    if(!$server){
        echo "<div class='alert alert-danger'>Server með þessu ID er ekki í gagnagrunninum</div>";
    }

    printf("<p><a href='/'>Forsíða</a> &gt; Server &gt; <a href='/?server=%s'>%s</a></p>"
		, urlencode($_GET['server'])
		, $server['ip']
	);
    
    echo "<div class='row'><div class='col-lg-6'>";

    echo "<h2>{$server['ip']}</h2>";
    
    if(strtotime($server['date'])<time()-600){ //If server was online less than ten minutes ago.
        $status = "<span class='label label-danger'>Offline</span>";
    } else {
        $status = "<span class='label label-success'>Online</span>";
    }
   
    printf("<table>
        <tr>
        <tr><td>Staða</td><td>%s</td></tr>
        <tr><td>Titill</td><td>%s</td></tr>
        <tr><td>Síðast uppi</td><td>%s</td></tr>
        <tr><td>Spilarar</td><td>%s / %s</td></tr>
        <tr><td>Minecraft útgáfa</td><td>%s</td></tr>
        <tr><td>Gamemode</td><td>%s</td></tr>
        <tr><td>Hugbúnaður</td><td>%s</td></tr>
        </table>\n\n",
        
        $status,
        preg_replace('/§[0-9a-f]{1}/', '', $server['server_motd']),
        $server['date'],
        $server['server_players'],
        $server['server_max_players'],
        $server['server_version'],
        $server['server_gamemode'],
        $server['server_software']
    );
    
     echo "<h2>Plugins</h2>\n";

    $plugins = $mysql->fetchall("SELECT * 
        FROM `minequery_plugins` 
        WHERE `server_status_id`=?;", 
        
        array($server['id'])
    );

    echo "<table>\n";

    foreach($plugins as $plugin){
        echo "<tr><td>{$plugin['plugin']}</td></tr>\n";
    }

    echo "</table>\n";

    echo "</div><div class='col-lg-6'>";

    echo "<h2>Spilarar</h2>\n";

    $players = $mysql->fetchall("SELECT `minequery_players`.`player` AS `player`,
        COUNT(`minequery_players`.`player`) * 5 AS `time_spent`
        FROM `minequery_servers`           
        LEFT JOIN `minequery` ON `minequery`.`server_id` = `minequery_servers`.`id`
        LEFT JOIN `minequery_players` ON `minequery_players`.`server_id` = `minequery`.`id`
        WHERE `minequery_players`.`player` IN (SELECT player FROM minequery_players WHERE server_status_id=?)
        AND minequery_players.server_id=?
        GROUP BY `minequery_players`.`player`
        ORDER BY `time_spent` DESC", 
        
        array($server['id'], $_GET['server'])
    );

    echo "<table width=400><tr><th witdh=30></th><th>Spilari</th><th>Klukkustundir</th></tr>\n";

    foreach($players as $player){
        $time_spent = floor($player['time_spent']/60);

        echo "<tr><td><img src='https://minotar.net/avatar/{$player['player']}/16'/></td><td><a href='?player={$player['player']}'>{$player['player']}</a></td><td>$time_spent klst</td></tr>\n";

    }

    echo "</table></div></div>";

} else {
    $servers = $mysql->fetchall("SELECT `minequery_servers`.`ip`, `minequery`.* 
        FROM `minequery` 
        INNER JOIN `minequery_servers` ON `minequery_servers`.`id`=`minequery`.`server_id`
        WHERE `minequery`.`id` IN (
            SELECT MAX(`id`) 
            FROM `minequery` 
            GROUP BY `server_id`
        );"
    );

    echo "<p><b>Staða íslenskra minecraft servera</b></p>";

    echo "<h2>Serverar</h2>\n";

    echo "<table width=1200>
        <tr>
            <th>Server</th>
            <th>Staða</th>
            <th>Titill</th>
            <th>Síðast uppi</th>
            <th>Spilarar</th>
            <th>Útgáfa</th>
            <th>Gamemode</th>
        </tr>\n";

    foreach($servers as $server){
        if(strtotime($server['date'])<time()-600){ //If server was online less than ten minutes ago.
            $status = "<span class='label label-danger'>Offline</span>";
        } else {
            $status = "<span class='label label-success'>Online</span>";
        }

        $server['server_motd'] = preg_replace('/§[0-9a-f]{1}/', '', $server['server_motd']); //Remove color codes from MOTD.

        printf("<tr>
            <td><a href='?server=%s'>%s</a></td>
            <td>%s</td>
            <td>%s</td>
            <td>%s</td>
            <td>%s / %s</td>
            <td>%s</td>
            <td>%s</td>
        </tr>\n",

            $server['server_id'],
            $server['ip'],
            $status,
            $server['server_motd'],
            $server['date'],
            $server['server_players'],
            $server['server_max_players'],
            $server['server_version'],
            $server['server_gamemode']
        );
    }

    echo "</table>\n";

    echo "<h2>Finna spilara</h2>
    <div class='col-lg-4'>
        <form action='/'>
            <div class='input-group'>
                <input type='text' name='player' placeholder='Nafn spilara' class='form-control'>
                <span class='input-group-btn'>
                    <button class='btn btn-primary' type='submit'>Leita</button>
                </span>
            </div>
        </form>
    </div><br/><br/>";

    echo "<h2>Virkustu spilararnir</h2>\n";
    echo "<table width=400><tr><th width=30></th><th>Spilari</th><th>Klukkustundir</th></tr>\n";

    $players = $mysql->fetchall('SELECT `player`, count(*) *5 AS `minutes` 
        FROM `minequery_players` 
        GROUP BY `player` 
        ORDER BY COUNT(*) DESC 
        LIMIT 10;'
    );

    foreach($players as $player){
        $hours = floor($player['minutes']/60);

        printf("<tr>
            <td><img src='https://minotar.net/avatar/%s/16'/></td>
            <td><a href='?player=%s'>%s</a></td>
            <td>%s klst</td>
        </tr>\n",
        
            $player['player'],
            $player['player'],
            $player['player'],
            $hours
        );
    }

    echo "</table>\n";

}

?>

<div id='footer' style='margin: 20px 0px 30px 0px;'>
    <br/>
</div>

</div>
</body>
</html>
