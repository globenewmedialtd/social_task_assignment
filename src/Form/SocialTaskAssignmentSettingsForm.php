<?php

namespace Drupal\social_task_assignment\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Social Task Assignment settings for this site.
 */
class SocialTaskAssignmentSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_task_assignment_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['social_task_assignment.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['group_member_sync'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Group member sync'),
      '#default_value' => $this->config('social_task_assignment.settings')->get('group_member_sync'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('social_task_assignment.settings')
      ->set('group_member_sync', $form_state->getValue('group_member_sync'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
