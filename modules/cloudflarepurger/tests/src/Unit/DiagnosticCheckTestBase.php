<?php

namespace Drupal\Tests\cloudflarepurger\Unit;

use Drupal\cloudflare\CloudFlareStateInterface;
use Drupal\cloudflare\State as CloudFlareState;
use Drupal\Component\Datetime\Time;
use Drupal\Core\Cache\NullBackend;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\KeyValueStore\KeyValueMemoryFactory;
use Drupal\Core\Lock\NullLockBackend;
use Drupal\Core\State\State as CoreState;
use Drupal\Core\State\StateInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Tests that purge_requirements() passes on our diagnostic checks.
 */
abstract class DiagnosticCheckTestBase extends UnitTestCase {

  /**
   * The dependency injection container.
   *
   * @var \Drupal\Core\DependencyInjection\ContainerBuilder
   */
  protected $container;

  /**
   * Tracks Drupal states.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected StateInterface $drupalState;

  /**
   * Tracks rate limits associated with CloudFlare Api.
   *
   * @var \Drupal\cloudflare\CloudFlareStateInterface
   */
  protected CloudFlareStateInterface $cloudflareState;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->drupalState = new CoreState(new KeyValueMemoryFactory(), new NullBackend('state'), new NullLockBackend());
    $this->cloudflareState = new CloudFlareState($this->drupalState, new Time());

    $this->container = new ContainerBuilder();
    $this->container->set('string_translation', $this->getStringTranslationStub());

    \Drupal::setContainer($this->container);
  }

}
