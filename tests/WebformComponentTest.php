<?php

namespace Drupal\autocomplete_api;

use Upal\DrupalUnitTestCase;

/**
 * Test how the element interacts with the form-API.
 */
class WebformComponentTest extends DrupalUnitTestCase {

  public function setUp() {
    parent::setUp();
    module_load_include('components.inc', 'webform', 'includes/webform');
  }

  /**
   * Test component edit form with defaults.
   */
  public function testEditDefaults() {
    $component['type'] = 'autocomplete';
    $component['extra']['dataset'] = 'test';
    $component['client'] = $this->createMock(Client::class);
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

