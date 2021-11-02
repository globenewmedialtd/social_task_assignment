<?php

namespace Drupal\social_task_assignment_vbo\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\social_task_assignment\TaskAssignmentInterface;
use Drupal\social_user_export\Plugin\Action\ExportUser;

/**
 * Exports a task assignment accounts to CSV.
 *
 * @Action(
 *   id = "social_task_assignment_vbo_export_assignments_action",
 *   label = @Translation("Export the selected assignees to CSV"),
 *   type = "task_assignment",
 *   view_id = "manage_all_task_assignments",
 *   display_id = "page",
 *   confirm = TRUE,
 *   confirm_form_route_name = "social_task_assignment_vbo.management.vbo.confirm",
 * )
 */
class SocialTaskAssignmentVboExportAssignments extends ExportUser {

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {
    /** @var \Drupal\social_event\EventEnrollmentInterface $entity */
    foreach ($entities as &$entity) {
      $entity = $this->getAccount($entity);
    }

    parent::executeMultiple($entities);
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
   * {@inheritdoc}
   *
   * To make sure the file can be downloaded, the path must be declared in the
   * download pattern of the social user export module.
   *
   * @see social_user_export_file_download()
   */
  protected function generateFilePath() : string {
    $hash = md5(microtime(TRUE));
    return 'export-assignments-' . substr($hash, 20, 12) . '.csv';
  }

  /**
   * Extract user entity from event enrollment entity.
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
