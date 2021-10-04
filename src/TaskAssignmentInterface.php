<?php

namespace Drupal\social_task_assignment;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Task assignment entities.
 *
 * @ingroup social_task_assignment
 */
interface TaskAssignmentInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Task assignment status open.
   */
  const TASK_ASSIGNMENT_OPEN = 1;

  /**
   * Task assignment status submitted.
   */
  const TASK_ASSIGNMENT_SUBMITTED = 2;  

  /**
   * Task assignment status completed.
   */
  const TASK_ASSIGNMENT_COMPLETED = 3;  

  /**
   * Gets the Task assignment name.
   *
   * @return string
   *   Name of the Task assignment.
   */
  public function getName();

  /**
   * Sets the Task assignment name.
   *
   * @param string $name
   *   The Task assignment name.
   *
   * @return \Drupal\social_task_assignment\TaskAssignmentInterface
   *   The called Task assignment entity.
   */
  public function setName($name);

  /**
   * Gets the Task assignment creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Task assignment.
   */
  public function getCreatedTime();

  /**
   * Sets the Task assignment creation timestamp.
   *
   * @param int $timestamp
   *   The Task assignment creation timestamp.
   *
   * @return \Drupal\social_task_assignment\TaskAssignmentInterface
   *   The called Task assignment entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Task assignment published status indicator.
   *
   * Unpublished Task assignment are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Task assignment is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Task assignment.
   *
   * @param bool $published
   *   TRUE to set this Task assignment to published, FALSE for unpublished.
   *
   * @return \Drupal\social_task_assignment\TaskAssignmentInterface
   *   The called Task assignment entity.
   */
  public function setPublished($published);

}
