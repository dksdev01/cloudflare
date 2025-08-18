<?php

declare(strict_types=1);

namespace Drupal\Tests\cloudflarepurger\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests settings form.
 */
#[Group('cloudflarepurger')]
final class SettingFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'claro';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['cloudflarepurger'];

  /**
   * An admin user that has been setup for the test.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminUser;

  /**
   * Route providing the main configuration form of the cloudflare module.
   *
   * @var string
   */
  protected $route = 'cloudflarepurger.admin_settings_form';

  /**
   * The form URL.
   *
   * @var \Drupal\Core\Url
   */
  protected Url $formUrl;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser(['administer cloudflare']);
    $this->drupalLogin($this->adminUser);
    $this->formUrl = Url::fromRoute($this->route);
  }

  /**
   * Tests that empty list is saved as empty array.
   */
  public function testEmptyListIsSavedEmpty(): void {
    $edit = [
      'cache_tag_excludelist' => '',
    ];
    $this->drupalGet($this->formUrl);
    $this->submitForm($edit, 'Save configuration');
    $config = $this->config('cloudflarepurger.settings');
    $this->assertEmpty($config->get('cache_tag_excludelist'));
  }

  /**
   * Test that various end of line characters are handled correctly.
   */
  public function testVariousEndOfLine(): void {
    $edit = [
      'cache_tag_excludelist' => "first_pref\nsecond_pref\r \r\nthird_pref ",
    ];
    $this->drupalGet($this->formUrl);
    $this->submitForm($edit, 'Save configuration');
    $config = $this->config('cloudflarepurger.settings');
    $this->assertEquals(3, count($config->get('cache_tag_excludelist')), 'Three prefixes found');
    $this->assertEquals(['first_pref', 'second_pref', 'third_pref'], $config->get('cache_tag_excludelist'), 'Config matches');
  }

}
