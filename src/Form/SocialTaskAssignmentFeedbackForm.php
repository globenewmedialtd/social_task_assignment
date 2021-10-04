<?php

namespace Drupal\social_task_assignment\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\node\Entity\Node;
use Drupal\social_task_assignment\Entity\TaskAssignment;
use Drupal\social_task_assignment\TaskAssignmentInterface;
use Drupal\user\UserInterface;
use Drupal\user\UserStorageInterface;
use Drupal\group\Entity\GroupContent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class SocialTaskAssignmentFeedbackForm.
 *
 * @package Drupal\social_task_assignment\Form
 */
class SocialTaskAssignmentFeedbackForm extends FormBase implements ContainerInjectionInterface {

  /**
   * The routing matcher to get the nid.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The node storage for iteration enrollments.
   *
   * @var \Drupal\Core\entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The entity type manager.
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
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {

    static $count = 0;
    $count++;

    return 'social_task_assignment_feedback_form_' .  $count;

  }

  /**
   * Constructs a Social Task Assignment Feedback Form.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The entity storage.
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   The user storage.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   */
  public function __construct(RouteMatchInterface $route_match, EntityStorageInterface $entity_storage, UserStorageInterface $user_storage, EntityTypeManagerInterface $entityTypeManager, AccountProxyInterface $currentUser, ConfigFactoryInterface $configFactory, ModuleHandlerInterface $moduleHandler) {
    $this->routeMatch = $route_match;
    $this->entityStorage = $entity_storage;
    $this->userStorage = $user_storage;
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $currentUser;
    $this->configFactory = $configFactory;
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('entity_type.manager')->getStorage('task_assignment'),
      $container->get('entity_type.manager')->getStorage('user'),
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('config.factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
    //$nid = $this->routeMatch->getRawParameter('node');
    $current_user = $this->currentUser;
    $uid = $current_user->id();

    // We check if the node is placed in a Group I am a member of. If not,
    // we are not going to build anything.
    if (!empty($nid)) {
      if (!is_object($nid) && !is_null($nid)) {
        $node = $this->entityTypeManager
          ->getStorage('node')
          ->load($nid);
      }

      //$groups = $this->getGroups($node);
      
      $conditions = [
        'field_account' => $current_user->id(),
        'field_task' => $node->id(),
      ];

      $task_assignment = $this->entityStorage->loadByProperties($conditions);

      if ($assignment = array_pop($task_assignment)) {
        if($assignment->field_status->value !== 'submitted') {
          return;
        }
      }

      $attributes = [
        'class' => [
          'btn',
          'btn-accent brand-bg-accent',
          'btn-lg btn-raised',
          'waves-effect',
        ],
      ];

      $form['task'] = [
        '#type' => 'hidden',
        '#value' => $nid,
      ];

      $form['feedback'] = [
        '#type' => 'text_format',
        '#format' => 'full_html',
        '#title' => $this->t('Feedback'),
      ];

      $form['completed'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Completed')
      ];      

  
    }

    return $form;
  
  }  

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $current_user = $this->currentUser;
    $uid = $current_user->id();
    $nid = $form_state->getValue('task');    

    $node = $this->entityTypeManager->getStorage('node')->load($nid);

    
    
  }
  

}