<?php

namespace Drupal\example_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class ExampleContentTypeForm  extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'example_content_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    
    $form['email'] = [
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
    
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');

    
    $node = Node::create([
      'type' => 'example_form', 
      'title' => $name,
      'field_example_form_name' => $name,
      'field_example_form_email' => $email,
    ]);

    
    $node->save();

    
    $this->messenger()->addMessage($this->t('Thank you @name, your email @email has been submitted.', [
      '@name' => $name,
      '@email' => $email,
    ]));

    
    $form_state->setRedirect('entity.node.canonical', ['node' => $node->id()]);
  }

}
