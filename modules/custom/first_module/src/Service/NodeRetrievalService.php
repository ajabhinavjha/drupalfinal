<?php

namespace Drupal\first_module\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Service for retrieving nodes of the 'example_form' content type.
 */
class NodeRetrievalService {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a NodeRetrievalService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Retrieves nodes of the 'example_form' content type.
   *
   * @return array
   *   An array of node data, including ID, name, and email.
   */
  public function getExampleFormNodes() {
    // Query to get node IDs of the 'example_form' content type.
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $nids = $query
      ->condition('type', 'example_form')
      ->accessCheck(TRUE)
      ->execute();

    // Load the nodes.
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

    $data = [];
    foreach ($nodes as $node) {
      $data[] = [
        'id' => $node->id(),
        'name' => $node->get('field_example_form_name')->value,
        'email' => $node->get('field_example_form_email')->value,
      ];
    }

    return $data;
  }

}
