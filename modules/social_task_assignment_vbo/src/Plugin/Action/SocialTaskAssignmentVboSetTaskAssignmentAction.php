<?php

namespace Drupal\social_task_assignment_vbo\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\social_task_assignment\TaskAssignmentInterface;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;

/**
 * Set task assignment to assigned entity action.
 *
 * @Action(
 *   id = "social_task_assignment_vbo_set_assignment_task_assignment_action",
 *   label = @Translation("Set assignment to true for selected task assignment entities"),
 *   type = "task_assignment",
 *   view_id = "task_assignment_selection_source",
 *   display_id = "page", 
 *   confirm = TRUE,
 *   confirm_form_route_name = "social_task_assignment_vbo.vbo.confirm",
 * )
 */
class SocialTaskAssignmentVboSetTaskAssignmentAction extends ViewsBulkOperationsActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /** @var \Drupal\social_task_assignment\TaskAssignmentInterface $entity */
    $entity->set('field_assigned', TRUE);
    $entity->save();
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $access = AccessResult::forbidden();

    if ($object instanceof TaskAssignmentInterface) {
      //$access = $object->access('edit', $account, TRUE);
      $access = AccessResult::allowedIfHasPermission($account, 'manage everything assignments');

      \Drupal::logger('social_task_assignment_vbo')->notice('<pre><code>' . print_r($access, TRUE) . '</code></pre>' );

      $task_id = $object->getFieldValue('field_task', 'target_id');
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($task_id);
      
    }

    return $return_as_object ? $access : $access->isAllowed();
  }



}
