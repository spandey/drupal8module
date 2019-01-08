<?php
/**
 * @file
 * Contains \Drupal\rsvplist\EnablerService
 */

namespace Drupal\rsvplist;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class EnablerService {
   /**
    * Constructor
    */ 
   public function __construct() {
       
   }
   /**
    * Set an individual node is RSVP enabled.
    * @param Node $node
    * @return boolean
    */
   public function setEnabled(Node $node) {
       if(!$this->isEnabled($node)) {
           $insert = Database::getConnection()->insert('rsvplist_enabled');
           $insert->fields(array('nid'),array($node->id()));
           $insert->execute();
       }
       
   }
   /**
    * Check if an individual node is RSVP enabled.
    * 
    * @param \Drupal\node\Etity\node $node
    * @return bool 
    */
   public function isEnabled(Node $node) {
       if($node->isNew()) {
           return FALSE;
       }
       $select = Database::getConnection()->select('rsvplist_enabled','re');
        $select->fields('re', array('nid'));
        $select->condition('nid', $node->id());
        $result = $select->execute();
        return !empty($result->fetchCol());
   }
   /**
    * Deletes enabled settings
    * @param Node $node
    * @return boolean
    */
   public function delEnabled(Node $node) {
        $delete = Database::getConnection()->delete('rsvplist_enabled');
        $delete->condition('nid', $node->id());
        $result = $delete->execute();
   }
}