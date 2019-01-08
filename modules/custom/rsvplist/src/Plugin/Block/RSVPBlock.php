<?php
/**
 * @files
 * Contains \Drupal\rsvplist\Plugin\Block\RSVPBlock
 */

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a block for RSVP List.
 *
 * @Block(
 *   id = "rsvp_list_block",
 *   admin_label = @Translation("RSVP List Block"),
 *  
 * )
 */

class RSVPBlock extends BlockBase {
    /**
     * {@inheritdoc}
     */
    public function build() {
        //return array('#markup' => $this->t('RSVP List Block'));
        return \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
    }
    /**
     * {@inheritdoc}
     */
    public function blockAccess(AccountInterface $account) {
        $node = \Drupal::routeMatch()->getParameter('node');
        $nid = $node->nid->value;
        $enabler = \Drupal::service('rsvplist.enabler');
        if(is_numeric($nid)) {
            if($enabler->isEnabled($node)) {
                return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
            }
        }
        return AccessResult::forbidden();
    }
}