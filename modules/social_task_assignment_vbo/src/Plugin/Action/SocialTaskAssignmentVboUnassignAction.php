<?php

namespace Drupal\social_task_assignment_vbo\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\social_task_assignment\TaskAssignmentInterface;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;

/**
 * SocialTaskAssignmentVboUnassignAction class.
 *
 * @Action(
 *   id = "social_task_assignment_vbo_unassign_action",
 *   label = @Translation("Unassign the selected assignees"),
 *   type = "task_assignment",
 *   view_id = "manage_all_task_assignments",
 *   display_id = "page",
 *   confirm = TRUE,
 *   confirm_form_route_name = "social_task_assignment_vbo.management.vbo.confirm",
 * )
 */
class SocialTaskAssignmentVboUnassignAction extends ViewsBulkOperationsActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /** @var \Drupal\social_task_assignment\TaskAssignmentInterface $entity */
    if (isset($entity->field_assigned->value) && $entity->field_assigned->value  == TRUE) {
      $entity->field_assigned->value = FALSE;
      $entity->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object instanceof TaskAssignmentInterface) {
      $access = $this->getAccount($object)->access('view', $account, TRUE);
    }
    else {
      $access = AccessResult::forbidden();
    }

    return $return_as_object ? $access : $access->isAllowed();
  }

  /**
   * Extract user entity from task assignment entity.
   *
   * @param \Drupal\social_task_assignment\TaskAssignmentInterface $entity
   *   The task assignment.
   *
   * @return \Drupal\user\UserInterface
   *   The user.
   */
  public function getAccount(TaskAssignmentInterface $entity) {
    $accounts = $entity->field_account->referencedEntities();
    return reset($accounts);
  }

}
