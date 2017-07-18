<?php
/*
Plugin Name: Poll
 */

include_once plugin_dir_path( __FILE__ ).'/vote.php';
#include_once plugin_dir_path( __FILE__ ).'/pollwidget.php';

/**
 * Classe Poll_Plugin
 * Déclare le plugin
 */
class Poll_Plugin
{
    /**
     * Constructeur
     */
    public function __construct()
    {
          register_activation_hook(__FILE__, array('Poll_Plugin', 'install'));
          new Vote();
          # ne 'sapplique qu'à la désactivation du pluggin
          #register_deactivation_hook()
          # ne s'active que s'il y a suppression du pluggin
          register_uninstall_hook(__FILE__, array('Poll_Plugin', 'uninstall'));          
    }

    /**
     * Fonction d'installation
     */
    public function install()
    {
          global $wpdb;
          $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}poll_options (id INT AUTO_INCREMENT PRIMARY KEY, label VARCHAR(255) NOT NULL);");
          $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}poll_results (option_id INT NOT NULL, total INT NOT NULL);");
    }

    /**
     * Fonction de désinstallation
     * Suppression des tables du sondage
     */
    public function uninstall()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}poll_options;");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}poll_results;");
    }
}

new Poll_Plugin();
