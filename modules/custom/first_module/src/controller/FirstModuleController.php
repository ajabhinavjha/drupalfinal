<?php

namespace Drupal\first_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\first_module\Service\EmailValidator;
use Drupal\first_module\Service\NameValidator;
use Drupal\first_module\Service\NodeRetrievalService;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a controller for the First Module.
 */
class FirstModuleController extends ControllerBase {

  /**
   * The name validator service.
   *
   * @var \Drupal\first_module\Service\NameValidator
   */
  protected $nameValidator;

  /**
   * The email validator service.
   *
   * @var \Drupal\first_module\Service\EmailValidator
   */
  protected $emailValidator;

  /**
   * The node retrieval service.
   *
   * @var \Drupal\first_module\Service\NodeRetrievalService
   */
  protected $nodeRetrievalService;

  /**
   * Constructs a new FirstModuleController.
   *
   * @param \Drupal\first_module\Service\NameValidator $nameValidator
   *   The name validator service.
   * @param \Drupal\first_module\Service\EmailValidator $emailValidator
   *   The email validator service.
   * @param \Drupal\first_module\Service\NodeRetrievalService $nodeRetrievalService
   *   The node retrieval service.
   */
  public function __construct(NameValidator $nameValidator, EmailValidator $emailValidator, NodeRetrievalService $nodeRetrievalService) {
    $this->nameValidator = $nameValidator;
    $this->emailValidator = $emailValidator;
    $this->nodeRetrievalService = $nodeRetrievalService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('first_module.name_validator'),
      $container->get('first_module.email_validator'),
      $container->get('first_module.node_retrieval_service')
    );
  }

  /**
   * Creates and returns a form.
   *
   * @return array
   *   A renderable array representing the form.
   */
  public function createForm() {
    $form = \Drupal::formBuilder()->getForm('Drupal\first_module\Form\FirstForm');
    return [
      '#theme' => 'first_module',
      '#items' => $form,
      '#title' => $this->t('First Form'),
    ];
  }

  /**
   * Retrieves and displays a list of entries.
   *
   * @return array
   *   A renderable array representing the list of entries.
   */
  public function getFirstlist() {
    $query = \Drupal::database();
    $result = $query->select('first', 'e')
      ->fields('e', ['id', 'name', 'email'])
      ->execute()
      ->fetchAll(\PDO::FETCH_OBJ);

    $data = [];
    $count = 1;
    foreach ($result as $row) {
      $data[] = [
        'serial_no' => $count . ".",
        'name' => $row->name,
        'email' => $row->email,
        'edit' => $this->t('<a href="@url">Edit</a>', ['@url' => "edit-first/$row->id"]),
        'delete' => $this->t('<a href="@url">Delete</a>', ['@url' => "delete-first/$row->id"]),
      ];
      $count++;
    }

    $header = ['serial_no', 'Name', 'Email', 'Edit', 'Delete'];

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $data,
    ];

    return [
    // Ensure the array key 'table' is explicitly set.
      'table' => $build['table'],
      '#title' => $this->t('First List'),
    ];
  }

  /**
   * Provides an API endpoint to retrieve a list of nodes.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response with the list of nodes.
   */
  public function getFirstListApi() {
    $data = $this->nodeRetrievalService->getExampleFormNodes();
    return new JsonResponse($data);
  }

  /**
   * Creates a node from request data.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response indicating success or failure.
   */
  public function createNode(Request $request) {
    $data = json_decode($request->getContent(), TRUE);

    if (empty($data['name']) || empty($data['email'])) {
      return new JsonResponse(['error' => 'Name and email are required.'], 400);
    }

    if (!$this->emailValidator->isValid($data['email'])) {
      return new JsonResponse(['error' => 'Invalid email address.'], 400);
    }

    if (!$this->nameValidator->isValid($data['name'])) {
      return new JsonResponse(['error' => 'Invalid name.'], 400);
    }

    try {
      $node = Node::create([
        'type' => 'example_form',
        'title' => $data['name'],
        'field_example_form_name' => $data['name'],
        'field_example_form_email' => $data['email'],
      ]);

      $node->save();
      return new JsonResponse(['message' => 'Node created successfully'], 201);
    }
    catch (\Exception $e) {
      \Drupal::logger('first_module')->error($e->getMessage());
      return new JsonResponse(['error' => 'Failed to create node.'], 500);
    }
  }

  /**
   * Deletes an entry by its ID.
   *
   * @param int $id
   *   The ID of the entry to delete.
   */
  public function deleteFirst($id) {
    $query = \Drupal::database();
    $query->delete('first')
      ->condition('id', $id, '=')
      ->execute();
    $response = new RedirectResponse('/first-list');
    $response->send();
  }

}
