<?php

namespace Drupal\social_task_assignment_auto_assign;

use Drupal\social_task_assignment\Entity\TaskAssignment;
use Drupal\social_task_assignment\TaskAssignmentInterface;
use Drupal\group\Entity\Group;
use Drupal\group\Entity\GroupInterface;
use Drupal\group\Entity\GroupContentInterface;
use Drupal\group\Entity\GroupContent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\social_task_assignment_auto_assign\Entity\TaskAssignmentAutoAssignInterface;
use Drupal\social_task_assignment_auto_assign\Entity\TaskAssignmentAutoAssign;
use Drupal\Core\Datetime\DrupalDateTime;


/**
 * Defines Social Task Assignment Auto Assign.
 */
class SocialTaskAssignmentAutoAssignService {

  /** Entity type manager.
    *
    * @var \Drupal\Core\Entity\EntityTypeManagerInterface
    */
  protected $entityTypeManager;

  /**
   * Configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;  

  /**
   * Social Task Assignment Auto Assign Service constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Configuration factory.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, ConfigFactoryInterface $configFactory) {
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $configFactory;
  }

  /**
   * Get group content and look for tasks with auto assign enabled
   *
   * @return []
   *   Array of allowed recording access options.
   */
  public function newGroupMemberAutoAssign($group_id, $uid) {


    if ($group_id !== NULL) {
      /** @var \Drupal\group\Entity\GroupInterface $group */
      $group = $this->entityTypeManager
        ->getStorage('group')
        ->load($group_id);
    }
    
    if ($group instanceOf GroupInterface) {
      $plugin_id = 'group_node:task';
      $tasks = $group->getContentEntities($plugin_id);
      foreach ($tasks as $task) {
        if ($task instanceof NodeInterface) {         
          $nid = $task->id();                
          $this->autoAssign($nid, $uid);          
        }
      }
    }
   
  }

  /**
   * Get group content and look for tasks with auto assign enabled
   *
   * @return 
   *   TRUE or FALSE
   */
  protected function isTaskWithAutoAssignEnabled($nid) {
 
    $task = $this->entityTypeManager->getStorage('node')->load($nid);
    $auto_assign = FALSE;

    $social_task_auto_assign = \Drupal::service('social_task_assignment_auto_assign'); 
    if ($config = $social_task_auto_assign->getSocialTaskAutoAssignConfig($nid)) {
      $auto_assign = $config->getAutoAssign();  
    }

    if ($auto_assign) {
      return TRUE;
    }

    return FALSE;

  }

  /**
   * Delete assignments
   *
   * @return 
   *   TRUE or FALSE
   */
  public function removeAutoAssignEnabled($group_id, $uid) {

    if ($group_id !== NULL) {
      /** @var \Drupal\group\Entity\GroupInterface $group */
      $group = $this->entityTypeManager
        ->getStorage('group')
        ->load($group_id);
    }
    
    if ($group instanceOf GroupInterface) {
      $plugin_id = 'group_node:task';
      $tasks = $group->getContentEntities($plugin_id);
      foreach ($tasks as $task) {
        if ($task instanceof NodeInterface) {
          $nid = $task->id();
          $this->deleteAssignments($nid, $uid);          
        }
      }
    }   

  }

  /**
   * Delete assignments
   */
  protected function deleteAssignments($nid, $uid) {
    $task_assignment = $this->entityTypeManager->getStorage('task_assignment');
    $task = $this->entityTypeManager->getStorage('node');
    
    // Check if user has assigned the task
    $assigned = $task_assignment->loadByProperties([
      'field_account' => $uid,
      'field_task' => $nid,
    ]);
  
    foreach($assigned as $key => $record) {
      if ($record instanceof TaskAssignmentInterface) {
          $delete_assigned[$key] = $key;         
      }
    }
  
    $itemsToDelete = $task_assignment->loadMultiple($delete_assigned);
  
    // Loop through our entities and deleting them by calling by delete method.
    foreach ($itemsToDelete as $item) {
      $item->delete();
    }
  }

  /**
   * Check if we need to import members into assignments
   */
  public function checkIfTaskAssignments($nid) {

    $task_assignment = $this->entityTypeManager->getStorage('task_assignment');
    $task = $this->entityTypeManager->getStorage('node')->load($nid);
    $group_members = $this->getGroupMembers($task);
    
    // Check if anyone has assigned the task
    $assigned = $task_assignment->loadByProperties([
      'field_task' => $nid
    ]);

    if(!$assigned && is_array($group_members)) {
      // Auto assign them
      foreach($group_members as $uid) {
        $this->autoAssign($nid,$uid);
      }
    }

  }


  /**
   * Import all group members into task assignments
   */
  protected function getGroupMembers($task) {

    // Get the group from the $task node object
    $group = _social_group_get_current_group($task);

    if (isset($group)) {

      $members = $group->getMembers();
      foreach ($members as $member) {
        $user = $member->getUser();
        $uid = $user->id();
        $userids[$uid] = $uid;
      }

    }

    return $userids;

  }  

  /**
   * Create assignments with status FALSE
   */
  protected function autoAssign($nid, $uid) {

    $task_assignment = $this->entityTypeManager->getStorage('task_assignment');

    // For security reason we check first for existing assignment
    // to avoid duplicates
    // Check if user has assigned the task
    $assigned = $task_assignment->loadByProperties([
      'field_account' => $uid,      
      'field_task' => $nid
    ]);
    
    if (!$assigned) {

      $assignment = TaskAssignment::create([
        'user_id' => $uid,
        'field_task' => $nid,
        'field_account' => $uid,
      ]);
      $assignment->save();

    }

    if ($assigned_record = array_pop($assigned)) {     
      // We update when already assigend
      $assigned_record->field_assigned->value = TRUE;
      $assigned_record->save();
    }


  }

  /*
   * Get config
   */
  public function getSocialTaskAutoAssignConfig($nid) {

    $social_task_auto_assign = $this->entityTypeManager
      ->getStorage('task_assignment_auto_assign')
      ->load($nid);
    
    if ($social_task_auto_assign instanceof TaskAssignmentAutoAssignInterface) {
      return $social_task_auto_assign;
    }

    return FALSE;

  }

  /*
   * Create config
   */
  public function createSocialTaskAutoAssignConfig($nid, $auto_assign) {

    $config = TaskAssignmentAutoAssign::create([
      'id' => $nid,
    ]);
    $config->setAutoAssign($auto_assign);
    $config->setNode($nid);
    $config->save();

  }
  
  /*
   * Create config
   */
  public function updateSocialTaskAutoAssignConfig($nid, $auto_assign) {

    $social_task_auto_assign = $this->entityTypeManager
      ->getStorage('task_assignment_auto_assign')
      ->load($nid);
    
    if ($social_task_auto_assign instanceof TaskAssignmentAutoAssignInterface) {
      $social_task_auto_assign->setAutoAssign($auto_assign);
      $social_task_auto_assign->save();
    }

  }  

}
