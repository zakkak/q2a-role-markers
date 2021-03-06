<?php

/*
        Plugin Name: Role Markers
        Plugin URI: https://github.com/NoahY/q2a-role-markers
        Plugin Update Check URI: https://github.com/NoahY/q2a-role-markers/raw/master/qa-plugin.php
        Plugin Description: Adds role markers to avatars and usernames
        Plugin Version: 1.2
        Plugin Date: 2013-10-04
        Plugin Author: NoahY, Foivos S. Zakkak
        Plugin Author URI:
        Plugin License: GPLv2
        Plugin Minimum Question2Answer Version: 1.4
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
			header('Location: ../../');
			exit;
	}

	qa_register_plugin_layer('qa-marker-layer.php', 'Marker Layer');

	qa_register_plugin_module('module', 'qa-marker-admin.php', 'qa_marker_admin', 'Role Markers');

/*
	Omit PHP closing tag to help avoid accidental output
*/
