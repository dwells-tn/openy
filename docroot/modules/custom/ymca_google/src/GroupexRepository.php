<?php

namespace Drupal\ymca_google;

use Drupal\ymca_groupex\GroupexRequestTrait;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\ymca_mappings\Entity\Mapping;

/**
 * Class GroupexRepository.
 *
 * @package Drupal\ymca_google
 */
class GroupexRepository implements GroupexRepositoryInterface {

  /**
   * Timezone string.
   *
   * @var string
   */
  protected $timezoneString = 'UTC';

  /**
   * Timezone object.
   *
   * @var \DateTimeZone
   */
  protected $timezone;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->timezone = new \DateTimeZone($this->timezoneString);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $data, $start, $end) {
    foreach ($data as $item) {
      // Parse time to create timestamps.
      preg_match("/(.*)-(.*)/i", $item->time, $output);
      $item->timestamp_start = $this->extractTimestamp($item->date, $output[1]);
      $item->timestamp_end = $this->extractTimestamp($item->date, $output[2]);

      // Create entity.
      $mapping = Mapping::create([
        'type' => 'groupex',
        'field_groupex_category' => $item->category,
        'field_groupex_class_id' => $item->id,
        'field_groupex_date' => [$item->date],
        'field_groupex_description' => $item->desc,
        'field_groupex_instructor' => $item->instructor,
        'field_groupex_location' => $item->location,
        'field_groupex_orig_instructor' => $item->original_instructor,
        'field_groupex_studio' => $item->studio,
        'field_groupex_sub_instructor' => $item->sub_instructor,
        'field_groupex_time' => $item->time,
        'field_timestamp_end' => $item->timestamp_end,
        'field_timestamp_start' => $item->timestamp_start,
      ]);
      $mapping->setName($item->location . ' [' . $item->id . ']');
      $mapping->save();
    }
  }

  /**
   * Extract timestamp from date and time strings.
   *
   * @param string $date
   *   Date string. Example: Tuesday, May 31, 2016.
   * @param string $time
   *   Time string. Example: 5:05am.
   *
   * @return int
   *   Timestamp.
   */
  private function extractTimestamp($date, $time) {
    $dateTime = DrupalDateTime::createFromFormat(GroupexRequestTrait::$dateFullFormat, $date, $this->timezone);
    $start_datetime = new \DateTime($time);

    $dateTime->setTime(
      $start_datetime->format('H'),
      $start_datetime->format('i'),
      $start_datetime->format('s')
    );

    return $dateTime->getTimestamp();
  }

}
