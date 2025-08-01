<?php

namespace Drupal\cloudflare;

/**
 * Tracks rate limits associated with CloudFlare Api.
 */
interface CloudFlareStateInterface {

  /**
   * Get the count of purges done in the past 5 minutes.
   *
   * @return int
   *   Count of purges done in the past 5 minutes
   */
  public function getApiRateCount(): int;

  /**
   * Get the count of tag purges done today.
   *
   * @return int
   *   Count of tag purges done today.
   */
  public function getTagDailyCount(): int;

  /**
   * Increment the count of api calls done in the past 5 minutes.
   *
   * @see https://api.cloudflare.com/#requests
   */
  public function incrementApiRateCount(): void;

  /**
   * Increment the count of tag purges done today.
   *
   * @see https://support.cloudflare.com/hc/en-us/articles/206596608-How-to-Purge-Cache-Using-Cache-Tags
   */
  public function incrementTagPurgeDailyCount(): void;

  /**
   * Reset the daily count if it is a new day.
   */
  public function resetTagPurgeDailyCount(): void;

}
