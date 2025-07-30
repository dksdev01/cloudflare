<?php

namespace Drupal\Tests\cloudflarepurger\Functional;

use Drupal\FunctionalTests\Update\UpdatePathTestBase;

/**
 * Tests the cloudflarepurger_update_10001 update hook.
 *
 * @group Update
 * @covers cloudflarepurger_update_10001
 */
class CacheTagExcludelistRenameTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setDatabaseDumpFiles(): void {
    $this->databaseDumpFiles = [
      __DIR__ . '/../../fixtures/update/db-2.0.0-beta1.php.gz',
    ];
  }

  /**
   * Test that configuration key is renamed preserving existing configuration.
   */
  public function testCacheTagExcludelistRename(): void {
    $expected = ['one', 'two', 'four'];

    $settings = \Drupal::configFactory()->get('cloudflarepurger.settings');
    // cspell:disable-next-line
    $this->assertSame($expected, $settings->get('edge_cache_tag_header_blacklist'));

    $this->runUpdates();

    $settings = \Drupal::configFactory()->get('cloudflarepurger.settings');
    $this->assertSame($expected, $settings->get('cache_tag_excludelist'));
  }

}
