<?php

namespace Drupal\ymca_page_context;

use Drupal\node\Entity\Node;

/**
 * Controls in what context page header should be rendered.
 */
class PageContextService {
  private $context;

  /**
   * Constructs a new PageContextService.
   */
  public function __construct() {
    $this->context = NULL;
  }

  /**
   * Overrides current context.
   *
   * @param Node $node
   *   Context node entity.
   */
  public function setContext(Node $node) {
    $this->context = $node;
  }

  /**
   * Returns current context.
   *
   * @return mixed
   *   An instance of \Drupal\node\Entity\Node or null.
   */
  public function getContext() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      if (in_array($node->bundle(), ['camp', 'location'])) {
        return $node;
      }
      if ($node->hasField('field_related_camps_locations')) {
        if ($values = $node->field_related_camps_locations->getValue()) {
          foreach ($values as $value) {
            if ($id = $value['target_id']) {
              return \Drupal::entityTypeManager()->getStorage('node')->load($id);
            }
          }
        }
      }
      if ($node->hasField('field_related')) {
        if ($value = $node->field_related->getValue()) {
          if ($id = $value[0]['target_id']) {
            return \Drupal::entityTypeManager()->getStorage('node')->load($id);
          }
        }
      }
      if ($node->hasField('field_site_section')) {
        if ($value = $node->field_site_section->getValue()) {
          if ($id = $value[0]['target_id']) {
            return \Drupal::entityTypeManager()->getStorage('node')->load($id);
          }
        }
      }
    }

    return $this->context;
  }

}
