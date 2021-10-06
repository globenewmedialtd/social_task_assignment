<?php

namespace Drupal\social_task_assignment\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Provides a 'GroupAddTaskBlock' block.
 *
 * @Block(
 *  id = "group_add_task_block",
 *  admin_label = @Translation("Group add task block"),
 * )
 */
class GroupAddTaskBlock extends BlockBase {
  /**
   * {@inheritdoc}
   *
   * Custom access logic to display the block.
   */
  public function blockAccess(AccountInterface $account)    {
    $group = _social_group_get_current_group();

    if (is_object($group)) {
      if ($group->hasPermission('create group_node:task entity', $account) && $account->hasPermission("create task content")) {
        if ($group->getGroupType()->id() === 'public_group') {
          $config = \Drupal::config('entity_access_by_field.settings');
          if ($config->get('disable_public_visibility') === 1 && !$account->hasPermission('override disabled public visibility')) {
            return AccessResult::forbidden();
          }
        }
        return AccessResult::allowed();
      }
    }
    // By default, the block is not visible.
    return AccessResult::forbidden();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $group = _social_group_get_current_group();

    if (is_object($group)) {
      $url = Url::fromRoute('entity.group_content.create_form', ['group' => $group->id(), 'plugin_id' => 'group_node:task']);
      $link_options = [
        'attributes' => [
          'class' => [
            'btn',
            'btn-primary',
            'btn-raised',
            'waves-effect',
            'brand-bg-primary',
          ],
        ],
      ];
      
      $url->setOptions($link_options);

      $build['content'] = Link::fromTextAndUrl(t('Create Task'), $url)->toRenderable();

      // Cache.
      $build['#cache']['contexts'][] = 'url.path';
      $build['#cache']['tags'][] = 'group:' . $group->id();
    }

    return $build;
  }
}
