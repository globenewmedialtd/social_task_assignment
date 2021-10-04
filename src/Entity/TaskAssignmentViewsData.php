<?php

namespace Drupal\social_task_assignment\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Task assignment entities.
 */
class TaskAssignmentViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['task_assignment_field_data']['table']['base'] = [
      'field' => 'id',
      'title' => $this->t('Task assignment'),
      'help' => $this->t('The Task assignment ID.'),
    ];

    return $data;
  }

}
