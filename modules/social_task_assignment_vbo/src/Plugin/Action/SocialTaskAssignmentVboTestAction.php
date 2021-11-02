<?php

namespace Drupal\social_task_assignment_vbo\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;
use Drupal\social_task_assignment\TaskAssignmentInterface;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;

/**
 * Set task assignment to assigned entity action.
 *
 * @Action(
 *   id = "social_task_assignment_vbo_test_action",
 *   label = @Translation("Set assignment test"),
 *   type = "user",
 *   view_id = "manage_task_assignment_members",
 *   display_id = "page", 
 * )
 */
class SocialTaskAssignmentVboTestAction extends ViewsBulkOperationsActionBase {


  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity !== NULL) {

      // Here create the assignment
      

    }
  }


  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {
    foreach ($entities as $entity) {
      $this->execute($entity);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $access = AccessResult::forbidden();

    if ($object instanceof UserInterface) {
      //$access = $object->access('edit', $account, TRUE);
      $access = AccessResult::allowedIfHasPermission($account, 'manage everything assignments');

      //\Drupal::logger('social_task_assignment_vbo')->notice('<pre><code>' . print_r($access, TRUE) . '</code></pre>' );

      //$task_id = $object->getFieldValue('field_task', 'target_id');
      //$node = \Drupal::entityTypeManager()->getStorage('node')->load($task_id);
      
    }

    return $return_as_object ? $access : $access->isAllowed();

  }



}
