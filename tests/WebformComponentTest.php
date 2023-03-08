<?php

namespace Drupal\autocomplete_api;

use Drupal\little_helpers\Services\Container;
use Upal\DrupalUnitTestCase;

/**
 * Test how the element interacts with the form-API.
 */
class WebformComponentTest extends DrupalUnitTestCase {

  public function setUp() : void {
    parent::setUp();
    module_load_include('components.inc', 'webform', 'includes/webform');
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
   * Test component edit form with defaults.
   */
  public function testEditDefaults() {
    $component['type'] = 'autocomplete';
    $component['extra']['dataset'] = 'test';
    webform_component_defaults($component);
    $form = webform_component_invoke($component['type'], 'edit', $component);
    $this->assertNotEmpty($form['display']['count']);
  }

  /**
   * Test component rendering with defaults.
   */
  public function testRenderDefaults() {
    $component['type'] = 'autocomplete';
    $component['extra']['dataset'] = 'test';
    webform_component_defaults($component);
    $element = webform_component_invoke($component['type'], 'render', $component);
    $this->assertEqual(20, $element['#count']);
  }

}

