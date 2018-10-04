<?php

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
    '#options' => [
      'countries' => t('Countries'),
    ],
  ];
  return $form;
}

/**
 * Implements _webform_render_[component]().
 */
function _webform_render_autocomplete($component, $value = NULL, $filter = TRUE) {
  $element = [
    '#required' => !empty($component['mandatory']) || !empty($component['required']),
    '#weight' => $component['weight'],
    '#theme_wrappers' => ['webform_element'],
    '#pre_render' => [],
    '#title' => $component['name'],
    '#title_display' => $component['extra']['title_display'],
    '#description' => $component['extra']['description'],
    '#translatable' => ['title', 'description'],
  ];

  $html_id = drupal_html_id('webform-autocomplete');
  $element += [
    '#type' => 'textfield',
    '#id' => $html_id,
  ];

  $endpoint = variable_get_value('autocomplete_field_api_endpoint');
  $global_config['endpoint'] = "$endpoint/v1/{$component['extra']['dataset']}/rows";
  $global_config['apiKey'] = variable_get_value('autocomplete_field_api_key');
  $settings['autocomplete_field'] = $global_config;
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
