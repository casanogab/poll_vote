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
          # ne 'sapplique qu'à la désactivation du pluggin JE FLUSH LA TABLE
          #register_deactivation_hook()
          register_deactivation_hook(__FILE__, array('Poll_Plugin', 'dropTables'));
          # ne s'active que s'il y a suppression du pluggin
          register_uninstall_hook(__FILE__, array('Poll_Plugin', 'dropTables'));   
          #ajout d'un menu
          add_action('admin_menu', array($this, 'add_admin_menu'),11);  
          add_action('monHookAjoutDeNouvellesOptions', array($this, 'ajoutDeNouvellesOptions'),10);
          add_action('monHookInsertDansPollResultsEtPollOptions', array($this, 'insertDansPollResultsEtPollOptions'),9);     
    }

    /**
     * Fonction d'installation
     */
    public function install()
    {
          global $wpdb;
          $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}poll_options (id INT AUTO_INCREMENT PRIMARY KEY, label VARCHAR(255) NOT NULL);");
          $wpdb->insert("{$wpdb->prefix}poll_options", array('id' => '1', 'label' => 'oranges' ));
          $wpdb->insert("{$wpdb->prefix}poll_options", array('id' => '2', 'label' => 'pommes' ));
          $wpdb->insert("{$wpdb->prefix}poll_options", array('id' => '3', 'label' => 'poires' ));
          $wpdb->insert("{$wpdb->prefix}poll_options", array('id' => '4', 'label' => 'pêches' ));# a voir si le chapeau cause du trouble
          
          $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}poll_results (option_id INT NOT NULL, total INT NOT NULL);");
          for ($cpt = 1; $cpt <= 4; $cpt++) {
            $wpdb->insert("{$wpdb->prefix}poll_results", array('option_id' => $cpt, 'total' => '0' ));
          }
    }

    /**
     * Fonction de dropTables
     * Suppression des tables du sondage
     */
    public function dropTables()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}poll_options;");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}poll_results;");
    }
    public function add_admin_menu()
    {
      add_menu_page('lepluginPoll', 'Pollplugin', 'manage_options', 'poll', array($this, 'menu_html'));
    }
    public function menu_html()
    {
    echo '<h1>'.get_admin_page_title().'</h1>';
    echo '<p>Bienvenue sur la page d\'accueil du plugin Poll</p>';
          do_action('monHookAjoutDeNouvellesOptions');
    }
    public function ajoutDeNouvellesOptions()
   {
     ?>
      <form action="" method="post">
          <p>
             <?php
               global $wpdb;
                $totalOptions = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}poll_options" );   
                for ($cpt = 1; $cpt <= $totalOptions ; $cpt++) {
                    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}poll_options WHERE id = '$cpt'"); 
                    echo "<input type=\"text\" name=\"rdb_fruits_admin\" value=\"$row->label\"><br><br>";
                }
                     
              ?>
          Ajouter une nouvelle réponse<input type="text" name="nouveauFruits"><br><br>
          </p>
          <input type="submit" value="envoyer"/>
      </form>
      <?php  
           if(isset($_POST['nouveauFruits'])  && !empty($_POST['nouveauFruits'])){
             $nouveauFruits = $_POST['nouveauFruits']; 
             do_action('monHookInsertDansPollResultsEtPollOptions', $nouveauFruits);
           }    

   }
   public function insertDansPollResultsEtPollOptions($nouveauFruits)
   {
            global $wpdb;
            $totalOptions = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}poll_options" );
            $nouvelID = $totalOptions + 1;
            $wpdb->insert("{$wpdb->prefix}poll_options", array('id' =>  $nouvelID, 'label' => $nouveauFruits ));
            $wpdb->insert("{$wpdb->prefix}poll_results", array('option_id' =>  $nouvelID, 'total' => 0 ));        
    
   }
  
    
}

new Poll_Plugin();
