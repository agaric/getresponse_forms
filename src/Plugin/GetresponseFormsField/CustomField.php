<?php

namespace Drupal\getresponse_forms\Plugin\GetresponseFormsField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\getresponse_forms\ConfigurableFieldInterface;

/**
 * Provides all available custom fields.
 *
 * @GetresponseFormsField(
 *   id = "getresponse_forms_custom_field",
 *   admin_label = @Translation("Custom field"),
 *   deriver = "Drupal\getresponse_forms\Plugin\Derivative\CustomField"
 * )
 */
class CustomField implements ConfigurableFieldInterface {

  /**
   * {@inheritdoc}
   */
  public function getUuid() {
    return $this->uuid;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginId() {
    return 'getresponse_forms_custom_field:' . $this->customFieldId;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#theme' => 'image_resize_summary',
      '#data' => $this->configuration,
    ];
    $summary += parent::getSummary();

    return $summary;
}

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'name' => NULL,
      'label' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['label'] = [
      '#type' => 'text',
      '#title' => t('Label'),
      '#default_value' => $this->configuration['label'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['label'] = $form_state->getValue('label');
  }

}
