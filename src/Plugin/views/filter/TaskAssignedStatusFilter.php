<?php

namespace Drupal\social_task_assignment\Plugin\views\filter;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

/**
 * Filter tasks by due date.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("task_status_filter")
 */
class TaskAssignedStatusFilter extends InOperator {

  /**
   * {@inheritdoc}
   */
  protected $valueFormType = 'radios';

  /**
   * Flag to indicate Upcoming tasks.
   */
  const OPEN_TASKS = 1;

  /**
   * Flag to indicate overdue tasks.
   */
  const SUBMITTED_TASKS = 2;

  /**
   * Flag to indicate overdue tasks.
   */
  const COMPLETED_TASKS = 3;  

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->definition['options callback'] = [$this, 'generateOptions'];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $value = (int) current($this->value);

    if (empty($value)) {
      return;
    }  

    // Because this is a special filter for one specific field
    // we set this field here hardcoded.

    $field = "task_assignment__field_status.field_status_value";       

    switch ($value) {
      case self::OPEN_TASKS:
        $this->query->addWhereExpression($this->options['group'], "({$field} = 'open'})");
        break;

      case self::SUBMITTED_TASKS:
        $this->query->addWhereExpression($this->options['group'], "({$field} = 'submitted')");
        break;

      case self::COMPLETED_TASKS:
        $this->query->addWhereExpression($this->options['group'], "({$field} = 'completed')");
    }
  }

  /**
   * Retrieves the allowed values for the date filter.
   *
   * @return array
   *   An array of allowed values in the form key => label.
   */
  public function generateOptions() {
    return [
      self::OPEN_TASKS => 'open',
      self::SUBMITTED_TASKS => 'submitted',
      self::COMPLETED_TASKS => 'completed',
    ];
  }

}
