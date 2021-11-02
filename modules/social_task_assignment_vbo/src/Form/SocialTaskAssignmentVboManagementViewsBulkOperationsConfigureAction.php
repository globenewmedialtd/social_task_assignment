<?php

namespace Drupal\social_task_assignment_vbo\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views_bulk_operations\Form\ConfigureAction;

/**
 * Action configuration form.
 */
class SocialTaskAssignmentVboManagementViewsBulkOperationsConfigureAction extends ConfigureAction {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $view_id = 'manage_all_task_assignments', $display_id = 'page') {
    return parent::buildForm($form, $form_state, 'manage_all_task_assignments', 'page');
  }

}
