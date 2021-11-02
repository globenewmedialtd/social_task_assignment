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
 * @ViewsFilter("task_due_date_upcoming_filter")
 */
class TaskAssignedDueDateFilter extends InOperator {

  /**
   * {@inheritdoc}
   */
  protected $valueFormType = 'radios';

  /**
   * Flag to indicate Upcoming tasks.
   */
  const UPCOMING_TASKS = 1;

  /**
   * Flag to indicate overdue tasks.
   */
  const OVERDUE_TASKS = 2;

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

    $this->ensureMyTable();
    $now = $this->query->getDateFormat('NOW()', DateTimeItemInterface::DATETIME_STORAGE_FORMAT, TRUE);
    $configuration = [
      'table' => 'node__field_date_due',
      'field' => 'entity_id',
      'left_table' => '',
      'left_field' => 'nid',
    ];

    $join = Views::pluginManager('join')
      ->createInstance('standard', $configuration);
    $alias = $this->query->addRelationship($configuration['table'], $join, 'node_field_data');
    $field = "{$this->tableAlias}.{$this->realField}";
    $field = $this->query->getDateFormat($field, DateTimeItemInterface::DATETIME_STORAGE_FORMAT, TRUE);
   

    switch ($value) {
      case self::UPCOMING_TASKS:
        $this->query->addWhereExpression($this->options['group'], "({$field} >= {$now})");
        break;

      case self::OVERDUE_TASKS:
        $this->query->addWhereExpression($this->options['group'], "({$now} <= {$field})");
        break;
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
      self::UPCOMING_TASKS => $this->t('Upcoming tasks'),
      self::OVERDUE_TASKS => $this->t('Overdue tasks'),
    ];
  }

}
