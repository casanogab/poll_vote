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
      
        #if(isset($_POST['submit'])){
          if (1==1){
           $voteEffectue = $_POST['rdb_fruits']; 
             # echo "fruit choisi".$voteEffectue;
              global $wpdb;
              $wpdb->insert("{$wpdb->prefix}poll_results", array('option_id' => $voteEffectue, 'total' => '1000' ));
        }
      echo $args['after_widget'];
    }

    /**
     * Affichage du formulaire dans l'administration PAS POUR LE MOMENT
     */
    public function form($instance)
    {}
}
