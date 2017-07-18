<?php
/*

 */
include_once plugin_dir_path( __FILE__ ).'/pollwidget.php';

/**
 * Classe vote
 * utilitaire de poll_plugin
 */
class Vote
{
    /**
     * Constructeur
     */
    public function __construct()
    {
    add_action('widgets_init', function(){register_widget('Poll_Widget');});
    }

    /**
     * Fonction de base
     */
    public function uneFonction()
    {
    }

}


