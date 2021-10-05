<?php

namespace Drupal\social_task_assignment_auto_assign\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;


/**
 * Defines the Task Assignment Auto Assign entity.
 *
 * @ConfigEntityType(
 *   id = "task_assignment_auto_assign",
 *   label = @Translation("Task assignment auto assign setting"),
 *   config_prefix = "task_assignment_auto_assign",
 *   entity_keys = {
 *     "id" = "id",
 *     "auto_assign" = "auto_assign",
 *     "uuid" = "uuid"
 *   },
 * )
 */
class TaskAssignmentAutoAssign extends ConfigEntityBase implements TaskAssignmentAutoAssignInterface {

  /**
   * The ID of the setting.
   *
   * @var string
   */
  protected $id;

  /**
   * The Auto assign field.
   *
   * @var string
   */
  protected $auto_assign;

  /**
   * The id of the task node.
   *
   * @var string
   */
  protected $node;


  /**
   * {@inheritdoc}
   */
  public function getAutoAssign() {
    return $this->auto_assign;
  }

  /**
   * {@inheritdoc}
   */
  public function setAutoAssign(bool $auto_assign) {
    $this->auto_assign = $auto_assign;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNode() {
    return $this->node;
  }

  /**
   * {@inheritdoc}
   */
  public function setNode(string $node) {
    $this->node = $node;
    return $this;
  }

}
