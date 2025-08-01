<?php

namespace Drupal\Tests\cloudflare\Unit;

use Drupal\cloudflare\State as CloudFlareState;
use Drupal\Core\Cache\NullBackend;
use Drupal\Core\KeyValueStore\KeyValueMemoryFactory;
use Drupal\Core\Lock\NullLockBackend;
use Drupal\Core\State\State as CoreState;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests functionality of CloudFlareState object.
 *
 * @group cloudflare
 */
#[CoversClass(\Drupal\cloudflare\State::class)]
class StateTest extends UnitTestCase {

  /**
   * Data provider for testTagPurgeDailyCountIncrements.
   */
  public static function tagPurgeDailyCountIncrementsData() {
    return [
      [
        (new \DateTime('2010-02-01 00:00:00'))->getTimestamp(),
        (new \DateTime('2010-02-01 00:01:00'))->getTimestamp(),
        (new \DateTime('2010-02-01 00:02:00'))->getTimestamp(),
      ],
    ];
  }

  /**
   * Tests tag count tracking functionality.
   */
  #[DataProvider('tagPurgeDailyCountIncrementsData')]
  public function testTagPurgeDailyCountIncrements(...$timestamps) {
    $cloudflare_state = $this->getCloudflareState($timestamps);

    $cloudflare_state->incrementTagPurgeDailyCount();
    $cloudflare_state->resetTagPurgeDailyCount();
    $count = $cloudflare_state->getTagDailyCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementTagPurgeDailyCount();
    $cloudflare_state->resetTagPurgeDailyCount();
    $count = $cloudflare_state->getTagDailyCount();
    $this->assertEquals(2, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementTagPurgeDailyCount();
    $cloudflare_state->resetTagPurgeDailyCount();
    $count = $cloudflare_state->getTagDailyCount();
    $this->assertEquals(3, $count, 'Tested state with first increment of day');
  }

  /**
   * Data provider for testTagPurgeBoundaryIncrements.
   */
  public static function tagPurgeBoundaryIncrementsData() {
    return [
      [
        (new \DateTime('2010-02-01 00:00:00'))->getTimestamp(),
        (new \DateTime('2010-02-02 00:01:00'))->getTimestamp(),
        (new \DateTime('2010-02-03 00:02:00'))->getTimestamp(),
      ],
    ];
  }

  /**
   * Tests tag count boundary functionality.
   */
  #[DataProvider('tagPurgeBoundaryIncrementsData')]
  public function testTagPurgeBoundaryIncrements(...$timestamps) {
    $cloudflare_state = $this->getCloudflareState($timestamps);

    $cloudflare_state->incrementTagPurgeDailyCount();
    $cloudflare_state->resetTagPurgeDailyCount();
    $count = $cloudflare_state->getTagDailyCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementTagPurgeDailyCount();
    $cloudflare_state->resetTagPurgeDailyCount();
    $count = $cloudflare_state->getTagDailyCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementTagPurgeDailyCount();
    $cloudflare_state->resetTagPurgeDailyCount();
    $count = $cloudflare_state->getTagDailyCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');
  }

  /**
   * Data provider for testApiRateLimitCountIncrements.
   */
  public static function apiRateLimitCountIncrementsData() {
    return [
      [
        (new \DateTime('2010-02-01 00:00:00'))->getTimestamp(),
        (new \DateTime('2010-02-01 00:01:00'))->getTimestamp(),
        (new \DateTime('2010-02-01 00:02:00'))->getTimestamp(),
      ],
    ];
  }

  /**
   * Tests tag count tracking functionality.
   */
  #[DataProvider('apiRateLimitCountIncrementsData')]
  public function testApiRateLimitCountIncrements(...$timestamps) {
    $cloudflare_state = $this->getCloudflareState($timestamps);

    $cloudflare_state->incrementApiRateCount();
    $count = $cloudflare_state->getApiRateCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementApiRateCount();
    $count = $cloudflare_state->getApiRateCount();
    $this->assertEquals(2, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementApiRateCount();
    $count = $cloudflare_state->getApiRateCount();
    $this->assertEquals(3, $count, 'Tested state with first increment of day');
  }

  /**
   * Date provider for apiRateLimitBoundaryIncrementsData.
   */
  public static function apiRateLimitBoundaryIncrementsData() {
    return [
      [
        (new \DateTime('2010-02-01 00:00:00'))->getTimestamp(),
        (new \DateTime('2010-02-02 00:01:00'))->getTimestamp(),
        (new \DateTime('2010-02-03 00:02:00'))->getTimestamp(),
        (new \DateTime('2010-02-03 00:10:00'))->getTimestamp(),
        (new \DateTime('2010-02-03 00:15:00'))->getTimestamp(),
      ],
    ];
  }

  /**
   * Tests tag count boundary functionality.
   */
  #[DataProvider('apiRateLimitBoundaryIncrementsData')]
  public function testApiRateLimitBoundaryIncrements(...$timestamps) {
    $cloudflare_state = $this->getCloudflareState($timestamps);

    $cloudflare_state->incrementApiRateCount();
    $count = $cloudflare_state->getApiRateCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementApiRateCount();
    $count = $cloudflare_state->getApiRateCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementApiRateCount();
    $count = $cloudflare_state->getApiRateCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementApiRateCount();
    $count = $cloudflare_state->getApiRateCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');

    $cloudflare_state->incrementApiRateCount();
    $count = $cloudflare_state->getApiRateCount();
    $this->assertEquals(1, $count, 'Tested state with first increment of day');
  }

  /**
   * Sets up Cloudflare state for testing.
   *
   * @param array<int, int> $timestamps
   *   Set of timestamps for use in the Cloudflare state service.
   *
   * @return \Drupal\cloudflare\State
   *   Prepared state service.
   */
  public function getCloudflareState(array $timestamps): CloudFlareState {
    // Configure the time service stub.
    $time_service = $this->createMock('\Drupal\Component\Datetime\TimeInterface');
    $time_service
      ->method('getCurrentTime')
      ->willReturnOnConsecutiveCalls(...$timestamps);

    $drupal_state_service = new CoreState(new KeyValueMemoryFactory(), new NullBackend('state'), new NullLockBackend());
    $cloudflare_state = new CloudFlareState($drupal_state_service, $time_service);
    $initial_count = $cloudflare_state->getTagDailyCount();
    $this->assertEquals(0, $initial_count, 'Tested state with empty counts');
    return $cloudflare_state;
  }

}
