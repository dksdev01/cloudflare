<?php

namespace Drupal\cloudflare;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Zone methods for CloudFlare.
 */
interface CloudFlareZoneInterface {

  /**
   * Instantiates new instance of the class.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   * @param \Drupal\cloudflare\CloudFlareStateInterface $state
   *   Tracks rate limits associated with Cloudflare API.
   *
   * @return CloudFlareZoneInterface
   *   ZoneApi instance for accessing Cloudflare zone API.
   */
  public static function create(ConfigFactoryInterface $config_factory, LoggerInterface $logger, CacheBackendInterface $cache, CloudFlareStateInterface $state);

  /**
   * Zone constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   * @param \Drupal\cloudflare\CloudFlareStateInterface $state
   *   Tracks rate limits associated with Cloudflare API.
   * @param \Cloudflare\API\Endpoints\Zones|null $zone_api
   *   ZoneApi instance for accessing api.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LoggerInterface $logger, CacheBackendInterface $cache, CloudFlareStateInterface $state, $zone_api);

  /**
   * Retrieves a listing of zones in the current CloudFlare account.
   *
   * @return array
   *   A array of CloudFlareZones objects from the current CloudFlare account.
   *
   * @throws \GuzzleHttp\Exception\RequestException
   *   Application level error returned from the API.
   */
  public function listZones();

  /**
   * Asserts that credentials are valid. Does NOT pull settings from CMI.
   *
   * @param string $api_token
   *   The secret Api token used to authenticate against CloudFlare.
   * @param \Drupal\cloudflare\CloudFlareStateInterface $state
   *   Tracks rate limits associated with CloudFlare Api.
   * @param string $zone_name
   *   Zone name to limit the results for.
   */
  public static function assertValidToken(string $api_token, CloudFlareStateInterface $state, string $zone_name = '');

  /**
   * Asserts that credentials are valid. Does NOT pull settings from CMI.
   *
   * @param string $apikey
   *   The secret Api key used to authenticate against CloudFlare.
   * @param string $email
   *   Email of the account used to authenticate against CloudFlare.
   * @param \Drupal\cloudflare\CloudFlareStateInterface $state
   *   Tracks rate limits associated with CloudFlare Api.
   *
   * @throws \GuzzleHttp\Exception\ClientException
   *   Thrown if $apikey and $email fail to authenticate against the Api.
   * @throws \GuzzleHttp\Exception\RequestException
   *   Thrown if an unknown exception occurs when connecting to the Api.
   */
  public static function assertValidCredentials($apikey, $email, CloudFlareStateInterface $state);

}
