<?php

/**
 * @file
 * Builds placeholder replacement tokens for Social Task assignment module.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Link;
use Drupal\social_task_assignment\TaskAssignmentInterface;

/**
 * Implements hook_token_info().
 */
function social_task_assignment_token_info() {

  $type = [
    'name' => t('Social Task Assignment'),
    'description' => t('Tokens from the Social task assignment module.'),
  ];

  $social_task_assignment['task_iam_organizing'] = [
    'name' => t('URL of the Task I organize.'),
    'description' => t('URL of the Task I organize.'),
  ];

  $social_task_assignment['assigned_user'] = [
    'name' => t('Username of user assigned to the task.'),
    'description' => t('Username of user assigned to the Task.'),
  ];

  $social_task_assignment['assigned_task_url'] = [
    'name' => t('Url of the Task a user assigned to.'),
    'description' => t('Url of the Task a user assigned to.'),
  ];

  return [
    'types' => ['social_task_assignment' => $type],
    'tokens' => [
      'social_task_assignment' => $social_task_assignment,
    ],
  ];
}

/**
 * Implements hook_tokens().
 */
function social_task_assignment_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];
  if ($type === 'social_task_assignment' && !empty($data['message'])) {
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'task_iam_organizing':
          $assignment = $data['message']->getFieldValue('field_message_related_object', 'target_id');
          if (!empty($assignment)) {
            $link = get_link_to_task_from_assignment($assignment, FALSE);

            if ($link !== NULL) {
              $replacements[$original] = $link;
            }
          }
          break;

        case 'assigned_task_url':
          $assignment = $data['message']->getFieldValue('field_message_related_object', 'target_id');
          if (!empty($assignment)) {
            $link = get_link_to_task_from_assignment($assignment, TRUE);

            if ($link !== NULL) {
              $replacements[$original] = $link;
            }
          }
          break;

        case 'assigned_user':
          $id = $data['message']->getFieldValue('field_message_related_object', 'target_id');
          if (!empty($id)) {
            $assignment_name = get_name_from_assignment($id);

            if ($assignment_name !== NULL) {
              $replacements[$original] = $assignment_name;
            }
          }
          break;
      }
    }
  }

  return $replacements;
}

/**
 * Render a link to a task as full link with title or just absolute as string.
 *
 * @param int $object_id
 *   The ID of the task assignment.
 * @param bool $as_link
 *   True if you want the full link otherwise just a canonical URL string.
 *
 * @return string|null
 *   the Url.
 */
function get_link_to_task_from_assignment($object_id, $as_link = FALSE) {
  $entity_storage = \Drupal::entityTypeManager()
    ->getStorage('task_assignment');
  /** @var \Drupal\social_task_assignment\Entity\TaskAssignment $entity */
  $entity = $entity_storage->load($object_id);
  if ($entity !== NULL) {
    $task_id = $entity->getFieldValue('field_task', 'target_id');
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    /** @var \Drupal\node\Entity\Node $task */
    $task = $storage->load($task_id);

    // Check if the event still exists.
    if ($task !== NULL) {
      if ($as_link) {
        $url_string = $task->toUrl('canonical')->toString(TRUE);
        return $url_string->getGeneratedUrl();
      }

      return Link::fromTextAndUrl($task->getTitle(), $task->toUrl('canonical'))->toString();
    }
  }

  return NULL;
}

/**
 * Get the user display name from an Assignment.
 *
 * @param int $id
 *   the Task assignment entity ID.
 *
 * @return array|\Drupal\Component\Render\MarkupInterface|mixed|null|string
 *   Markup for the Username.
 *
 * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
 * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
 */
function get_name_from_assignment($id) {
  $entity_storage = \Drupal::entityTypeManager()
    ->getStorage('task_assignment');
  /** @var \Drupal\social_task_assignment\Entity\TaskAssignment $entity */
  $assignment = $entity_storage->load($id);

  if ($assignment instanceof TaskAssignmentInterface) {
    // If there is a Uid. Lets load the user and return his display name.
    if ($assignment !== NULL &&
      $assignment->hasField('field_account') &&
      $assignment->getFieldValue('field_account', 'target_id') > 0) {
      $entity_storage = \Drupal::entityTypeManager()
        ->getStorage('user');
      /** @var \Drupal\user\Entity\User $user */
      $user = $entity_storage->load($assignment->getFieldValue('field_account', 'target_id'));
      $assignment_name = $user->getDisplayName();
    }
  }

  return $assignment_name;
  
}
