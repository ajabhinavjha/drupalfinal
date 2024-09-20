<?php

namespace Drupal\first_module\Service;

/**
 * Provides name validation services.
 */
class NameValidator {

  /**
   * Validates a name.
   *
   * Ensures that the name starts with a capital letter and contains only
   * letters, spaces, and hyphens.
   *
   * @param string $name
   *   The name to validate.
   *
   * @return bool
   *   TRUE if the name is valid, FALSE otherwise.
   */
  public function isValid($name) {
    if (empty($name)) {
      return FALSE;
    }

    $pattern = '/^[A-Z][a-zA-Z\s\-]*$/';
    return preg_match($pattern, $name);
  }

}
