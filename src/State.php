<?php

namespace Drupal\cloudflare;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\State\StateInterface;

/**
 * Tracks rate limits associated with CloudFlare Api.
 */
class State implements CloudFlareStateInterface {
  const TAG_PURGE_DAILY_COUNT = "cloudflare_tag_purge_daily_count";
  const TAG_PURGE_DAILY_COUNT_START = "cloudflare_tag_purge_daily_start";

  const API_RATE_COUNT = "cloudflare_api_rate_count";
  const API_RATE_COUNT_START = "cloudflare_api_rate_count_start";

  /**
   * Tracks rate limits associated with CloudFlare Api.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected StateInterface $state;

  /**
   * Time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $time;

  /**
   * State constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The drupal state service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   Time service.
   */
  public function __construct(StateInterface $state, TimeInterface $time) {
    $this->state = $state;
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public function incrementTagPurgeDailyCount(): void {
    $count = $this->state->get(self::TAG_PURGE_DAILY_COUNT, 0);
    $count++;
    $this->state->set(self::TAG_PURGE_DAILY_COUNT, $count);
  }

  /**
   * {@inheritdoc}
   */
  public function resetTagPurgeDailyCount(): void {
    $last_recorded_timestamp = $this->state->get(self::TAG_PURGE_DAILY_COUNT_START);
    if (is_null($last_recorded_timestamp)) {
      $last_recorded_timestamp = (new \DateTime('2001-01-01'))->getTimestamp();
    }

    $format = 'Y-m-d';
    $now = $this->time->getCurrentTime();
    $todays_date = DateTimePlus::createFromTimestamp($now)->format($format);
    if (!empty($last_recorded_timestamp) && is_object($last_recorded_timestamp)) {
      $last_recorded_timestamp = $last_recorded_timestamp->getTimestamp();
    }

    $last_recorded_date = DateTimePlus::createFromTimestamp($last_recorded_timestamp)->format($format);
    if (empty($last_recorded_timestamp) || ($last_recorded_date != $todays_date)) {
      $this->state->set(self::TAG_PURGE_DAILY_COUNT, 1);
      $this->state->set(self::TAG_PURGE_DAILY_COUNT_START, $now);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTagDailyCount(): int {
    return $this->state->get(self::TAG_PURGE_DAILY_COUNT, 0);
  }

  /**
   * {@inheritdoc}
   */
  public function incrementApiRateCount(): void {
    $count = $this->state->get(self::API_RATE_COUNT, 0);
    $last_recorded_timestamp = $this->state->get(self::API_RATE_COUNT_START);
    if (is_null($last_recorded_timestamp)) {
      $last_recorded_timestamp = (new \DateTime('2001-01-01'))->getTimestamp();
    }

    if (!empty($last_recorded_timestamp) && is_object($last_recorded_timestamp)) {
      $last_recorded_timestamp = $last_recorded_timestamp->getTimestamp();
    }

    $now = $this->time->getCurrentTime();
    $diff = $now - $last_recorded_timestamp;
    $minutes_passed = $diff / 60;

    if ($minutes_passed >= 5) {
      $this->state->set(self::API_RATE_COUNT, 1);
      $this->state->set(self::API_RATE_COUNT_START, $now);
    }
    else {
      $this->state->set(self::API_RATE_COUNT, ++$count);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getApiRateCount(): int {
    $count = $this->state->get(self::API_RATE_COUNT, 0);
    return $count;
  }

}
