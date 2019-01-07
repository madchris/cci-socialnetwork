<?php

namespace Drupal\cci_cpnt_socialnetwork\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class cciSocialNetworkSettingsForm extends ConfigFormBase {

  protected $config;

  protected function getEditableConfigNames() {
    return [
      'cci_cpnt_socialnetwork.settings',
    ];
  }

  public function getFormId() {
    return 'cci_socialnetwork_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $socialnetwork_settings = $this->config('cci_cpnt_socialnetwork.settings');

    $form['facebook'] = [
      '#type' => 'textfield',
      "#title" => $this->t("Nom du compte facebook"),
      '#default_value' => $socialnetwork_settings->get('facebook'),
    ];

    $form['twitter'] = [
      '#type' => 'textfield',
      "#title" => $this->t("Nom du compte twitter"),
      '#default_value' => $socialnetwork_settings->get('twitter'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $this->config('cci_cpnt_socialnetwork.settings')
      ->set('facebook', $values['facebook'])
      ->set('twitter', $values['twitter'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}