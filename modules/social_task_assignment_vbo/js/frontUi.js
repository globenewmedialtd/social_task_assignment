/**
 * @file
 * Select-All Button functionality.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.social_task_assignment = {
    attach: function (context, settings) {
      $('.vbo-view-form').once('social-vbo-init').each(Drupal.socialTaskAssignmentViewsBulkOperationsFrontUi);
    }
  };

  /**
   * Callback used in {@link Drupal.behaviors.social_task_assignment}.
   */
  Drupal.socialTaskAssignmentViewsBulkOperationsFrontUi = function () {
    $('.vbo-view-form .select-all').addClass('form-no-label checkbox form-checkbox views-field-views-bulk-operations-bulk-form');
  };

})(jQuery, Drupal);
