<?php

namespace Drupal\getresponse_forms\Routing;

use Symfony\Component\Routing\Route;

/**
 * Defines dynamic routes for MailChimp signup forms rendered as pages.
 */
class MailchimpSignupRoutes {

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $routes = array();

    $signups = getresponse_forms_load_multiple();

    /* @var $signup \Drupal\getresponse_forms\Entity\MailchimpSignup */
    foreach ($signups as $signup) {
      if ((intval($signup->mode) == MAILCHIMP_SIGNUP_PAGE) || (intval($signup->mode) == MAILCHIMP_SIGNUP_BOTH)) {
        $routes['getresponse_forms.' . $signup->id] = new Route(
          // Route Path.
          '/' . $signup->settings['path'],
          // Route defaults.
          array(
            '_controller' => '\Drupal\getresponse_forms\Controller\MailchimpSignupController::page',
            '_title' => $signup->title,
            'signup_id' => $signup->id,
          ),
          // Route requirements.
          array(
            '_permission'  => 'access mailchimp signup pages',
          )
        );
      }
    }

    return $routes;
  }

}
