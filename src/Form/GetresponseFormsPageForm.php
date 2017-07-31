<?php

namespace Drupal\getresponse_forms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\getresponse_forms\Entity\GetresponseForms;
use Drupal\getresponse\Service\Api;

/**
 * Subscribe to a GetResponse list.
 */
class GetresponseFormsPageForm extends FormBase {

  /**
   * The ID for this form.
   * Set as class property so it can be overwritten as needed.
   *
   * @var string
   */
  private $formId = 'getresponse_forms_page_form';

  /**
   * The GetresponseForms entity used to build this form.
   *
   * @var GetresponseForms
   */
  private $signup = NULL;

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return $this->formId;
  }

  public function setFormID($formId) {
    $this->formId = $formId;
  }

  public function setSignup(GetresponseForms $signup) {
    $this->signup = $signup;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['getresponse_forms.page_form'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = array();

    $form['#attributes'] = array('class' => array('getresponse-forms-subscribe-form'));

    $form['description'] = array(
      '#markup' => $this->signup->description,
      '#weight' => -80,
    );

    foreach ($this->signup->getFields() as $field) {
      getresponse_forms_drupal_form_element($field, $form, $form_state);
    }

    $lists = getresponse_get_lists($this->signup->gr_lists);
    $lists_count = (!empty($lists)) ? count($lists) : 0;
    if (empty($lists)) {
      drupal_set_message($this->t('The subscription service is currently unavailable. Please try again later.'), 'warning');
    }

    if ($lists_count > 1) {
      $options = [];
      foreach ($lists as $list) {
        $options[$list->campaignId] = $list->name;

        $form['getresponse_lists'] = [
          '#type' => 'checkboxes',
          '#title' => t('Subscriptions'),
          '#options' => $options,
          '#required' => TRUE,
//          '#default_value' => $form_state->getValue('getresponse_lists', []),
          '#weight' => 80,
        ];

      }
    }
    else {
      $list = reset($lists);
      $form['getresponse_lists'] = array(
        '#type' => 'hidden',
        '#title' => $list->name,
        '#value' => $list->campaignId,
        '#weight' => 80,
      );

    }

    $form['actions'] = ['#type' => 'actions', '#weight' => 99];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->signup->submit_button,
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $build_info = $form_state->getBuildInfo();
    $signup = $build_info['callback_object']->signup;

    // Ensure that we have an e-mail address.
    $email = $form_state->getValue('getresponse_forms_email_field');
    if (!$email) {
      // TODO make sure we don't ever show them this when there is no form.
      $form_state->setErrorByName('getresponse_forms_email_field', t("Please enter your e-mail address."));
    }

    // For forms that allow subscribing to multiple lists
    // ensure at least one list has been selected.

    // Get the enabled lists for this form.
    $enabled_lists = array_filter($signup->gr_lists);
    if (count($enabled_lists) > 1) {

      // Filter the selected lists out of the form values.
      $selected_lists = array_filter($form_state->getValue('getresponse_lists'),
        function($list) {
          return $list['subscribe'];
        }
      );

      // If a list has been selected, validation passes.
      if (!empty($selected_lists)) {
        return;
      }

      $form_state->setErrorByName('getresponse_lists', t("Please select at least one list to subscribe to."));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    global $base_url;

    $list_details = getresponse_get_lists($this->signup->gr_lists);

    $subscribe_lists = array();

    // Filter out blank fields so we don't erase values on the GetResponse side.
    $custom_fields = array_filter($form_state->getValue('custom_fields'));

    $getresponse_lists = $form_state->getValue('getresponse_lists');

    // If we only have one list we won't have checkbox values to investigate.
    if (count(array_filter($this->signup->gr_lists)) == 1) {
      $subscribe_lists[0] = array(
        'subscribe' => reset($this->signup->gr_lists),
      );
    }
    else {
      // We can look at the checkbox values now.
      foreach ($getresponse_lists as $list) {
        if ($list['subscribe']) {
          $subscribe_lists[] = $list;
        }
      }
    }


    $request = [
      "name" => $form_state->getValue('getresponse_forms_name_field'),
      "email" => $form_state->getValue('getresponse_forms_email_field'),
      "campaign" => [
        "campaignId" => reset($subscribe_lists),
      ],
      "customFieldValues" => [],
    ];
    foreach ($this->signup->getFields() as $field) {
      $definition = $field->getPluginDefinition();
      if (isset($definition['customFieldId'])) {
        $key = $definition['name'];
        $value = $form_state->getValue($key);
        if ($value) {
          $request["customFieldValues"][] = [
            "customFieldId" => $definition['customFieldId'],
            "value" => is_array($value) ? $value : [$value],
          ];
        }
      }
    }
    drupal_set_message(var_export($request, TRUE));
    // $result = mailchimp_subscribe($list_id, $email, $mergevars, $this->signup->settings['doublein']);
    $api_key = \Drupal::config('getresponse.settings')->get('api_key');
    $api     = new Api($api_key);
    // $result  = $api->addContact($fields);

    if (empty($result)) {
      drupal_set_message(t('There was a problem with your newsletter signup to %list.', array(
        '%list' => $list_details[$list_id]->name,
      )), 'warning');
    }
    else {
      $successes[] = $list_details[$list_id]->name;
    }

    if (count($successes) && strlen($this->signup->settings['confirmation_message'])) {
      drupal_set_message($this->signup->settings['confirmation_message'], 'status');
    }

    $destination = $this->signup->settings['destination'];
    if (empty($destination)) {
      $destination_url = Url::fromRoute('<current>');
    }
    else {
      $destination_url = Url::fromUri($base_url . '/' . $this->signup->settings['destination']);
    }

    $form_state->setRedirectUrl($destination_url);
  }

}
