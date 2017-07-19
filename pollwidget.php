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
      add_action('monHook', array($this, 'updateDansPollResults'));  
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
      ?>
      <form action="" method="post">
          <p>

                <input type="radio" name="rdb_fruits" value="1"> oranges<br>
                <input type="radio" name="rdb_fruits" value="2"> pommes <br>
                <input type="radio" name="rdb_fruits" value="3"> poires <br>
                <input type="radio" name="rdb_fruits" value="4"> pèches <br>

          </p>
          <input type="submit" value="envoyer"/>
      </form>
      <?php
      
        if(isset($_POST['rdb_fruits'])){
          #if (1==1){
           $vote = $_POST['rdb_fruits']; 
             # echo "fruit choisi".$voteEffectue;
             do_action('monHook', $vote);


        }
      echo $args['after_widget'];
    }

    public function updateDansPollResults($vote)
    {
            global $wpdb;
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}poll_results WHERE option_id = '$vote'");
            $totalTmp = $row->total + 1;
            $wpdb->update("{$wpdb->prefix}poll_results", array('option_id' => $vote, 'total' => $totalTmp ),array('option_id' => $vote));         
    }


    /**
     * Affichage du formulaire dans l'administration PAS POUR LE MOMENT
     */
    public function form($instance)
    {}
}
