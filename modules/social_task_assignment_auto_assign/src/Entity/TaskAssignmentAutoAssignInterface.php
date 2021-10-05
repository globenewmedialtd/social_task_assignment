<?php

namespace Drupal\social_task_assignment_auto_assign\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Task assignment auto assign entities.
 */
interface TaskAssignmentAutoAssignInterface extends ConfigEntityInterface {

  // Add get/set methods for your configuration properties here.
  public function getAutoAssign();

  public function setAutoAssign(bool $auto_assign);

  public function getNode();

  public function setNode(string $node);


}
