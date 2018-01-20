<?php

/**
 * Fired during plugin activation
 *
 * @link       otrain.com.au
 * @since      1.0.0
 *
 * @package    Ot_feedback
 * @subpackage Ot_feedback/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ot_feedback
 * @subpackage Ot_feedback/includes
 * @author     PhiHai <nguyenphihai1910@gmail.com>
 */
class Ot_feedback_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	 
	public static function activate() {
		global $wpdb;
		$tableName = 'wp_scoreboard';
		$wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				  `user` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
				  `score` int(5) DEFAULT '0',
				  `ip` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
				  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
				  `url` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
				  `comments` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `agent` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
				  PRIMARY KEY (`id`)
			) DEFAULT CHARACTER SET = utf8 DEFAULT COLLATE utf8_general_ci;");
	}

}
