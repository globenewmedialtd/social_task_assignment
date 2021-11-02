<?php

namespace Drupal\social_task_assignment_vbo\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\social_task_assignment\TaskAssignmentInterface;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;

/**
 * Delete task assignment entity action.
 *
 * @Action(
 *   id = "social_task_assignment_vbo_delete_task_assignment_action",
 *   label = @Translation("Delete selected task assignment entities"),
 *   type = "task_assignment",
 *   confirm = TRUE,
 *   confirm_form_route_name = "social_task_assignment_vbo.vbo.confirm",
 * )
 */
class TaskAssignmentEntityDeleteAction extends ViewsBulkOperationsActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /** @var \Drupal\social_task_assignment\TaskAssignmentInterface $entity */
    $entity->delete();
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $access = AccessResult::forbidden();

    if ($object instanceof TaskAssignmentInterface) {
      $access = $object->access('delete', $account, TRUE);

      $task_id = $object->getFieldValue('field_task', 'target_id');
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($task_id);
      
    }

    return $return_as_object ? $access : $access->isAllowed();
  }

}
