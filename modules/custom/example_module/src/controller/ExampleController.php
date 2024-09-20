<?php

namespace Drupal\example_module\Controller;

use Drupal\Core\Controller\ControllerBase;

class ExampleController extends ControllerBase {
  public function examplePage() {
    $form = \Drupal::formBuilder()->getForm('Drupal\example_module\Form\ExampleContentTypeForm');

    $rendered_form = \Drupal::service('renderer')->render($form);

    return[
      '#type' => 'markup',
      '#markup' => $rendered_form,
    ];
  }
}
