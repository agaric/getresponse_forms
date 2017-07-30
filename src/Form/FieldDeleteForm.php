<?php

namespace Drupal\getresponse_forms\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\getresponse_forms\GetresponseFormsInterface;

/**
 * Form for deleting a field.
 */
class FieldDeleteForm extends ConfirmFormBase {

  /**
   * The form containing the field to be deleted.
   *
   * @var \Drupal\getresponse_forms\GetresponseFormsInterface
   */
  protected $getresponseForm;

  /**
   * The GetResponse custom field to be deleted.
   *
   * @var \Drupal\getresponse_forms\FieldInterface
   */
  protected $field;

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to remove the @field field from the %form form?', ['%form' => $this->getresponseForm->label(), '@field' => $this->field->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Remove');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->imageStyle->urlInfo('edit-form');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'getresponse_forms_field_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, GetresponseFormsInterface $getresponse_form = NULL, $field = NULL) {
    $this->getresponseForm = $getresponse_form;
    $this->field = $this->getresponseForm->getField($field);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->imageStyle->deleteImageEffect($this->imageEffect);
    drupal_set_message($this->t('The image effect %name has been deleted.', ['%name' => $this->imageEffect->label()]));
    $form_state->setRedirectUrl($this->imageStyle->urlInfo('edit-form'));
  }

}
