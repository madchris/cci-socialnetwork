<?php

namespace Drupal\cci_cpnt_socialnetwork\Import\Node\SocialContent;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Class Region
 */
class CciFacebookContent extends CciImportSocialContent {

  /**
   * @var string $bundle
   */
  public static $bundle = 'external_social_content';

  /**
   * @var string $entity_type
   */
  public static $entity_type = 'node';
  /**
   * @var string $source_sitename
   */
  protected static $source_sitename = 'facebook';

  /**
   * CciImportSocialContent constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Return entity mapping field.
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws EntityStorageException
   */
  protected static function getFields() {
    return [
      'type' => self::$bundle,
      'field_cci_id' => [['value' => self::getId()]],
      'title' => self::getTitle([
        'truncate' => 250,
        'strip_tags' => TRUE,
      ]),
      'body' => [['value' => self::getBody()]],
      'field_date' => [['value' => self::getDate()]],
      'created' => self::getDate(),
      'field_image' => self::getImage(),
      'field_image_replace_video' => self::getImage(),
      'field_link' => self::getLinkToPost(),
      'field_type_of_news' => [['target_id' => self::getTypeOfNews()]],
      'source_sitename' => [['value' => self::$source_sitename]],
      'status' => NodeInterface::PUBLISHED,
    ];
  }

  /**
   * @param  $content
   * @throws EntityStorageException
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public static function handleContent($content) {
    self::$content = $content;

    if (!empty(self::$content->id) && $entity = self::getEntityExist(self::getProperties(), self::$entity_type)) {
      // Update Content.
      foreach (self::getFields() as $name => $value) {
        $entity->set($name, $value);
      }

      $entity->updateOriginalValues();
    }
    else {
      // Create Content.
      $entity = Node::create(self::getFields());
    }

    // Save Content.
    if ($entity) {
      $entity->save();
    }

  }

  /**
   * Return ID.
   *
   * @return string | null
   */
  protected static function getId() {
    return isset(self::$content->id) ? self::$content->id : NULL;
  }

  /**
   * Return first name.
   *
   * @param array $processors
   * @return string | null
   */
  protected static function getTitle(array $processors = ['truncate' => 127]) {
    $string = isset(self::$content->name) ? self::$content->name : self::$content->message;

    if (!empty($string)) {
      return !empty($processors) ? self::stringProcessor($string, $processors) : $string;
    }

    return '';
  }

  /**
   * Return first name.
   *
   * @return string | null
   */
  protected static function getLinkToPost() {
    return isset(self::$content->permalink_url) ? self::$content->permalink_url : NULL;
  }

  /**
   * Return first name.
   *
   * @return string | null
   */
  protected static function getDate() {
    return isset(self::$content->created_time) ? strtotime(self::$content->created_time->date) : NULL;
  }

  /**
   * Return mail.
   *
   * @return string | null
   */
  protected static function getProvider() {
    return isset(self::$source_sitename) ? self::$source_sitename : NULL;
  }

  /**
   * Return biography.
   *
   * @return string | null
   */
  protected static function getBody() {
    return isset(self::$content->description) ? self::$content->description : NULL;
  }

  /**
   * @throws EntityStorageException
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected static function getImage() {

    if (!isset(self::$content->full_picture)) {
      return NULL;
    }

    $source = file_get_contents(self::$content->full_picture);
    if (empty($source)) {
      return NULL;
    }

    $user = \Drupal::currentUser();
    $uri  = file_unmanaged_save_data($source, 'public://' . self::getId() . '.jpg', FILE_EXISTS_REPLACE);

    $reference_ids = [];
    // check first if the file exists for the uri
    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uri' => $uri]);
    $file = reset($files);

    // if not create a file
    if (!$file) {
      $file = File::create([
        'uri' => $uri,
        'uid' => $user->id(),
        'status' => FILE_STATUS_PERMANENT,
      ]);
      $file->save();
    }

    $reference_ids[]= [
      'target_id' => $file->id(),
      'alt' => self::getTitle(),
      'title' => self::getTitle()
    ];

    return $reference_ids;
  }

  /**
   * Return bundle property.
   *
   * @return mixed
   */
  protected function getBundle() {
    return self::$bundle;
  }

  /**
   * Return entity_type property.
   *
   * @return mixed
   */
  protected function getEntityType() {
    return self::$entity_type;
  }


}
