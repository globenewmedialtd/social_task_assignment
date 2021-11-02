<?php

namespace Drupal\social_task_assignment\Plugin\ActivityContext;

use Drupal\activity_creator\ActivityFactory;
use Drupal\activity_creator\Plugin\ActivityContextBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\Sql\QueryFactory;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\social_task_assignment\TaskAssignmentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'TaskOrganizerActivityContext' activity context.
 *
 * @ActivityContext(
 *   id = "task_organizer_activity_context",
 *   label = @Translation("Task Organizer activity context"),
 * )
 */
class TaskOrganizerActivityContext extends ActivityContextBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a TaskOrganizerActivityContext object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\Query\Sql\QueryFactory $entity_query
   *   The query factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\activity_creator\ActivityFactory $activity_factory
   *   The activity factory service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    QueryFactory $entity_query,
    EntityTypeManagerInterface $entity_type_manager,
    ActivityFactory $activity_factory,
    ModuleHandlerInterface $module_handler
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_query, $entity_type_manager, $activity_factory);

    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.query.sql'),
      $container->get('entity_type.manager'),
      $container->get('activity_creator.activity_factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getRecipients(array $data, $last_uid, $limit) {
    $recipients = [];

    // We only know the context if there is a related object.
    if (isset($data['related_object']) && !empty($data['related_object'])) {
      $related_entity = $this->activityFactory->getActivityRelatedEntity($data);
      if ($data['related_object'][0]['target_type'] === 'task_assignment') {
        /** @var \Drupal\social_task_assignment\TaskAssignmentInterface $task_assignment */
        $task_assignment = $this->entityTypeManager->getStorage('task_assignment')
          ->load($data['related_object'][0]['target_id']);
        // Send out task assignment notifications when people actually assigned.
        if ($task_assignment instanceof TaskAssignmentInterface && !$task_assignment->get('field_assigned')->isEmpty() && $task_assignment->get('field_assigned')->value != FALSE) {
          $recipients = $this->getRecipientTaskOrganizerFromEntity($related_entity, $data);       
        }
      }
    }

    // Remove the actor (user performing action) from recipients list.
    if (!empty($data['actor'])) {
      $key = array_search($data['actor'], array_column($recipients, 'target_id'), FALSE);
      if ($key !== FALSE) {
        unset($recipients[$key]);
      }
    }

    return $recipients;
  }

  /**
   * Returns Task Organizer recipient from Tasks.
   *
   * @param array $related_entity
   *   The related entity.
   * @param array $data
   *   The data.
   *
   * @return array
   *   An associative array of recipients, containing the following key-value
   *   pairs:
   *   - target_type: The entity type ID.
   *   - target_id: The entity ID.
   */
  public function getRecipientTaskOrganizerFromEntity(array $related_entity, array $data) {
    $recipients = [];

    // Don't return recipients if user assigns to own Task.
    $original_related_object = $data['related_object'][0];
    if (isset($original_related_object['target_type'])
      && $original_related_object['target_type'] === 'task_assignment'
      && $related_entity !== NULL) {
      $storage = $this->entityTypeManager->getStorage($related_entity['target_type']);
      $task = $storage->load($related_entity['target_id']);

      if ($task === NULL) {
        return $recipients;
      }

      $recipients[] = [
        'target_type' => 'user',
        'target_id' => $task->getOwnerId(),
      ];
    }

    // If there are any others we should add. Make them also part of the
    // recipients array.
    $this->moduleHandler->alter('activity_recipient_task_organizer', $recipients, $task, $original_related_object);

    return $recipients;
  }

}
