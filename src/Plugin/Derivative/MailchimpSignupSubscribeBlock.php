<?php

namespace Drupal\getresponse_forms\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Provides block plugin definitions for MailChimp Signup blocks.
 *
 * @see \Drupal\getresponse_forms\Plugin\Block\MailchimpSignupSubscribeBlock
 */
class MailchimpSignupSubscribeBlock extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $signups = getresponse_forms_load_multiple();

    /* @var $signup \Drupal\getresponse_forms\Entity\MailchimpSignup */
    foreach ($signups as $signup) {
      if (intval($signup->mode) == MAILCHIMP_SIGNUP_BLOCK || intval($signup->mode) == MAILCHIMP_SIGNUP_BOTH) {

        $this->derivatives[$signup->id] = $base_plugin_definition;
        $this->derivatives[$signup->id]['admin_label'] = t('Mailchimp Subscription Form: @name', array('@name' => $signup->label()));
      }
    }

    return $this->derivatives;
  }

}
