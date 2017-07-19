<?php

/**
 * Classe Poll_Widget
 */
class Poll_Widget extends WP_Widget
{
    /**
     * Constructeur
     */
    public function __construct()
    {
      parent::__construct('gab_Vote_Fruits', 'Vote_Fruits', array('description' => 'Un formulaire de vote sur les fruits favoris.'));
      add_action('monHookUpdateDansPollResults', array($this, 'updateDansPollResults')); 
      add_action('monHookQuestionnaireDePollResults', array($this, 'questionnaireDePollResults'));
      add_action('monHookRenduDePollResults', array($this, 'renduDePollResults')); 

      
    }


    /**
     * Affichage du widget Du coté client
     */

    public function widget($args, $instance)
    {
      echo $args['before_widget'];
      echo $args['before_title'];
      echo apply_filters('widget_title', $instance['title']);
      echo $args['after_title'];
      do_action('monHookQuestionnaireDePollResults');
      
        if(isset($_POST['rdb_fruits'])){
           $vote = $_POST['rdb_fruits']; 
             do_action('monHookUpdateDansPollResults', $vote);
        }
       do_action('monHookRenduDePollResults');
      echo $args['after_widget'];
    }

    public function updateDansPollResults($vote)
    {
            global $wpdb;
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}poll_results WHERE option_id = '$vote'");
            $totalTmp = $row->total + 1;
            $wpdb->update("{$wpdb->prefix}poll_results", array('option_id' => $vote, 'total' => $totalTmp ),array('option_id' => $vote));         
    }
 
  public function questionnaireDePollResults()
    {
    ?>
      <form action="" method="post">
          <p>
             <?php
               global $wpdb;
                $totalOptions = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}poll_options" );   
                for ($cpt = 1; $cpt <= $totalOptions ; $cpt++) {
                    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}poll_options WHERE id = '$cpt'"); 
                    echo "<input type=\"radio\" name=\"rdb_fruits\" value=\"$cpt\">$row->label<br><br>";
                }
              ?>
          </p>
          <input type="submit" value="envoyer"/>
      </form>
      <?php        
    }

  public function renduDePollResults()
    {
 
      ?>
        <br><label>Résultats: </label><br><br>
          <p>
            <?php
             global $wpdb;
             $totalOptions = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}poll_options" );  
             for ($cpt = 1; $cpt <= $totalOptions ; $cpt++) {
                  echo $wpdb->get_row("SELECT * FROM {$wpdb->prefix}poll_options WHERE id = '$cpt'")->label." ";
                  echo $wpdb->get_row("SELECT * FROM {$wpdb->prefix}poll_results WHERE option_id = '$cpt'")->total." votes<br><br>";
              } 
            ?>
          </p>

      <?php        
    }

    /**
     * Affichage du formulaire dans l'administration 
     * oÙ C'EST CALLER CA %???
     */
    public function form($instance)
    {
      #permet de setter le titre dans apparence de widget DICI
      $title = isset($instance['title']) ? $instance['title'] : '';
      ?>
      <p>
          <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo  $title; ?>" />
      </p>
      <?php
      #Jusqu'à ici Sondage
    }  
}
