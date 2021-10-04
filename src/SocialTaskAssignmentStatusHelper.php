<?php

namespace Drupal\social_task_assignment;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Class SocialTaskAssignmentStatusHelper.
 *
 * Providers service to get the assignments for a user.
 */
class SocialTaskAssignmentStatusHelper {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * EventInvitesAccess constructor.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Configuration factory.
   */
  public function __construct(RouteMatchInterface $routeMatch, EntityTypeManagerInterface $entityTypeManager, AccountProxyInterface $currentUser, ConfigFactoryInterface $configFactory) {
    $this->routeMatch = $routeMatch;
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $currentUser;
    $this->configFactory = $configFactory;
  }

  /**
   * Custom check to see if a user has assignments.
   *
   * @param string $user
   *   The email or userid you want to check on.
   * @param int $task
   *   The task id you want to check on, use 0 for all.
   *
   * @return array
   *   Returns the conditions for which to search task assignments on.
   */
  public function userAssignments($user, $task) {
    $current_user = $this->currentUser;
    $uid = $current_user->id();
    $nid = $this->routeMatch->getRawParameter('node');

    if ($task) {
      $nid = $task;
    }

    // If there is no trigger get the enrollment for the current user.
    $conditions = [
      'field_account' => $uid,
      'field_task' => $nid,
    ];

    if ($user) {
      // Always assume the trigger is emails unless the ID is a user.
      $conditions = [
        'field_email' => $user,
        'field_task' => $nid,
      ];

      /** @var \Drupal\user\Entity\User $user */
      $account = User::load($user);
      if ($account instanceof UserInterface) {
        $conditions = [
          'field_account' => $account->id(),
          'field_task' => $nid,          
        ];
      }
    }

    return $conditions;
  }

  /**
   * Custom check to get all assignments for a task.
   *
   * @param int $task
   *   The task id you want to check on.
   *
   * @return array
   *   Returns the conditions for which to search task assignments on.
   */
  public function taskAssignments($task) {
    $nid = $this->routeMatch->getRawParameter('node');

    if ($task) {
      $nid = $task;
    }

    // If there is no trigger get the assignemtns for the current user.
    $conditions = [
      'field_task' => $nid,
    ];

    return $conditions;
  }

  /**
   * Custom check to see if a user has assignments.
   *
   * @param string $user
   *   The email or userid you want to check on.
   *
   * @return bool|\Drupal\Core\Entity\EntityInterface|mixed
   *   Returns all the enrollments for a user.
   */
  public function getAllUserTaskAssignments($user) {
    $conditions = $this->userAssignments($user, NULL);

    unset($conditions['field_task']);

    return $this->entityTypeManager->getStorage('task_assignment')
      ->loadByProperties($conditions);
  }

  /**
   * Custom check to see if a user has assignments.
   *
   * @param string $user
   *   The email or userid you want to check on.
   * @param int $task
   *   The task id you want to check on.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Returns a specific task assignment for a user.
   */
  public function getTaskAssignments($user, $task) {
    $conditions = $this->userAssignments($user, $task);

    return $this->entityTypeManager->getStorage('task_assignment')
      ->loadByProperties($conditions);
  }

  /**
   * Custom check to get all assignments for a task.
   *
   * @param int $task
   *   The task id you want to check on.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Returns all assignments for a task.
   */
  public function getAllTaskAssignments($task) {
    $conditions = $this->eventEnrollments($task);

    return $this->entityTypeManager->getStorage('task_assignment')
      ->loadByProperties($conditions);
  }

}
