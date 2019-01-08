<?php
/**
 * @files
 * Contains \Drupal\rsvplist\Form\RSVPForm 
 * 
 */
namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures RSVP List settings.
 */
class RSVPSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvp_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'rsvplist.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $type = node_type_get_names();
    $config  = $this->config('rsvplist.settings');
    $form['rsvplist_type'] = array(
        '#type' => 'checkboxes',
        '#title' => $this->t('The content type to enable RSVP collection for'),
        '#default_value' => $config->get('allowed_types'),
        '#options' => $type,
        '#description' => $this->t('On the specified node type, an RSVP option will be available'
                . ' and can be enabled while that node is being edited.'),
    ); 
    $form['array_filter'] = array(
      '#type' => 'value',
      '#value' => TRUE,
      
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $allowed_types = array_filter($form_state->getValue('rsvplist_type'));
    sort($allowed_types);
    $this->config('rsvplist.settings')
      ->set('allowed_types', $allowed_types)
      ->save();

    parent::submitForm($form, $form_state);
  }

  

}
