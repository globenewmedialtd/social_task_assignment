<?php

namespace Drupal\social_task_assignment_vbo\EventSubscriber;

use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Builds up the routes of event management forms.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * Constructs the service with DI.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $module_handler
   *   The module handler.
   */
  public function __construct(ModuleHandler $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
  }

  /**
   * Returns a set of route objects.
   *
   * @return \Symfony\Component\Routing\RouteCollection
   *   A route collection.
   */
  public function routes() {
    $collection = new RouteCollection();

    if ($this->moduleHandler->moduleExists('views_bulk_operations')) {

      // Add Assignees
      $route = new Route(
        '/node/{node}/manage-task-assignments/add-assignees/configure-action',
        [
          '_form' => '\Drupal\social_task_assignment_vbo\Form\SocialTaskAssignmentVboViewsBulkOperationsConfigureAction',
          '_title' => 'Configure action',
          'view_id' => 'task_assignment_selection_source',
          'display_id' => 'page',
        ],
        [
          '_views_bulk_operation_access' => 'TRUE',
        ]
      );
      $collection->add('social_task_assignment_vbo.vbo.execute_configurable', $route);

      $route = new Route(
        '/node/{node}/manage-task-assignments/add-assignees/confirm-action',
        [
          '_form' => '\Drupal\social_task_assignment_vbo\Form\SocialTaskAssignmentVboViewsBulkOperationsConfirmAction',
          '_title' => 'Confirm action',
          'view_id' => 'task_assignment_selection_source',
          'display_id' => 'page',
        ],
        [
          '_views_bulk_operation_access' => 'TRUE',
        ]
      );
      $collection->add('social_task_assignment_vbo.vbo.confirm', $route);
      
      // Manage Assigness
      $route = new Route(
        '/node/{node}/manage-task-assignments/configure-action',
        [
          '_form' => '\Drupal\social_task_assignment_vbo\Form\SocialTaskAssignmentVboManagementViewsBulkOperationsConfigureAction',
          '_title' => 'Configure action',
          'view_id' => 'manage_all_task_assignments',
          'display_id' => 'page',
        ],
        [
          '_views_bulk_operation_access' => 'TRUE',
        ]
      );
      $collection->add('social_task_assignment_vbo.management.vbo.execute_configurable', $route);

      $route = new Route(
        '/node/{node}/manage-task-assignments/confirm-action',
        [
          '_form' => '\Drupal\social_task_assignment_vbo\Form\SocialTaskAssignmentVboManagementViewsBulkOperationsConfirmAction',
          '_title' => 'Confirm action',
          'view_id' => 'manage_all_task_assignments',
          'display_id' => 'page',
        ],
        [
          '_views_bulk_operation_access' => 'TRUE',
        ]
      );
      $collection->add('social_task_assignment_vbo.management.vbo.confirm', $route);




    }

    return $collection;
  }

}
