<?php

/**
 * @file
 * Configuration variable definitions and documentation.
 */

/**
 * Implements hook_variable_info().
 */
function autocomplete_field_variable_info() {
  $variables['autocomplete_field_api_url'] = [
    'type' => 'url',
    'title' => t('Absolute URL to the autocompletion API-endpoint'),
    'description' => t('Use the base url without the version prefix.'),
    'default' => '',
  ];
  $variables['autocomplete_field_api_key'] = [
    'type' => 'string',
    'title' => t('API-key'),
    'description' => t('Note that the domain of this site needs to whitelisted for this API-key.'),
    'default' => '',
  ];
  return $variables;
}

