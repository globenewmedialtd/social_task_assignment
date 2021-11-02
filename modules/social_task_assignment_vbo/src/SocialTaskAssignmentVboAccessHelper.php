<?php

namespace Drupal\social_task_assignment_vbo;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\NodeInterface;

/**
 * Helper class for checking update access on task managers nodes.
 */
class SocialTaskAssignmentVboAccessHelper {

  /**
   * NodeAccessCheck for given operation, node and user account.
   */
  public static function nodeAccessCheck(NodeInterface $node, $op, AccountInterface $account) {
    if ($op === 'update') {

      // Only for tasks.
      if ($node->getType() === 'task') {
        // Only continue if the user has access to view the task.
        if ($node->access('view', $account)) {
          // The owner has access.
          if ($account->id() === $node->getOwnerId()) {
            return 2;
          }

          $task_managers = $node->get('field_task_managers')->getValue();

          foreach ($task_managers as $task_manager) {
            if (isset($task_manager['target_id']) && $account->id() == $task_manager['target_id']) {
              return 2;
            }
          }

          // No hits, so we assume the user is not a task manager.
          return 1;
        }
      }
    }
    return 0;
  }

  /**
   * Gets the Entity access for the given node.
   */
  public static function getEntityAccessResult(NodeInterface $node, $op, AccountInterface $account) {
    $access = self::nodeAccessCheck($node, $op, $account);

    switch ($access) {
      case 2:
        return AccessResult::allowed()->cachePerPermissions()->addCacheableDependency($node);

      case 1:
        return AccessResult::forbidden();
    }

    return AccessResult::neutral();
  }

}
