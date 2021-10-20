<?php

namespace Drupal\social_task_assignment\Plugin\views\filter;

use Drupal\Core\Database\Query\Condition;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\views\Views;

/**
 * Filters tasks based on created or assigned status.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("task_assigned_or_created")
 */
class TaskAssignedOrCreated extends FilterPluginBase {

  /**
   * {@inheritdoc}
   */
  public function adminSummary() {
  }

  /**
   * {@inheritdoc}
   */
  protected function operatorForm(&$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function canExpose() {
    return FALSE;
  }

  /**
   * Query for the activity stream on the account pages.
   */
  public function query() {
    // Profile user.
    $account_profile = \Drupal::routeMatch()->getParameter('user');

    if (!is_null($account_profile) && is_object($account_profile)) {
      $account_profile = $account_profile->id();
    }

    // Join the task tables.
    $configuration = [
      'table' => 'task_assignment__field_task',
      'field' => 'field_task_target_id',
      'left_table' => 'node_field_data',
      'left_field' => 'nid',
      'operator' => '=',
    ];
    $join = Views::pluginManager('join')->createInstance('standard', $configuration);
    $this->query->addRelationship('task_assignment__field_task', $join, 'node_field_data');

    $configuration = [
      'table' => 'task_assignment_field_data',
      'field' => 'id',
      'left_table' => 'task_assignment__field_task',
      'left_field' => 'entity_id',
      'operator' => '=',
    ];
    $join = Views::pluginManager('join')->createInstance('standard', $configuration);
    $this->query->addRelationship('task_assignment_field_data', $join, 'node_field_data');

    $configuration = [
      'table' => 'task_assignment__field_status',
      'field' => 'entity_id',
      'left_table' => 'task_assignment__field_task',
      'left_field' => 'entity_id',
      'operator' => '=',
    ];
    $join = Views::pluginManager('join')->createInstance('standard', $configuration);
    $this->query->addRelationship('task_assignment__field_status', $join, 'node_field_data');

    $configuration = [
      'table' => 'task_assignment__field_account',
      'field' => 'entity_id',
      'left_table' => 'task_assignment__field_task',
      'left_field' => 'entity_id',
      'operator' => '=',
    ];
    $join = Views::pluginManager('join')->createInstance('standard', $configuration);
    $this->query->addRelationship('task_assignment__field_account', $join, 'node_field_data');

    $or_condition = new Condition('OR');

    // Check if the user is the author of the task.
    $task_creator = new Condition('AND');
    $task_creator->condition('node_field_data.uid', $account_profile, '=');
    $task_creator->condition('node_field_data.type', 'task', '=');
    $or_condition->condition($task_creator);

    // Or if the user assigned to the task.
    $assigned_to_task = new Condition('AND');
    $assigned_to_task->condition('task_assignment__field_account.field_account_target_id', $account_profile, '=');
    $or_condition->condition($assigned_to_task);

    $this->query->addWhere('assigned_or_created', $or_condition);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $cache_contexts = parent::getCacheContexts();

    // Since the Stream is different per url.
    if (!in_array('url', $cache_contexts)) {
      $cache_contexts[] = 'url';
    }

    return $cache_contexts;
  }

}
