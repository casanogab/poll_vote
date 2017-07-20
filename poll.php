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
          add_action('admin_menu', array($this, 'add_admin_menu'),20);  
          add_action('monHookAjoutDeNouvellesOptions', array($this, 'ajoutDeNouvellesOptions'));
          add_action('monHookInsertDansPollResultsEtPollOptions', array($this, 'insertDansPollResultsEtPollOptions')); 
          add_action('admin_init', array($this, 'register_settings')); 
          if (isset($_POST['reinitialise'])) {
            $this->dropTables();
            $this->install();
          }   
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

      add_submenu_page('poll', 'Apercu', 'Apercu', 'manage_options', 'poll', array($this, 'menu_html'));
      $hook =add_submenu_page('poll', 'Poll', 'Poll', 'manage_options', 'sousmenu4', array($this, 'menu_html'));
         add_action('load-'.$hook, array($this, 'process_action'));
    }
    public function menu_html()
    {
    echo '<h1>'.get_admin_page_title().'</h1>';
    echo '<p>Bienvenue sur la page d\'accueil du plugin Poll</p>';
     ?>
        <!--<form action="options.php" method="post">-->
          <form action="" method="post">
          <p>    
          <?php do_action('monHookAjoutDeNouvellesOptions'); ?>
          <?php settings_fields('la_question_du_poll_settings') ?>
          <?php# add_settings_field('la_question_du_poll', 'QUESTION2222222222', array($this, 'sender_html'), 'la_question_du_poll_settings', 'la_question_du_poll_section'); ?>
          <?php do_settings_sections('la_question_du_poll_settings') ?>
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
          <?php submit_button(); ?>
      </form>
      <form method="post" action="">
       <input type="hidden" name="reinitialise" value="1"/>
       <?php submit_button("Réinitiliaser les options et les résultats"); ?>
      </form>
     <?php 
    }
    
    public function ajoutDeNouvellesOptions()
    {
      echo "<p> CECI</p>".$_POST['rdb_fruits_admin'];      
      if(isset($_POST['nouveauFruits']) && !empty($_POST['nouveauFruits'])){
        
        $nouveauFruits = $_POST['nouveauFruits']; 
        do_action('monHookInsertDansPollResultsEtPollOptions', $nouveauFruits);
     }  
   
    }

    public function register_settings()
    {
    register_setting('la_question_du_poll_settings', 'la_question_du_poll_INPUT_TEXT');

    add_settings_section('la_question_du_poll_section', 'La section du poll settings', array($this, 'section_html'), 'la_question_du_poll_settings');

    add_settings_field('la_question_du_poll_INPUT_TEXT', 'Question ici', array($this, 'question_html'), 'la_question_du_poll_settings', 'la_question_du_poll_section');
   }

    public function section_html()
    {
    echo 'Renseignez les paramètres du pluggin de pool.';
    }
    
    public function question_html()
    {?>
     <input type="text" name="la_question_du_poll_INPUT_TEXT" value="<?php echo get_option('la_question_du_poll_INPUT_TEXT')?>"/>
    <?php
    }
    public function process_action()
     {
 
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
