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
    if ($route = $collection->get('view.manage_all_task_assignments.page_1')) {
      $route->setRequirement('_social_task_assignment_custom_access', 'Drupal\social_task_assignment\Access\SocialTaskAssignmentAccessCheck');
    }
  }
}
