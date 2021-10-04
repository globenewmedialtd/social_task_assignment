<?php

namespace Drupal\social_task_assignment\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Task assignment edit forms.
 *
 * @ingroup social_task_assignment
 */
class TaskAssignmentForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\social_task_assignment\Entity\TaskAssignment $entity */
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label Task assignment.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Saved the %label Task assignment.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.task_assignment.canonical', ['task_assignment' => $entity->id()]);
  }

}
