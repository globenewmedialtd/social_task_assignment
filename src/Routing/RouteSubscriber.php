<?php

namespace Drupal\social_task_assignment\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // We only want access to group tasks
    // if plugin enabled on group.
    if ($route = $collection->get('view.group_tasks.page_group_tasks')) {
      $route->setRequirement('_social_task_assignment_group_custom_access', 'Drupal\social_task_assignment\Access\SocialTaskAssignmentGroupAccessCheck');
    }
    if ($route = $collection->get('view.manage_all_task_assignments.page')) {
      $route->setRequirement('_social_task_assignment_manager_custom_access', 'Drupal\social_task_assignment\Access\SocialTaskAssignmentManagerAccessCheck');
    }
    if ($route = $collection->get('view.task_organisers.view_managers')) {
      $route->setRequirement('_social_task_assignment_custom_access', 'Drupal\social_task_assignment\Access\SocialTaskAssignmentAccessCheck');
    }    
    if ($route = $collection->get('view.user_tasks.page_profile')) {
      $requirements = $route->getRequirements();
      $requirements['_custom_access'] = '\Drupal\social_task_assignment\Controller\SocialTaskAssignmentController::myTaskAccess';
      $route->setRequirements($requirements);
    }
  }
}
