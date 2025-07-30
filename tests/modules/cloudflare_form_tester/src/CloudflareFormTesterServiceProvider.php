<?php

namespace Drupal\cloudflare_form_tester;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies the language manager service.
 */
class CloudflareFormTesterServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Overrides language_manager class to test domain language negotiation.
    $definition = $container->getDefinition('cloudflare.zone');
    $definition->setClass('Drupal\cloudflare_form_tester\Mocks\ZoneMock');
  }

}
