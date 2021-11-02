<?php

namespace Drupal\social_task_assignment\Plugin\views\sort;

use Drupal\views\Plugin\views\sort\Date;

/**
 * Basic sort handler for overdue tasks.
 *
 * @ViewsSort("task_upcoming_due_date_sort")
 */
class TaskOverdue extends Date {

  /**
   * Called to add the sort to a query.
   */
  public function query() {
    $order = ($this->view->exposed_data["{$this->realField}_op"] == '>=') ? 'ASC' : 'DESC';
    $this->query->addOrderBy($this->tableAlias, $this->realField, $order);
  }

}
