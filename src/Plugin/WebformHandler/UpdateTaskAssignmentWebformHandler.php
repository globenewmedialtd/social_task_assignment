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
 * Update a new task assignment entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "update_task_assignment",
 *   label = @Translation("Update Task assignment"),
 *   category = @Translation("Entity Update"),
 *   description = @Translation("Updates a new task assignment from Webform Submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class UpdateTaskAssignmentWebformHandler extends WebformHandlerBase {

/**
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * @var WebformSubmissionConditionsValidatorInterface
   */
  protected $conditionsValidator;

  /**
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param LoggerChannelFactoryInterface $logger_factory
   * @param ConfigFactoryInterface $config_factory
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param WebformSubmissionConditionsValidatorInterface $conditions_validator
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    LoggerChannelFactoryInterface $logger_factory,
    ConfigFactoryInterface $config_factory,
    EntityTypeManagerInterface $entity_type_manager,
    WebformSubmissionConditionsValidatorInterface $conditions_validator
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->loggerFactory = $logger_factory->get('update_task_assignment');
    $this->configFactory = $config_factory;
    $this->conditionsValidator = $conditions_validator;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * @param ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return ContainerFactoryPluginInterface|EmailWebformHandler|WebformHandlerBase|WebformHandlerInterface|WebformHandlerMessageInterface|static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory'),
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('webform_submission.conditions_validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    // In here, we perform our logic to manipulate and use the webform submission data however we want.
    // To access data from the webform submission, we call $webform_submission->getData(), we should be able to grab a part of the array that should be returned using a key.
    // The key will be the machine name of the field on the webform. 
    // So for example, if you have a field on the webform with a machine name of group, you code to get the value would be $webform_submission->getData()['group']
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
    $owner  = $webform_submission->isOwner($account);
    $source_url = $webform_submission->getSourceUrl();
    $token_url = $webform_submission->getTokenUrl();
    $submission_id = $webform_submission->id();
    //$owner = $webform_submission->getOwner();

    //\Drupal::logger('social_task_assignment')->notice('source id: ' . $source->id());

    //\Drupal::logger('social_task_assignment')->notice('webform submission id: ' . $webform_submission->id());
    //\Drupal::logger('social_task_assignment')->notice('Owner:<pre><code>' . print_r($user_id, TRUE) . '</code></pre>' );

    $conditions = [
      'field_account' => $user_id,
      'field_task' => $nid,
    ];

    $task_assignment = $this->entityTypeManager->getStorage('task_assignment')
      ->loadByProperties($conditions);

    // We need to set submission date
    // and status field.
    if ($assignment = array_pop($task_assignment)) {
      $submissions = [$submission_id];
      $timestamnp_current = \Drupal::time()->getCurrentTime();
      $assignment->set('field_submission_date',$timestamnp_current);
      $assignment->set('field_status','submitted');
      $assignment->set('field_webform_submissions',$submissions);
      $assignment->save();  
    }

  }


}


