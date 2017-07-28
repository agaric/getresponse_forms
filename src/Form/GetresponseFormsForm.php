<?php

namespace Drupal\getresponse_forms\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the GetresponseForms entity edit form.
 *
 * @ingroup getresponse_forms
 */
class GetresponseFormsForm extends EntityForm {

  /**
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The entity query.
   */
  public function __construct(QueryFactory $entity_query) {
    $this->entityQuery = $entity_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $signup = $this->entity;

    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#size' => 35,
      '#maxlength' => 32,
      '#default_value' => $signup->title,
      '#description' => $this->t('The title for this signup form.'),
      '#required' => TRUE,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $signup->id,
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => array(
        'source' => array('title'),
        'exists' => 'getresponse_forms_load',
      ),
      '#description' => t('A unique machine-readable name for this list. It must only contain lowercase letters, numbers, and underscores.'),
      '#disabled' => !$signup->isNew(),
    );

    $form['description'] = array(
      '#type' => 'textarea',
      '#title' => 'Description',
      '#default_value' => isset($signup->settings['description']) ? $signup->settings['description'] : '',
      '#rows' => 2,
      '#maxlength' => 500,
      '#description' => t('This description will be shown on the signup form below the title. (500 characters or less)'),
    );
    $mode_defaults = array(
      GETRESPONSE_FORMS_BLOCK => array(GETRESPONSE_FORMS_BLOCK),
      GETRESPONSE_FORMS_PAGE => array(GETRESPONSE_FORMS_PAGE),
      GETRESPONSE_FORMS_BOTH => array(GETRESPONSE_FORMS_BLOCK, GETRESPONSE_FORMS_PAGE),
    );
    $form['mode'] = array(
      '#type' => 'checkboxes',
      '#title' => 'Display Mode',
      '#required' => TRUE,
      '#options' => array(
        GETRESPONSE_FORMS_BLOCK => 'Block',
        GETRESPONSE_FORMS_PAGE => 'Page',
      ),
      '#default_value' => !empty($signup->mode) ? $mode_defaults[$signup->mode] : array(),
    );

    $form['settings'] = array(
      '#type' => 'details',
      '#title' => 'Settings',
      '#tree' => TRUE,
      '#open' => TRUE,
    );

    $form['settings']['path'] = array(
      '#type' => 'textfield',
      '#title' => 'Page URL',
      '#description' => t('Path to the signup page. ie "newsletter/signup".'),
      '#default_value' => isset($signup->settings['path']) ? $signup->settings['path'] : NULL,
      '#states' => array(
        // Hide unless needed.
        'visible' => array(
          ':input[name="mode[' . GETRESPONSE_FORMS_PAGE . ']"]' => array('checked' => TRUE),
        ),
        'required' => array(
          ':input[name="mode[' . GETRESPONSE_FORMS_PAGE . ']"]' => array('checked' => TRUE),
        ),
      ),
    );

    $form['settings']['submit_button'] = array(
      '#type' => 'textfield',
      '#title' => 'Submit Button Label',
      '#required' => 'TRUE',
      '#default_value' => isset($signup->settings['submit_button']) ? $signup->settings['submit_button'] : 'Submit',
    );

    $form['settings']['confirmation_message'] = array(
      '#type' => 'textfield',
      '#title' => 'Confirmation Message',
      '#description' => 'This message will appear after a successful submission of this form. Leave blank for no message, but make sure you configure a destination in that case unless you really want to confuse your site visitors.',
      '#default_value' => isset($signup->settings['confirmation_message']) ? $signup->settings['confirmation_message'] : 'You have been successfully subscribed.',
    );

    $form['settings']['destination'] = array(
      '#type' => 'textfield',
      '#title' => 'Form destination page',
      '#description' => 'Leave blank to stay on the form page.',
      '#default_value' => isset($signup->settings['destination']) ? $signup->settings['destination'] : NULL,
    );

    $form['gr_lists_config'] = array(
      '#type' => 'details',
      '#title' => t('GetResponse List Selection & Configuration'),
      '#open' => TRUE,
    );
    $lists = getresponse_get_lists();
    $options = array();
    foreach ($lists as $gr_list) {
      $options[$gr_list->campaignId] = $gr_list->name;
    }
    $gr_admin_url = Link::fromTextAndUrl('GetResponse', Url::fromUri('https://app.getresponse.com', array('attributes' => array('target' => '_blank', 'rel' => 'noopener noreferrer'))));
    $form['gr_lists_config']['gr_lists'] = array(
      '#type' => 'checkboxes',
      '#title' => t('GetResponse Lists (Campaigns)'),
      '#description' => t('Select which lists to show on your signup form. You can create additional lists at @GetResponse.',
        array('@GetResponse' => $gr_admin_url->toString())),
      '#options' => $options,
      '#default_value' => is_array($signup->gr_lists) ? $signup->gr_lists : array(),
      '#required' => TRUE,
      '#ajax' => array(
        'callback' => '::mergefields_callback',
        'wrapper' => 'mergefields-wrapper',
        'method' => 'replace',
        'effect' => 'fade',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('Retrieving merge fields for this list.'),
        ),
      ),
    );

    $form['gr_lists_config']['custom_fields'] = array(
      '#prefix' => '<div id="custom-fields-wrapper">',
      '#suffix' => '</div>',
    );


    $form['subscription_settings'] = array(
      '#type' => 'details',
      '#title' => t('Subscription Settings'),
      '#open' => TRUE,
    );

    $form['subscription_settings']['doublein'] = array(
      '#type' => 'checkbox',
      '#title' => t('Require subscribers to Double Opt-in TODO see if GetResponse has this option'),
      '#description' => t('New subscribers will be sent a link with an email they must follow to confirm their subscription.'),
      '#default_value' => isset($signup->settings['doublein']) ? $signup->settings['doublein'] : FALSE,
    );

    return $form;
  }

  /**
   * AJAX callback handler for GetresponseFormsForm.
   */
  public function customfields_callback(&$form, FormStateInterface $form_state) {
    return $form['gr_lists_config']['customfields'];
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $mode = $form_state->getValue('mode');

    /* @var $signup \Drupal\getresponse_forms\Entity\GetresponseForms */
    $signup = $this->getEntity();
    $signup->mode = array_sum($mode);

    $customfields = $form_state->getValue('customfields');

    $gr_lists = $form_state->getValue('gr_lists') ? $form_state->getValue('gr_lists') : $signup->gr_lists;

    foreach ($mergefields as $id => $val) {
      if ($val) {
        // Can't store objects in configuration; serialize this.
        $mergefields[$id] = serialize($mergevar_options[$id]);
      }
    }

    $signup->settings['mergefields'] = $mergefields;
    $signup->settings['description'] = $form_state->getValue('description');
    $signup->settings['doublein'] = $form_state->getValue('doublein');

    // Clear path value if mode doesn't include signup page.
    if (!isset($mode[GETRESPONSE_FORMS_PAGE])) {
      $signup->settings['path'] = '';
    }

    $signup->save();

    \Drupal::service('router.builder')->setRebuildNeeded();

    $form_state->setRedirect('getresponse_forms.admin');
  }



  public function exist($id) {
    $entity = $this->entityQuery->get('getresponse_forms')
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
