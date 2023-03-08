<?php

/**
 * @file
 * Configuration variable definitions and documentation.
 */

/**
 * Implements hook_variable_info().
 */
function autocomplete_api_variable_info($options) {
  $variables['autocomplete_api_url'] = [
    'title' => t('Autocomplete API URL'),
    'description' => t('API endpoint for the autocomplete service.'),
    'type' => 'url',
    'default' => '',
    'localize' => FALSE,
  ];
  $variables['autocomplete_api_signing_key'] = [
    'title' => t('Signing key'),
    'description' => t('Secret key used for signing and authorizing submitted values.'),
    'type' => 'string',
    'default' => '',
    'localize' => FALSE,
  ];

  return $variables;
}
