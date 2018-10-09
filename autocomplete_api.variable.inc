<?php

/**
 * @file
 * Configuration variable definitions and documentation.
 */

/**
 * Implements hook_variable_info().
 */
function autocomplete_api_variable_info($options) {
  $variables['autocomplete_api_credentials'] = [
    'title' => t('Autocomplete API credentials'),
    'description' => t('API endpoint and API keys for the Autocomplete API service.'),
    'type' => 'properties',
    'default' => [],
    'localize' => FALSE,
  ];
  return $variables;
}
