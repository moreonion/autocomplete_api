<?php

namespace Drupal\autocomplete_api;

use Drupal\little_helpers\Services\Container;
use Upal\DrupalUnitTestCase;

/**
 * Test how the element interacts with the form-API.
 */
class FormApiTest extends DrupalUnitTestCase {

  public function setUp() : void {
    parent::setUp();
    $GLOBALS['conf']['autocomplete_api_url'] = 'https://example.com/api/autocomplete/v1';
    $GLOBALS['conf']['autocomplete_api_signing_key'] = 'test-signing-key';
    $GLOBALS['conf']['campaignion_organization'] = 'impact-stack>exmple';
    Container::get()->inject('autocomplete_api.Client', $this->createMock(Client::class));
  }

  /**
   * Remove test API connection.
   */
  public function tearDown() : void {
    drupal_static_reset(Container::class);
    parent::tearDown();
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
