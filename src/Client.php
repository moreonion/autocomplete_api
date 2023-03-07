<?php

namespace Drupal\autocomplete_api;

use Drupal\little_helpers\Rest\Client as _Client;

/**
 * API-client for the Autocomplete REST API.
 */
class Client extends _Client {

  const API_VERSION = 'v1';

  protected $authClient;
  protected $organization;
  protected $signingKey;

  /**
   * Create a new client instance.
   *
   * @param string $endpoint
   *   The base URL for the autocompletion service (without version prefix).
   * @param string $public_key
   *   The public API-key (usually starting with `pk…`).
   * @param string $secret_key
   *   The secret API-key (usually starting with `sk…`).
   */
  public function __construct($endpoint, $auth_client, $organization, $signing_key) {
    $this->authClient = $auth_client;
    $this->organization = $organization;
    $this->signingKey = $signing_key;
    parent::__construct($endpoint);
  }

  /**
   * Add version prefix and authorization headers.
   */
  protected function send($path, array $query = [], $data = NULL, array $options = []) {
    if ($path && $path[0] != '/') {
      $path = '/' . $path;
    }
    $path = '/' . static::API_VERSION . $path;
    $options['headers']['Authorization'] = 'Bearer ' . $this->authClient->getToken();
    return parent::send($path, $query, $data, $options);
  }

  /**
   * Verify signature on values.
   *
   * @param string $dataset_key
   *   The path-name of the dataset which the value belongs to.
   * @param mixed $value
   *   JSON-parsed value.
   *
   * @return bool
   *   TRUE if the value contains a signature and the signature was verified.
   */
  public function verifySignature(string $dataset_key, $value) {
    if (is_array($value) && !empty($value['_signature'])) {
      $replace = ['+' => '-', '/' => '_', '=' => ''];
      $actual_signature = strtr($value['_signature'], $replace);
      $signature = $this->signature($dataset_key, $value);
      return $actual_signature === $signature;
    }
    return FALSE;
  }

  /**
   * Generate a signature for a value array.
   *
   * @param string $dataset_key
   *   The path-name of the dataset which the value belongs to.
   * @param string[] $value
   *   The value array to sign.
   *
   * @return string
   *   A HMAC signature for the values.
   */
  public function signature(string $dataset_key, array $value) {
    unset($value['_signature']);
    ksort($value);
    $parts = [];
    foreach ($value as $k => $v) {
      $parts[] = "$k=$v";
    }
    $serialized = $dataset_key . ':' . implode('&', $parts);
    return drupal_hmac_base64($serialized, $this->signingKey);
  }

  /**
   * Sign and serialize value for use as #default_value.
   *
   * @param string $dataset_key
   *   The path-name of the dataset which the value belongs to.
   * @param mixed $value
   *   The value to serialize.
   *
   * @return string
   *   Signed and JSON-encoded value.
   */
  public function encodeValue($dataset_key, $value) {
    if (!$value || !is_array($value)) {
      return '';
    }
    $value['_signature'] = $this->signature($dataset_key, $value);
    return Value::encode($value);
  }

  /**
   * Generate the global JS config.
   *
   * @param string $dataset_key
   *   The path-name of the dataset which the value belongs to.
   *
   * @return string[]
   *   JS settings.
   */
  public function getJsConfig($dataset_key) {
    $version = static::API_VERSION;
    return [
      'endpoint' => "{$this->endpoint}/$version/{$dataset_key}/rows",
      'token' => $this->authClient->getUserToken([]),
    ];
  }

  /**
   * Get a list of all datasets and return them as #options-array.
   *
   * @return string[]
   *   Options-array of all available datasets.
   */
  public function getDatasetOptions() {
    $options = [];
    foreach ($this->send('', ['organization' => $this->organization])['datasets'] as $ds) {
      $options[$ds['key']] = $ds['title'];
    }
    return $options;
  }

}
