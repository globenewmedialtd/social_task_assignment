<?php

namespace Drupal\social_task_assignment\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\group\Entity\GroupInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatch;

/**
 * Determines access to for event series view.
 */
class SocialTaskAssignmentAccessCheck implements AccessInterface {

  /**
   * Checks access to the event series view
   */
  public function access(Route $route, RouteMatch $route_match) {    

    $parameters = $route_match->getParameters();
    $node = $parameters->get('node');

    // In case we are on a node
    if (isset($node) && !is_object($node)) {
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($node);
    }
    
    if (is_object($node)) {
      if ($node->getType() === 'task') {
        return AccessResult::allowed();
      }
    } 

    return AccessResult::neutral();
    
  }

}
