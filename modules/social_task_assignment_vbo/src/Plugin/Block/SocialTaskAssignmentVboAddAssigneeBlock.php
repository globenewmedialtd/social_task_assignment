<?php

namespace Drupal\social_task_assignment_vbo\Plugin\Block;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'SocialTaskAssignmentVboAddAssigneeBlock' block.
 *
 * @Block(
 *  id = "social_task_assignment_vbo_add_assignee_block",
 *  admin_label = @Translation("Social Task Assignment VBO add assignee block"),
 * )
 */
class SocialTaskAssignmentVboAddAssigneeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;


  /**
   * SocialTaskAssignmentVboAddAssigneeBlock constructor.
   *
   * @param array $configuration
   *   The given configuration.
   * @param string $plugin_id
   *   The given plugin id.
   * @param mixed $plugin_definition
   *   The given plugin definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $routeMatch) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $routeMatch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {

    return AccessResult::allowed();

    /*
    try {
      return $this->accessHelper->eventFeatureAccess();
    }
    catch (InvalidPluginDefinitionException $e) {
      return AccessResult::neutral();
    }
    catch (PluginNotFoundException $e) {
      return AccessResult::neutral();
    }
    */
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    // Read our task from the URL
    $task = social_task_assignment_get_current_task();

    if (isset($task)) {
      $link = [
        '#type' => 'link',
        '#title' => $this->t('Back to Task assignment'),
        '#weight' => 1,
        '#url' => Url::fromRoute('view.manage_all_task_assignments.page',['node' => $task->id()]),
        '#attributes' => ['class'=> 'btn btn-block btn-default button button--primary js-form-submit form-submit'],
      ];

      $build['content'] = $link;
       
      $build['#cache'] = [
        'keys' => ['social_task_assignment_vbo_add_assignee_block', 'node', $task->id()],
        'contexts' => ['user'],
      ];


    }
    
    return $build;

  }


  public function getCacheTags() {

    // Read our task from the URL
    $task = social_task_assignment_get_current_task();
    //With this when your node change your block will rebuild
    if ($task) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $task->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

}