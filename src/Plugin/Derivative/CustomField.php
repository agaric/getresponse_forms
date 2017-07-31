<?php

namespace Drupal\getresponse_forms\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Provides field definitions for GetResponse custom fields.
 *
 * @see \Drupal\getresponse_forms\Plugin\GetresponseFormsField\CustomField
 */
class CustomField extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $custom_fields = getresponse_get_custom_fields();

    /* @var $signup \Drupal\getresponse_forms\Entity\GetresponseForms */
    foreach ($custom_fields as $key => $field) {
      print 'key ' . $key;
      $this->derivatives[$key] = $base_plugin_definition;
      $this->derivatives[$key]['admin_label'] = t('GetResponse Field: @name', array('@name' => $field->name));
    }

    return $this->derivatives;
  }

}
