<?php

namespace Drupal\cci_cpnt_socialnetwork\Import;

/**
 * Interface CciImportInterface
 * @package Drupal\cci_cpnt_socialnetwork\Import
 */
interface CciImportInterface {

  /**
   * @param $content
   * @param $content_type
   * @param array|\DrushBatchContext $context
   * @return mixed
   */
  public static function process($content, $content_type, &$context);

}
