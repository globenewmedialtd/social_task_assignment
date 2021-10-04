<?php

namespace Drupal\social_task_assignment;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Task assignment entity.
 *
 * @see \Drupal\social_task_assignment\Entity\TaskAssignment.
 */
class TaskAssignmentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\social_task_assignment\TaskAssignmentInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished task assignment entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published task assignment entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit task assignment entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete task assignment entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add task assignment entities');
  }

}
