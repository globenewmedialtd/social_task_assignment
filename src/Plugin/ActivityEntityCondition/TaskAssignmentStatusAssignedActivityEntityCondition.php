<?php

namespace Drupal\social_task_assignment\Plugin\ActivityEntityCondition;

use Drupal\activity_creator\Plugin\ActivityEntityConditionBase;

/**
 * Provides a 'TaskAssignmentStatusAssigned' activity condition.
 *
 * @ActivityEntityCondition(
 *  id = "task_assignment_assigned",
 *  label = @Translation("Task assignment changed to assigned"),
 *  entities = {"task_assignment" = {}}
 * )
 */
class TaskAssignmentStatusAssignedActivityEntityCondition extends ActivityEntityConditionBase {

  /**
   * {@inheritdoc}
   */
  public function isValidEntityCondition($entity) {
    if ($entity->getEntityTypeId() === 'task_assignment') {
      if ($entity->get('field_assigned')->value == TRUE) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
