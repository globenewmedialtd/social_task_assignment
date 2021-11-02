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
  protected function userNotAssigned($user, $task) {    

    $conditions = [
      'field_account' => $user,
      'field_task' => $task,
      'field_assigned' => FALSE,
    ];

    return $conditions;
  
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

    if ($task === NULL) {
      $conditions = [
        'field_account' => $user,        
      ];
    }
    else {
      $conditions = [
        'field_account' => $user,
        'field_task' => $task,
      ];
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
   *   Returns all the assignments for a user.
   */
  public function getAllUserTaskAssignments($user) {
    $conditions = $this->userAssignments($user, NULL);

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
    
    $task_assigned = FALSE;
    
    $conditions = [
      'field_account' => $user,
      'field_task' => $task,
    ];

    $task_assignment = $this->entityTypeManager->getStorage('task_assignment')
      ->loadByProperties($conditions);

    if ($assignment = array_pop($task_assignment)) {
      if ($assignment->field_assigned->value == TRUE &&
          $assignment->field_status->value !== 'open'      
      ) {
        $task_assigned = TRUE; 
      } 
    }
    
    return $task_assigned;

  }

  /**
   * Custom check to see if a user has assignment.
   *
   * @param string $user
   *   The email or userid you want to check on.
   * @param int $task
   *   The task id you want to check on.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Returns a specific task assignment for a user.
   */
  public function getTaskWithNoUserAssignment($user, $task) {

    $task_not_assigned = FALSE;
    
    $conditions = [
      'field_account' => $user,
      'field_task' => $task,
    ];

    $task_assignment = $this->entityTypeManager->getStorage('task_assignment')
      ->loadByProperties($conditions);

    if ($assignment = array_pop($task_assignment)) {
      if ($assignment->field_assigned->value == FALSE) {
        $task_not_assigned = TRUE; 
      } 
    }
    
    return $task_not_assigned;


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
    $conditions = $this->taskAssignments($task);

    return $this->entityTypeManager->getStorage('task_assignment')
      ->loadByProperties($conditions);
  }

  /**
   * Task flow - We have to hide the form
   * under some rules.
   * 
   * @param int $submission_from_date
   *   Timestamp of field_date
   * @param int $submission_cut_off_date
   *   Timestamp of field_date_cut_off
   * @param \Drupal\
   * @return boolean TRUE | FALSE
   */
  public function showTaskSubmissionForm($submission_from_date, $submission_cut_off_date, $node) {
    
    // Get the current time
    $current_time = \Drupal::time()->getCurrentTime();
    // Get the current user
    $user = $this->currentUser;
    // show form status
    $show_form_status = [
      'status' => TRUE,
      'text' => '',
    ];


    // Hide the form when the submission from date is higher than today
    if ($submission_from_date && ($submission_from_date > $current_time)) {
      $show_form_status['status'] = FALSE;
      $text = t('The task will be open for submission by') . ' ';
      $date = \Drupal::service('date.formatter')->format($node->field_date->date->getTimestamp(), 'social_date_and_time');
      $show_form_status['text'] = $text . $date;
    }

    // Hide the form when the submission cut off date is smaller than today
    if ($submission_cut_off_date && ($submission_cut_off_date < $current_time)) {
      $show_form_status['status'] = FALSE;
      $text = t('The task has been closed for submissions after') . ' ';
      $date = \Drupal::service('date.formatter')->format($node->field_date_cut_off->date->getTimestamp(), 'social_date_and_time');
      $show_form_status['text'] = $text . $date;
    }

    
   
    return $show_form_status;

  }

}