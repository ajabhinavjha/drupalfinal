<?php

namespace Drupal\first_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Provides a clss for FormBase.
 */
class FirstForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'first_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['field_example_form_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['field_example_form_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
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

    $postData = $form_state->getValues();
    $query = \Drupal::database();
    $query->insert('first')->fields([
      'name' => $postData['field_example_form_name'],
      'email' => $postData['field_example_form_email'],
    ])->execute();

    $this->messenger()->addMessage($this->t('Thank you @name, your email @email has been submitted.', [
      '@name' => 'field_example_form_name',
      '@email' => 'field_example_form_email',
    ]));

    $form_state->setRedirect('entity.node.canonical', ['node' => $node->id()]);
  }

}
