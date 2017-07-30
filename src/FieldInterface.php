<?php

namespace Drupal\getresponse_forms;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Image\ImageInterface;

/**
 * Defines the interface for image effects.
 *
 * @see \Drupal\image\Annotation\ImageEffect
 * @see \Drupal\image\ImageEffectBase
 * @see \Drupal\image\ConfigurableImageEffectInterface
 * @see \Drupal\image\ConfigurableImageEffectBase
 * @see \Drupal\image\ImageEffectManager
 * @see plugin_api
 */
interface FieldInterface extends PluginInspectionInterface, ConfigurablePluginInterface {

  /**
   * Applies an image effect to the image object.
   *
   * @param \Drupal\Core\Image\ImageInterface $image
   *   An image file object.
   *
   * @return bool
   *   TRUE on success. FALSE if unable to perform the image effect on the image.
   */
  public function applyEffect(ImageInterface $image);

  /**
   * Returns a render array summarizing the configuration of the image effect.
   *
   * @return array
   *   A render array.
   */
  public function getSummary();

  /**
   * Returns the field label (name).
   *
   * @return string
   *   The field label.
   */
  public function label();

  /**
   * Returns the unique ID representing the field.
   *
   * @return string
   *   The GetResponse field ID.
   */
  public function getId();

  /**
   * Returns the weight of the field.
   *
   * @return int|string
   *   Either the integer weight of the field, or an empty string.
   */
  public function getWeight();

  /**
   * Sets the weight for this field.
   *
   * @param int $weight
   *   The weight for this field.
   *
   * @return $this
   */
  public function setWeight($weight);

}
