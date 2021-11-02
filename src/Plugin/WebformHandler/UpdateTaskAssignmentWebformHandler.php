<?php

namespace Drupal\social_task_assignment\Plugin\WebformHandler;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\webform\Annotation\WebformHandler;
use Drupal\webform\Plugin\WebformHandler\EmailWebformHandler;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\Plugin\WebformHandlerInterface;
use Drupal\webform\Plugin\WebformHandlerMessageInterface;
use Drupal\webform\WebformSubmissionConditionsValidatorInterface;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;


/**
 * Webform submission entity post handler.
 *
 * @WebformHandler(
 *   id = "update_task_assignment",
 *   label = @Translation("Update task assignment"),
 *   category = @Translation("Tasks"),
 *   description = @Translation("Updates task assignments based on webform submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 *   tokens = FALSE,
 * )
 */ 

class UpdateTaskAssignmentWebformHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    return $instance;
  }


  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    parent::postSave($webform_submission,$update);
    //$state = $webform_submission->getWebform()->getSetting('results_disabled') ? WebformSubmissionInterface::STATE_COMPLETED : $webform_submission->getState();
    //if (in_array($state, $this->configuration['states'])) {
      //$this->executeAction($webform_submission);
    //}

    //
    //kint($webform_submission);
    $account = \Drupal::currentUser();
    $user_id = $account->id();
    
    // Task ID
    $source = $webform_submission->getSourceEntity();
    $nid = $source->id();
    $task_flow = $source->field_task_flow_value;

    if (isset($task_flow) && $task_flow === 'feedback') {
      $status = 'submitted';
    }
    else {
      $status = 'completed';
    }

    $owner  = $webform_submission->isOwner($account);
    $source_url = $webform_submission->getSourceUrl();
    $token_url = $webform_submission->getTokenUrl();
    $submission_id = $webform_submission->id();
    
    $conditions = [
      'field_account' => $user_id,
      'field_task' => $nid,
    ];

    $task_assignment = $this->entityTypeManager->getStorage('task_assignment')
      ->loadByProperties($conditions);

    // We need to set submission date
    // and status field.
    if ($assignment = array_pop($task_assignment)) {
      // For now we only want one submission.
      if ($assignment->field_status->value === 'open') {      
        $submissions = [$submission_id];       
        $assignment->set('field_status', $status);
        $assignment->set('field_webform_submissions',$submissions);
        $assignment->save();
      }  
    }
  }
}


