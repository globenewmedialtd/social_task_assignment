<?php

namespace Drupal\social_task_assignment\Plugin\views\sort;

use Drupal\views\Plugin\views\sort\Date;
use Drupal\social_task_assignment\Plugin\views\filter\TaskAssignedDueDateFilter as TaskAssignedDueDateFilter;

/**
 * Basic sort handler for passed tasks.
 *
 * @ViewsSort("social_task_assignment_sorting")
 */
class TaskDueDate extends Date {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $order = ($this->view->exposed_data[$this->realField] == TaskAssignedDueDateFilter::UPCOMING_TASKS) ? 'ASC' : 'DESC';
    $this->query->addOrderBy($this->tableAlias, $this->realField, $order);
  }

}
