<?php

namespace Drupal\autocomplete_api;

/**
 * Test how the element interacts with the form-API.
 */
class FormApiTest extends \DrupalUnitTestCase {

  /**
   * Set global variables for the API config.
   */
  public function setUp() : void {
    parent::setUp();
    $GLOBALS['conf']['autocomplete_api_credentials'] = [
      'endpoint' => 'https://autocomplete.example.com/v1',
      'secret_key' => 'sk',
      'public_key' => 'pk',
    ];
  }

  /**
   * Test default element.
   */
  public function testDefaults() {
    $form['autocomplete'] = [
      '#type' => 'autocomplete_api_select',
    ];
    $form_state = form_state_defaults();
    drupal_prepare_form('autocomplete_test', $form, $form_state);
    drupal_process_form('autocomplete_test', $form, $form_state);
    $this->assertEqual('autocomplete_api_select', $form['autocomplete']['#theme']);
    $this->assertArrayNotHasKey('#attached', $form['autocomplete']);
  }

  /**
   * Test element with dummy dataset.
   */
  public function testDummyDataset() {
    $form['autocomplete'] = [
      '#type' => 'autocomplete_api_select',
      '#dataset_key' => 'dummy',
    ];
    $form_state = form_state_defaults();
    drupal_prepare_form('autocomplete_test', $form, $form_state);
    drupal_process_form('autocomplete_test', $form, $form_state);
    $this->assertEqual('autocomplete_api_select', $form['autocomplete']['#theme']);
    $this->assertNotEmpty($form['autocomplete']['#attached']);
  }

}
