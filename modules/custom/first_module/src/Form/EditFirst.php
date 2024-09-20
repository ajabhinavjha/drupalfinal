<?php

namespace Drupal\first_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a class for FormBase.
 */
class EditFirst extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_first';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $id = \Drupal:: routeMatch()->getParameter('id');
    $query = \Drupal::database();
    $data = $query->select('first', 'e')
      ->fields('e', ['id', 'name', 'email'])
      ->condition('e.id', $id, '=')
      ->execute()->fetchAll(\PDO::FETCH_OBJ);
    $form['field_example_form_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
      '#default_value' => $data[0]->name,
    ];

    $form['field_example_form_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
      '#default_value' => $data[0]->email,
    ];

    $form['update'] = [
      '#type' => 'submit',
      '#value' => $this->t('update'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $node = Node::create([
      'type' => 'example_form',
      'title' => 'Form',
      'field_example_form_name' => $form_state->getValue('field_example_form_name'),
      'field_example_form_email' => $form_state->getValue('field_example_form_email'),
    ]);

    $node->save();

    $id = \Drupal:: routeMatch()->getParameter('id');
    $postData = $form_state->getValues();
    $query = \Drupal::database();
    $query->update('first')->fields([
      'name' => $postData['field_example_form_name'],
      'email' => $postData['field_example_form_email'],
    ])
      ->condition('id', $id)
      ->execute();

    $response = new RedirectResponse('../first-list');
    $response->send();

  }

}
