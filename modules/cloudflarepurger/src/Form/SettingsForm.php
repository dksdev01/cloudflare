<?php

namespace Drupal\cloudflarepurger\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for the Cloudflare Purger settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'cloudflarepurger.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cloudflarepurger_admin';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('cloudflarepurger.settings');
    $excludelist = $config->get('cache_tag_excludelist');
    $excludelist = is_array($excludelist) ? implode(PHP_EOL, $excludelist) : '';
    $form['cloudflare_config']['cache_tag_excludelist'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Cache tag exclude-list'),
      '#default_value' => $excludelist,
      '#description' => $this->t('List of tag prefixes to exclude from the "Cache-Tag" header. One per line.'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $formState) {
    $config = $this->configFactory()->getEditable('cloudflarepurger.settings');
    $config->set('cache_tag_excludelist', explode(PHP_EOL, $formState->getValue('cache_tag_excludelist')));
    $config->save();
  }

}
