<?php

namespace Drupal\cloudflare;

use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Endpoints\Zones;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;

/**
 * Zone methods for CloudFlare.
 */
class Zone implements CloudFlareZoneInterface {
  use StringTranslationTrait;

  /**
   * The settings configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Tracks rate limits associated with CloudFlare Api.
   *
   * @var \Drupal\cloudflare\CloudFlareStateInterface
   */
  protected $state;

  /**
   * ZoneApi object for interfacing with CloudFlare Php Sdk.
   *
   * @var \Cloudflare\API\Endpoints\Zones
   */
  protected $zoneApi;

  /**
   * The current cloudflare ZoneId.
   *
   * @var string
   */
  protected $zone;

  /**
   * The zone name to filter for.
   *
   * @var string
   */
  protected $zoneName;

  /**
   * Flag for valid credentials.
   *
   * @var bool
   */
  protected $validCredentials;

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * {@inheritdoc}
   */
  public static function create(ConfigFactoryInterface $config_factory, LoggerInterface $logger, CacheBackendInterface $cache, CloudFlareStateInterface $state) {
    $config = $config_factory->get('cloudflare.settings');
    $auth_using = $config->get('auth_using');
    if ($auth_using === 'key') {
      $api_key = $config->get('apikey');
      $email = $config->get('email');
    }
    elseif ($auth_using === 'token') {
      $token = $api_key = $config->get('api_token');
    }

    if ($auth_using === 'key') {
      $key = new APIKey($email, $api_key);
    }
    elseif ($auth_using === 'token') {
      $key = new APIToken($token);
    }

    $adapter = new Guzzle($key);
    $zoneapi = new Zones($adapter);

    return new static(
      $config_factory,
      $logger,
      $cache,
      $state,
      $zoneapi
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, LoggerInterface $logger, CacheBackendInterface $cache, CloudFlareStateInterface $state, $zone_api) {
    $this->config = $config_factory->get('cloudflare.settings');
    $this->logger = $logger;
    $this->cache = $cache;
    $this->state = $state;
    $this->zoneApi = $zone_api;
    $this->zone = $this->config->get('zone');
    $this->zoneName = $this->config->get('zone_name');
    $this->validCredentials = $this->config->get('valid_credentials');
  }

  /**
   * {@inheritdoc}
   */
  public function listZones() {
    $zones = [];
    $cid = 'cloudflare_zone_listing';
    try {

      if ($cached = $this->cache->get($cid)) {
        return $cached->data;
      }

      else {
        $page = 0;
        $results = $this->zoneApi->listZones($this->zoneName, '', $page);
        while (count($zones) < $results->result_info->total_count) {
          $page++;
          $results = $this->zoneApi->listZones($this->zoneName, '', $page);
          $zones = array_merge($zones, $results->result);
        }

        $this->cache->set($cid, $zones, time() + 60 * 5, ['cloudflare_zone']);
      }
    }
    catch (ClientException $e) {
      $this->logger->error($e->getMessage());
      throw $e;
    }
    return $zones;
  }

  /**
   * {@inheritdoc}
   */
  public static function assertValidToken(string $api_token, CloudFlareStateInterface $state, string $zone_name = '') {
    $key = new APIToken($api_token);
    $adapter = new Guzzle($key);
    $zone_api_direct = new Zones($adapter);

    try {
      $zone_api_direct->listZones($zone_name);
    }
    finally {
      $state->incrementApiRateCount();
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function assertValidCredentials($apikey, $email, CloudFlareStateInterface $state) {
    $key = new APIKey($email, $apikey);
    $adapter = new Guzzle($key);
    $zone_api_direct = new Zones($adapter);

    try {
      $zone_api_direct->listZones();
    }
    finally {
      $state->incrementApiRateCount();
    }
  }

}
