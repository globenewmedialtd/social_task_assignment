<?php

namespace Drupal\social_task_assignment\Element;

use Drupal\Core\Entity\EntityReferenceSelection\SelectionWithAutocreateInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\social_core\Entity\Element\EntityAutocomplete;
use Drupal\Component\Utility\Tags;
use Drupal\node\NodeInterface;
use Drupal\social_task_assignment\TaskAssignmentInterface;

/**
 * Provides an Assign member autocomplete form element.
 *
 * The #default_value accepted by this element is either an entity object or an
 * array of entity objects.
 *
 * @FormElement("social_assignment_entity_autocomplete")
 */
class SocialAssignmentAutocomplete extends EntityAutocomplete {

  /**
   * Form element validation handler for entity_autocomplete elements.
   */
  public static function validateEntityAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form, $select2 = FALSE) {
    $duplicated_values = $value = [];

    // Load the current Task assignments so we can check duplicates.
    $storage = \Drupal::entityTypeManager()->getStorage('task_assignment');

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      // You can get nid and anything else you need from the node object.
      $nid = $node->id();
    }
    elseif (!is_object($node)) {
      $nid = $node;
    }

    $input_values = $element['#value'];

    if ($select2 !== TRUE) {
      // Grab all the input values so we can get the ID's out of them.
      $input_values = Tags::explode($element['#value']);
    }

    foreach ($input_values as $input) {
      $match = static::extractEntityIdFromAutocompleteInput($input);
      // If we use the select 2 widget then we already got a nice array.
      if ($select2 === TRUE) {
        $match = $input;
      }
      if ($match === NULL) {
        $options = $element['#selection_settings'] + [
          'target_type' => $element['#target_type'],
          'handler' => $element['#selection_handler'],
        ];

        /** @var /Drupal\Core\Entity\EntityReferenceSelection\SelectionInterface $handler */
        $handler = \Drupal::service('plugin.manager.entity_reference_selection')->getInstance($options);
        $autocreate = (bool) $element['#autocreate'] && $handler instanceof SelectionWithAutocreateInterface;
        // Try to get a match from the input string when the user didn't use
        // the autocomplete but filled in a value manually.
        // Got this from the parent::validateEntityAutocomplete.
        $match = static::matchEntityByTitle($handler, $input, $element, $form_state, !$autocreate);
      }

      if ($match !== NULL) {
        $value[$match] = [
          'target_id' => $match,
        ];

        $assignments = $storage->loadByProperties(['field_task' => $nid, 'field_account' => $match]);

        // User is already a member, add it to an array for the Form element
        // to render an error after all checks are gone.
        if (!empty($assignments)) {
          $duplicated_values[] = $input;
        }

        // We need set "validate_reference" for element to prevent
        // receive notice Undefined index #validate_reference.
        if (!isset($element['#validate_reference'])) {
          $element['#validate_reference'] = FALSE;
        }

        // Validate input for every single user. This way we make sure that
        // The element validates one, or more users added in the autocomplete.
        // This is because we don't allow adding multiple users at once,
        // so we need to validate single users, if they all pass we can add
        // them all in the _social_group_action_form_submit.
        parent::validateEntityAutocomplete($element, $form_state, $complete_form);
      }
    }

    // If we have duplicates, provide an error message.
    if (!empty($duplicated_values)) {
      $usernames = implode(', ', $duplicated_values);

      $message = \Drupal::translation()->formatPlural(count($duplicated_values),
        "@usernames is already assigned, you can't add them again",
        "@usernames are already assigned, you can't add them again",
        ['@usernames' => $usernames]
      );

      // We have to kick in a form set error here, or else the
      // GroupContentCardinalityValidator will kick in and show a faulty
      // error message. Alter this later when Group supports multiple members.
      $form_state->setError($element, $message);
      return;
    }

    if ($value) {
      $form_state->setValue('entity_id_new', $value);
      $form_state->setValue('node_id', $nid);
    }
  }

}
