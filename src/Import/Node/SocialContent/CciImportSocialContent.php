<?php

namespace Drupal\cci_cpnt_socialnetwork\Import\Node\SocialContent;

use Drupal\cci_cpnt_socialnetwork\Import\CciImportInterface;
use Drupal\Component\Utility\Unicode;

/**
 * Class CciImport
 * @package Drupal\cci_cpnt_socialnetwork\Import
 */
abstract class CciImportSocialContent implements CciImportInterface {

  /**
   * CciImportSocialContent constructor.
   */
  public function __construct() {
    self::$entity_type = static::getEntityType();
    self::$bundle = static::getBundle();
  }

  /**
   * @var array content
   */
  public static $content;

  /**
   * @var string $bundle
   */
  public static $bundle;

  /**
   * @var string $entity_type
   */
  public static $entity_type;

  /**
   * @var string $source_sitename
   */
  protected static $source_sitename;

  /**
   * This function process content
   *
   * @param array $content
   *   Content to process
   * @param $content_type
   * @param array | \DrushBatchContext $context
   *   Context.
   */
  public static function process($content, $content_type, &$context) {
    $context['message'] = "Process data from {$content_type}..";

    array_unshift($content, '');
    unset($content[0]);

    // Initiate multistep processing.
    if (empty($context['sandbox'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['current_entity'] = 0;
      $context['sandbox']['max'] = count($content);;
    }

    // Process the next 100 if there are at least 100 left. Otherwise,
    // we process the remaining number.
    $limit = 10;

    $result = range($context['sandbox']['current_entity'] + 1 , $context['sandbox']['current_entity'] + 1 + $limit);
    foreach ($result as $row) {
      if (!isset($content[$row])) {
        continue;
      }
      static::handleContent($content[$row]);
      $context['results'][] = $row ;
      // Update our progress information.
      $context['sandbox']['progress']++;
      $context['sandbox']['current_entity'] = $row;
    }

    $context['message'] = dt("Running @progress/@max {$content_type}", [
      '@progress' => $context['sandbox']['progress'],
      '@max' => $context['sandbox']['max']
    ]);

    // Inform the batch engine that we are not finished,
    // and provide an estimation of the completion level we reached.
    if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
      $context['finished'] = $context['sandbox']['progress'] >= $context['sandbox']['max'];
    }

  }

  /**
   * @param $properties
   * @param $entity_type
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\Core\Entity\EntityInterface[]|mixed
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected static function getEntityExist($properties, $entity_type) {
    $entityTypeManager = \Drupal::entityTypeManager();
    $entity = $entityTypeManager
      ->getStorage($entity_type)
      ->loadByProperties($properties);

    if (count($entity) == 1) {
      return reset($entity);
    }

    return FALSE;
  }

  /**
   * Return Properties to search entity.
   *
   * @return array
   */
  public static function getProperties() {
    $properties = [
      'type' => static::$bundle,
      'field_cci_id' => static::getId()
    ];

    return $properties;
  }

  /**
   * Return languages reference.
   *
   * @return int
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected static function getTypeOfNews() {
    $reference_id = NULL;

    $properties = [
      'vid' => 'type_of_news',
      'name'=> 'Réseaux sociaux'
    ];

    if ($entity = static::getEntityExist($properties, 'taxonomy_term')) {
      $reference_id = $entity->id();
    }

    return $reference_id;
  }

  /**
   * Generic string processor function.
   *
   * @param string $string
   * @param array $processors
   *
   * @return string
   */
  protected static function stringProcessor($string, array $processors) {
    foreach ($processors as $processor => $value) {
      switch ($processor) {
        case 'truncate':
          $string = Unicode::truncate($string, $value, '...');
          break;
        case 'strip_tags':
          $string = strip_tags($string);
          break;
      }
    }

    return $string;
  }

  /**
   * Return bundle property.
   *
   * @return mixed
   */
  abstract protected function getBundle();

  /**
   * Return entity_type property.
   *
   * @return mixed
   */
  abstract protected function getEntityType();

  /**
   * Handle content to create or update.
   *
   * @param $content
   * @return mixed
   */
  abstract protected static function handleContent($content);

  /**
   * Return an associative array with field and her value.
   *
   * @return mixed
   */
  abstract protected static function getFields();

  /**
   * Return post ID.
   * @return mixed
   */
  abstract protected static function getId();

  /**
   * Return post title.
   * @return mixed
   */
  abstract protected static function getTitle();

  /**
   * Return post link.
   * @return mixed
   */
  abstract protected static function getLinkToPost();

  /**
   * Return date post.
   * @return mixed
   */
  abstract protected static function getDate();

  /**
   * Return body post
   * @return mixed
   */
  abstract protected static function getBody();

  /**
   * Return post image.
   * @return mixed
   */
  abstract protected static function getImage();

}
