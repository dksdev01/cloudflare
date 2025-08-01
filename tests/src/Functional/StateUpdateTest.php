<?php

namespace Drupal\Tests\cloudflare\Functional;

use Drupal\cloudflare\State;
use Drupal\FunctionalTests\Update\UpdatePathTestBase;

/**
 * Tests the cloudflare_update_10001 update hook.
 *
 * @group Update
 */

/**
 * Tests that date objects are converted to timestamps as expected.
 *
 * @coversFunction cloudflare_update_10001
 */
class StateUpdateTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setDatabaseDumpFiles(): void {
    $this->databaseDumpFiles = [
      __DIR__ . '/../../fixtures/update/db-before-cloudflare_update_10001.php.gz',
    ];
  }

  /**
   * Test that related state key values was updated as expected.
   */
  public function testStateUpdate(): void {
    $state = $this->container->get('state');

    foreach ([State::TAG_PURGE_DAILY_COUNT_START, State::API_RATE_COUNT_START] as $item) {
      $this->assertInstanceOf(\DateTime::class, $state->get($item));
    }

    $this->runUpdates();

    foreach ([State::TAG_PURGE_DAILY_COUNT_START, State::API_RATE_COUNT_START] as $item) {
      $this->assertIsInt($state->get($item));
    }
  }

}
