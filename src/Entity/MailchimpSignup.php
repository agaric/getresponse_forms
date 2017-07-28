<?php

namespace Drupal\getresponse_forms\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\getresponse_forms\MailchimpSignupInterface;

/**
 * Defines the MailchimpSignup entity.
 *
 * @ingroup getresponse_forms
 *
 * @ConfigEntityType(
 *   id = "getresponse_forms",
 *   label = @Translation("Mailchimp Signup Form"),
 *   fieldable = FALSE,
 *   handlers = {
 *     "list_builder" = "Drupal\getresponse_forms\Controller\MailchimpSignupListBuilder",
 *     "form" = {
 *       "add" = "Drupal\getresponse_forms\Form\MailchimpSignupForm",
 *       "edit" = "Drupal\getresponse_forms\Form\MailchimpSignupForm",
 *       "delete" = "Drupal\getresponse_forms\Form\MailchimpSignupDeleteForm"
 *     }
 *   },
 *   config_prefix = "getresponse_forms",
 *   admin_permission = "administer getresponse_forms",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/services/mailchimp/signup/{getresponse_forms}",
 *     "delete-form" = "/admin/config/services/mailchimp/signup/{getresponse_forms}/delete"
 *   }
 * )
 */
class MailchimpSignup extends ConfigEntityBase implements MailchimpSignupInterface {

  /**
   * The Signup ID.
   *
   * @var int
   */
  public $id;

  /**
   * The Signup Form Machine Name.
   *
   * @var string
   */
  public $name;

  /**
   * The Signup Form Title.
   *
   * @var string
   */
  public $title;

  /**
   * The Signup Form Mailchimp Lists.
   *
   * @var array
   */
  public $mc_lists;

  /**
   * The Signup Form Mode (Block, Page, or Both).
   *
   * @var int
   */
  public $mode;

  /**
   * The Signup Form Settings array.
   *
   * @var array
   */
  public $settings;

  /**
   * The Signup Form Status.
   *
   * @var boolean
   */
  public $status;

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->title;
  }

}
