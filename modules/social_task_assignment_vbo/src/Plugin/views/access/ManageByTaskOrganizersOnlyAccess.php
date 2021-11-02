<?php

namespace Drupal\social_task_assignment_vbo\Plugin\views\access;

use Drupal\Core\Session\AccountInterface;
use Drupal\views\Plugin\views\access\AccessPluginBase;
use Symfony\Component\Routing\Route;

/**
 * Manage by task organizers only access plugin.
 *
 * @ingroup views_access_plugins
 *
 * @ViewsAccess(
 *   id = "manage_by_task_organizers_only",
 *   title = @Translation("Manage by task organizers / tutors only"),
 *   help = @Translation("Access to the task manage all assignment page.")
 * )
 */
class ManageByTaskOrganizersOnlyAccess extends AccessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    return $account->isAuthenticated();
  }

  /**
   * {@inheritdoc}
   */
  public function alterRouteDefinition(Route $route) {
    $route->setRequirement('_custom_access', '\Drupal\social_task_assignment_vbo\Controller\SocialTaskAssignmentVboController::access');
  }

}
