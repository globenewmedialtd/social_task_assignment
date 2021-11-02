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
class SocialTaskAssignmentGroupAccessCheck implements AccessInterface {

  /**
   * Checks access to the event series view
   */
  public function access(Route $route, RouteMatch $route_match) {    

    $parameters = $route_match->getParameters();
    $group = $parameters->get('group');
   
    // In case we are on a group
    if (isset($group) && !is_object($group)) {
      $group = \Drupal::entityTypeManager()->getStorage('group')->load($group);      
    }    

    if (is_object($group)) {
      $bundle = $group->bundle();
      $group_type = \Drupal::entityTypeManager()->getStorage('group_type')->load($bundle);
      $plugins = $group_type->getInstalledContentPlugins();
      foreach ($plugins as $key => $plugin) {
        $bundle = $plugin->getEntityBundle();
        if ($bundle === 'task') {
          return AccessResult::allowed();
        }
      }
    }    

    return AccessResult::neutral();
    
  }

}
