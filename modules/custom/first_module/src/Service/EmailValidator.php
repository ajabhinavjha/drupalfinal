<?php

namespace Drupal\first_module\Service;

/**
 * Provides email validation services.
 */
class EmailValidator {

  /**
   * Validates an email address.
   *
   * @param string $email
   *   The email address to validate.
   *
   * @return bool
   *   TRUE if the email address is valid, FALSE otherwise.
   */
  public function isValid($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE;
  }

}
