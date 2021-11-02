<?php

namespace Drupal\social_task_assignment\Plugin\ActivityContext;

use Drupal\activity_creator\Plugin\ActivityContextBase;

/**
 * Provides a 'TaskOwnerActivityContext' activity context.
 *
 * @ActivityContext(
 *   id = "task_owner_activity_context",
 *   label = @Translation("Task Owner activity context"),
 * )
 */
class TaskOwnerActivityContext extends ActivityContextBase {

  /**
   * {@inheritdoc}
   */
  public function getRecipients(array $data, $last_uid, $limit) {
    $recipients = [];

    // We only know the context if there is a related object.
    if (isset($data['related_object']) && !empty($data['related_object'])) {
      $related_entity = $this->activityFactory->getActivityRelatedEntity($data);      
      $allowed_entity_types = ['node', 'post', 'comment'];
      if (in_array($related_entity['target_type'], $allowed_entity_types)) {        
        $recipients += $this->getRecipientOwnerFromEntity($related_entity, $data);
      }
    }

    // Remove the actor (user performing action) from recipients list.
    /*
    if (!empty($data['actor'])) {
      $key = array_search($data['actor'], array_column($recipients, 'target_id'), FALSE);
      if ($key !== FALSE) {
        unset($recipients[$key]);
      }
    }
    */

    \Drupal::logger('social_task_assignment')->warning('Recipients:<pre><code>' . print_r($recipients, TRUE) . '</code></pre>');

    

    return $recipients;
  }

  /**
   * Returns owner recipient from entity.
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
  public function getRecipientOwnerFromEntity(array $related_entity, array $data) {
    $recipients = [];

    $entity_storage = $this->entityTypeManager->getStorage($related_entity['target_type']);
    $entity = $entity_storage->load($related_entity['target_id']);

    \Drupal::logger('social_task_assignment')->warning('Entity type: ' . $entity->bundle());

    // It could happen that a notification has been queued but the content
    // has since been deleted. In that case we can find no additional
    // recipients.
    if (!$entity) {
      return $recipients;
    }

    // Don't return recipients if user comments on own content.
    $original_related_object = $data['related_object'][0];

    \Drupal::logger('social_task_assignment')->warning('Original Related object:<pre><code>' . print_r($original_related_object, TRUE) . '</code></pre>');
    

    if (isset($original_related_object['target_type']) && $original_related_object['target_type'] === 'task_assignment') {
      $storage = $this->entityTypeManager->getStorage($original_related_object['target_type']);
      $original_related_entity = $storage->load($original_related_object['target_id']);

      \Drupal::logger('social_task_assignment')->warning('Original Related entity:' . $original_related_entity->bundle());
    


      // In the case where a user is added by an event manager we'll need to
      // check on the enrollment status. If the user is not really enrolled we
      // should skip sending the notification.
      if ($original_related_entity->get('field_assigned')->value === TRUE) {
        return $recipients;
        \Drupal::logger('social_task_assignment')->warning('field_assigend set to TRUE');
      }

      if (!empty($original_related_entity) && $original_related_entity->getAccount() !== NULL) {
        \Drupal::logger('social_task_assignment')->warning('we are in task assignment');
        $recipients[] = [
          'target_type' => 'user',
          'target_id' => $original_related_entity->getAccount(),
        ];

      

        return $recipients;
      }
    }

    $recipients[] = [
      'target_type' => 'user',
      'target_id' => $entity->getOwnerId(),
    ];

    // If there are any others we should add. Make them also part of the
    // recipients array.
    $this->moduleHandler->alter('activity_recipient_task_owner', $recipients, $original_related_object);

    return $recipients;
  }

}