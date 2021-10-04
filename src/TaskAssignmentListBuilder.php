<?php

namespace Drupal\social_task_assignment;

use Drupal\Core\Link;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Task assignment entities.
 *
 * @ingroup social_task_assignment
 */
class TaskAssignmentListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];
    $header['id'] = $this->t('Task assignment ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = [];
    /** @var \Drupal\social_task_assignment\Entity\TaskAssignment $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::fromTextAndUrl($entity->label(), new Url(
      'entity.task_assignment.edit_form', [
        'task_assignment' => $entity->id(),
      ]
    ));
    return $row + parent::buildRow($entity);
  }

}
