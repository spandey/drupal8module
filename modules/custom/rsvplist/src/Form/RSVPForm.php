<?php
/**
 * @files
 * Contains \Drupal\rsvplist\Form\RSVPForm 
 * 
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Provoids an rsvp Email Form.
 */
class RSVPForm extends FormBase {
   /**
    * (@inheritdoc)
    */ 
   public function getFormId() {
    return 'rsvplist_email_form';
  } 
  /**
   * (@inheritdoc)
   */ 
   public function buildForm(array $form, FormStateInterface $form_state) {

        $node = \Drupal::routeMatch()->getParameter('node');
        $nid = $node->nid->value;
         $form['email'] = array(
          '#type' => 'textfield',
          '#title' => $this->t('Your Email Address'),
          '#size' => 25,
          '#description' => $this->t("We'll send updates to the email adress you provide."),
          '#required' => TRUE,
        );


        $form['save'] = array(
          '#type' => 'submit',
          '#value' => $this->t('RSVP Save'),
        );
        $form['nid'] = array(
          '#type' => 'hidden',
          '#value' => $nid,
        );
        return $form;
    }
    /**
     * (@inheritdoc)
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $value = $form_state->getValue('email');
        if($value ==! \Drupal::service('email.validator')->isValid($value)) {
           $form_state->setErrorByName('email', $this->t('The email address %mail is not valid.', array('%mail'=> $value))); 
           return;           
        }
        //Get node object by route
        $node = \Drupal::routeMatch()->getParameter('node');
        //check email is already set for this node
        $select = Database::getConnection()->select('rsvplist','r');
        $select->fields('r', array('nid'));
        $select->condition('nid', $node->id());
        $select->condition('email', $value);
        $result = $select->execute();
       if(empty($result->fetchCol())) {
          $form_state->setErrorByName('email', $this->t('The email address %mail is already subscried to this list.', array('%mail'=> $value))); 
           return;  
       }
    
    }
    /**
     * (@inheritdoc)
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
        db_insert('rsvplist')
        ->fields(array(
          'email'=> $form_state->getValue('email'),
          'nid'=> $form_state->getValue('nid'),
          'uid'=> $user->id(),
          'created' => time(),
        ))
        ->execute();        
        drupal_set_message(t('Thankyou for rsvp. you are on the list for the event.'));
    }
}