<?php

/**
 * @file
 * Define webform component callbacks.
 */

use Drupal\autocomplete_api\Client;
use Drupal\autocomplete_api\Value;
use Drupal\little_helpers\ArrayConfig;

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
      'placeholder' => '',
    ),
  );
}

/**
 * Implements _webform_edit_[component]().
 */
function _webform_edit_autocomplete($component) {
  ArrayConfig::mergeDefaults($component, _webform_defaults_autocomplete());
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
  ArrayConfig::mergeDefaults($component, _webform_defaults_autocomplete());
  $element = [
    '#type' => 'autocomplete_api_select',
    '#required' => !empty($component['mandatory']) || !empty($component['required']),
    '#weight' => $component['weight'],
    '#theme_wrappers' => ['webform_element'],
    '#pre_render' => [],
    '#title' => $component['name'],
    '#title_display' => $component['extra']['title_display'],
    '#description' => $component['extra']['description'],
    '#translatable' => ['title', 'description'],
    '#default_value' => $value,
    '#dataset_key' => $component['extra']['dataset'],
  ];
  if ($component['extra']['placeholder']) {
    $element['#placeholder'] = $component['extra']['placeholder'];
  }
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
  $header[0] = ['', ''];
  $header[1] = [$component['name'], ''];
  $header[2] = ['key', 'label'];
  return $header;
}

/**
 * Implements _webform_csv_data_component().
 */
function _webform_csv_data_autocomplete($component, $export_options, $value) {
  return [$value['key'], $value['label']];
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
