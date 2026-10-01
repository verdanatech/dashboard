<?php

function plugin_dashboard_install()
{

	global $DB, $LANG;

	if (! $DB->TableExists("glpi_plugin_dashboard_count")) {
		$query = "CREATE TABLE `glpi_plugin_dashboard_count` 
        (`type` INTEGER , `id` INTEGER, `quant` INTEGER, PRIMARY KEY AUTO_INCREMENT (`id`))
						ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; ";

		$DB->doQuery($query) or die("error creating glpi_plugin_dashboard_count " . $DB->error());

		$insert = "INSERT INTO glpi_plugin_dashboard_count (type,quant) VALUES ('1','1')";
		$DB->doQuery($insert);
	}

	//map
	if (! $DB->TableExists("glpi_plugin_dashboard_map")) {
		$query_map = "CREATE TABLE IF NOT EXISTS `glpi_plugin_dashboard_map` (
	  `id` int(4) NOT NULL AUTO_INCREMENT,
	  `entities_id` int(4) NOT NULL,
	  `location` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
	  `lat` float NOT NULL,
	  `lng` float NOT NULL,
	  PRIMARY KEY (`id`,`entities_id`)) 
	  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

		$DB->doQuery($query_map) or die("error creating table glpi_plugin_dashboard_map " . $DB->error());
	}


	//configs

	if (! $DB->TableExists("glpi_plugin_dashboard_config")) {

		$query_conf = "CREATE TABLE IF NOT EXISTS `glpi_plugin_dashboard_config` (
	  `id` int(4) NOT NULL AUTO_INCREMENT,
	  `name` varchar(50) NOT NULL,
	  `value` varchar(25) NOT NULL,
	  `users_id` varchar(25) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`,`name`,`value`,`users_id`),
	  UNIQUE KEY `name` (`name`,`users_id`),
	  KEY `name_2` (`name`,`users_id`)) 
	  ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";

		$DB->doQuery($query_conf) or die("error creating table glpi_plugin_dashboard_config " . $DB->error());
	}


	if ($DB->TableExists("glpi_plugin_dashboard_count")) {

		$query_alt = "ALTER TABLE `glpi_plugin_dashboard_count` DROP PRIMARY KEY, ADD PRIMARY KEY(`type`,`id`); ";
		$DB->doQuery($query_alt) or die("error update table glpi_plugin_dashboard_count primary key " . $DB->error());
	}


	if ($DB->TableExists("glpi_plugin_dashboard_config")) {

		$query_alt = "ALTER TABLE glpi_plugin_dashboard_config MODIFY value varchar(125); ";
		$DB->doQuery($query_alt) or die("error alter table glpi_plugin_dashboard_config value size" . $DB->error());

		//Config entities
		$query_ent = "SELECT users_id FROM glpi_plugin_dashboard_config WHERE name = 'entity' AND value = '-1' ";
		$result = $DB->doQuery($query_ent) or die("error alter table glpi_plugin_dashboard_config value size" . $DB->error());

		while ($row = $DB->fetchAssoc($result)) {
			$query = "UPDATE glpi_plugin_dashboard_config SET value = '' WHERE name = 'entity' AND users_id = " . $row['users_id'] . " ";
			$DB->doQuery($query) or die("error updating table glpi_plugin_dashboard_config entity value" . $DB->error());
		}
	}

	if ($DB->TableExists("glpi_plugin_dashboard_map")) {
		$query_alt = "ALTER TABLE `glpi_plugin_dashboard_map` ADD UNIQUE (`location`); ";
		$DB->doQuery($query_alt) or die("error update table glpi_plugin_dashboard_map primary key " . $DB->error());
	}

	return true;
}

function plugin_dashboard_redefine_menus($menu)
{
	if (empty($menu)) {
		return $menu;
	}

	if (empty($menu['servicetic'])) {
		$menu['servicetic'] = [
			'title' => __('ST-Módulos'),
			'icon'  => __('fa-solid fa-list'),
			'types'  => [],
			'content' => [],
		];
	}

	$menu['servicetic']['content']['dashboard'] = [
		'title' => __('ST-Dashboard'),
		'page'  => '/plugins/dashboard/public/index.php',
		'icon'  => 'fas fa-tachometer-alt',
	];


	// Sort menu by title
	usort($menu['servicetic']['content'], function ($a, $b) {
		return strcmp($a['title'], $b['title']);
	});

	return $menu;
}


function plugin_dashboard_uninstall()
{

	global $DB;

	$drop_count = "DROP TABLE glpi_plugin_dashboard_count";
	$DB->doQuery($drop_count);

	$drop_map = "DROP TABLE glpi_plugin_dashboard_map";
	$DB->doQuery($drop_map);

	$drop_config = "DROP TABLE glpi_plugin_dashboard_config";
	$DB->doQuery($drop_config);

	$restore_mode = "SET sql_mode=(SELECT CONCAT(@@sql_mode,',ONLY_FULL_GROUP_BY'));";
	$DB->doQuery($restore_mode);

	return true;
}
