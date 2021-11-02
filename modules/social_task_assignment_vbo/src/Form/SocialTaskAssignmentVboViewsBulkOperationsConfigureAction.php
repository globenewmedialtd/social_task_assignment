<?php

namespace Drupal\social_task_assignment_vbo\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views_bulk_operations\Form\ConfigureAction;

/**
 * Action configuration form.
 */
class SocialTaskAssignmentVboViewsBulkOperationsConfigureAction extends ConfigureAction {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $view_id = 'task_assignment_selection_source', $display_id = 'page') {
    return parent::buildForm($form, $form_state, 'task_assignment_selection_source', 'page');
  }

}
