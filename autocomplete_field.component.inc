<?php

use \Drupal\autocomplete_field\Client;
use \Drupal\autocomplete_field\Value;

/**
 * Implements _webform_defaults_[component]().
 */
function _webform_defaults_autocomplete() {
  return array(
    'name' => t('Select'),
    'form_key' => 'autocomplete',
    'pid' => 0,
    'weight' => 0,
    'value' => '',
    'mandatory' => 1,
    'extra' => array(
      'width' => '',
      'unique' => 0,
      'disabled' => 0,
      'title_display' => 'none',
      'description' => '',
      'attributes' => array(),
      'private' => FALSE,
    ),
  );
}

/**
 * Implements _webform_edit_[component]().
 */
function _webform_edit_autocomplete($component) {
  $form['dataset'] = [
    '#type' => 'select',
    '#title' => t('Dataset'),
    '#description' => t('Choose which kind of values you want the users to select.'),
    '#default_value' => $component['extra']['dataset'],
    '#parents' => ['extra', 'dataset'],
    '#options' => Client::fromConfig()->getDatasetOptions(),
  ];
  return $form;
}

/**
 * Implements _webform_render_[component]().
 */
function _webform_render_autocomplete($component, $value = NULL, $filter = TRUE) {
  $client = Client::fromConfig($component['extra']['dataset']);
  $element = [
    '#required' => !empty($component['mandatory']) || !empty($component['required']),
    '#weight' => $component['weight'],
    '#theme_wrappers' => ['webform_element'],
    '#pre_render' => [],
    '#title' => $component['name'],
    '#title_display' => $component['extra']['title_display'],
    '#description' => $component['extra']['description'],
    '#translatable' => ['title', 'description'],
    '#default_value' => $client->encodeValue($value),
    '#dataset_key' => $component['extra']['dataset'],
  ];

  $html_id = drupal_html_id('webform-autocomplete');
  $element += [
    '#type' => 'textfield',
    '#id' => $html_id,
    '#dataset' => $component['extra']['dataset'],
    '#element_validate' => ['_webform_validate_autocomplete'],
  ];

  $endpoint = variable_get_value('autocomplete_field_api_endpoint');
  $settings['autocomplete_field'] = $client->getJsConfig();
  $element_config = [];
  if ($component['extra']['placeholder']) {
    $element_config['placeholder'] = $component['extra']['placeholder'];
  }
  $settings['autocomplete_field']['elements'][$html_id] = $element_config;
  $element['#attached']['js'][] = ['data' => $settings, 'type' => 'setting'];

  $variant = variable_get('select2_compression_type', 'minified');
  $element['#attached']['libraries_load'][] = ['select2', $variant];

  $dir = drupal_get_path('module', 'autocomplete_field');
  $element['#attached']['js'][] = [
    'data' => $dir . '/autocomplete-field.js',
    'scope' => 'footer',
  ];
  return $element;

}

/**
 * Implements _webform_submit_COMPONENT().
 */
function _webform_submit_autocomplete($component, $value) {
  return Value::split($value);
}


/**
 * Validate the value submitted for the webform component.
 *
 * Callback for _form_validate().
 */
function _webform_validate_autocomplete($element, &$form_state, $form) {
  $client = Client::fromConfig($element['#dataset_key']);
  $value = drupal_json_decode($element['#value']);
  if (!$client->verifySignature($value)) {
    form_error($element, 'Invalid data submitted.');
  }
}

/**
 * Implements _webform_display_component().
 */
function _webform_display_autocomplete($component, $value, $format = 'html') {
  return ['#markup' => check_plain(reset($value))];
}

/**
 * Implements _webform_table_component().
 */
function _webform_table_autocomplete($component, $value) {
  return check_plain(reset($value));
}

/**
 * Implements _webform_csv_headers_component().
 */
function _webform_csv_headers_autocomplete($component, $export_options) {
  $header = [];
  $header[0] = '';
  $header[1] = '';
  $header[2] = $component['name'];
  return $header;
}

/**
 * Implements _webform_csv_data_component().
 */
function _webform_csv_data_autocomplete($component, $export_options, $value) {
  return $value;
}

/**
 * Webform conditional comparison callback for 'equal'.
 */
function _webform_conditional_comparison_autocomplete_equal(array $input_values, $rule_value, array $component) {
  foreach ($input_values as $value) {
    $value = Value::split($value);
    if (in_array($rule_value, $value)) {
      return TRUE;
    }
  }
  return FALSE;
}

/**
 * Webform conditional comparison callback for 'not_equal'.
 */
function _webform_conditional_comparison_autocomplete_not_equal(array $input_values, $rule_value, array $component) {
  return !_webform_conditional_comparison_autocomplete_equal($input_values, $rule_value, $component);
}
