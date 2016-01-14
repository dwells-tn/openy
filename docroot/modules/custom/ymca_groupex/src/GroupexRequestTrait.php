<?php
/**
 * @file
 * Helper trait.
 */

namespace Drupal\ymca_groupex;

trait GroupexRequestTrait {

  /**
   * Uri to make requests.
   *
   * @var string
   */
  public static $uri = 'http://api.groupexpro.com/schedule/embed';

  /**
   * Account ID.
   *
   * @var int
   */
  public static $account = 3;

  /**
   * Format of date filter.
   *
   * @var string
   */
  public static $date_filter_format = 'j n y';

  /**
   * Make a request to GroupEx.
   *
   * @param $options
   *   Request options.
   *
   * @return array
   *   Data.
   */
  private function request($options) {
    return [];
    $client = \Drupal::httpClient();
    $data = [];
    $options_defaults = [
      'query' => [
        'a' => self::$account,
      ],
    ];

    try {
      $response = $client->request('GET', self::$uri, array_merge_recursive($options_defaults, $options));
      $body = $response->getBody();
      $data = json_decode($body->getContents());
    }
    catch(\Exception $e) {
      watchdog_exception('ymca_groupex', $e);
    }

    return $data;
  }

}