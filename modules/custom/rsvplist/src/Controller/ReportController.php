<?php
/**
 * @file
 * Contains Drupal\rsvplist\Controller\ReportController
 */
namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

/**
 * Controller for RSVP report list
 */

class ReportController extends ControllerBase {
    /**
     * Get all RSVP from all nodes
     * @return array
     */
    protected function load() {
        $select = Database::getConnection()->select('rsvplist','r');
        //join the user table to get user name of RSVP entity creator's. 
        $select->join('users_field_data', 'u', 'r.uid = u.uid');
        //join node table to get node title.
        $select->join('node_field_data', 'n', 'r.nid = n.nid');
        // Select the specific fields.
        $select->addField('u', 'name','username');
        $select->addField('r', 'email');
        $select->addField('n', 'title');
        $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);
        return $entries;
    }
    /**
     * Create the report 
     * @return array
     * Render array for report
     */
    public function report(){
        $content = array();
        $content['message'] = array(
            '#markup'=>$this->t('Below is the list of all event RSVPs including email address, Event name and user name they will be attending.'),            
        );
        $headers = array(
            $this->t('Name'),
            $this->t('Event'),
            $this->t('Email'),
        );
        $rows = array();
        foreach($entries = $this->load() as $entry){
          // Senetize the each row
          // SafeMarkup is deprecated it will not more in drupal 9
          $rows[]   = array_map('Drupal\Component\Utility\SafeMarkup::checkPlain', $entry); 
        }
        $content['table'] = array(
            '#type' => 'table',
            '#header' => $headers,
            '#rows' => $rows,
            '#empty' => $this->t('No entries available'),
        );
        //Do't cache this page
        $content['#cache']['max-size'] = 0;
        return $content;
    }
            
}