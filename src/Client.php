<?php

namespace Drupal\autocomplete_field;

use Drupal\little_helpers\Rest\Client as _Client;
use Drupal\little_helpers\Rest\HttpError;


class Client extends _Client {

  const API_VERSION = 'v1';

  protected $publicKey;
  protected $secretKey;

  /**
   * Create a new instance based on global configuration.
   *
   * @param string $dataset_key
   *   The path-name of the currently selected dataset.
   */
  public static function fromConfig($dataset_key = '') {
    $credentials = variable_get_value('autocomplete_field_credentials');
    return new static($credentials['endpoint'], $credentials['public_key'], $credentials['secret_key'], $dataset_key);
  }

  /**
   * Create a new client instance.
   *
   * @param string $endpoint
   *   The base URL for the autocompletion service (without version prefix).
   * @param string $public_key
   *   The public API-key (usually starting with `pk…`).
   * @param string $secret_key
   *   The secret API-key (usually starting with `sk…`).
   * @param string $dataset_key
   *   The path-name of the currently selected dataset.
   */
  public function __construct($endpoint, $public_key, $secret_key, $dataset_key = '') {
    $this->publicKey = $public_key;
    $this->secretKey = $secret_key;
    $this->datasetKey = $dataset_key;
    parent::__construct($endpoint . '/' . static::API_VERSION);
  }

  /**
   * Add API-key to query parameters.
   */
  protected function send($path, array $query = [], $data = NULL, array $options = []) {
    $options['headers']['Authorization'] = $this->publicKey;
    return parent::send($path, $query, $data, $options);
  }

  /**
   * Verify signature on values.
   *
   * @param mixed $value
   *   JSON-parsed value.
   *
   * @return bool
   *   TRUE if the value contains a signature and the signature was verified.
   */
  public function verifySignature($value) {
    if (is_array($value) && !empty($value['_signature'])) {
      $actual_signature = strtr($value['_signature'], ['+' => '-', '/' => '_', '=' => '']);
      $signature = $this->signature($value);
      return $actual_signature === $signature;
    }
    return FALSE;
  }

  /**
   * Generate a signature for a value array.
   *
   * @param string[] $value
   *   The value array to sign.
   *
   * @return string
   *   A HMAC signature for the values.
   */
  public function signature(array $value) {
    unset($value['_signature']);
    ksort($value);
    $parts = [];
    foreach ($value as $k => $v) {
      $parts[] = "$k=$v";
    }
    $serialized = $this->datasetKey . ':' . implode('&', $parts);
    return drupal_hmac_base64($serialized, $this->secretKey);
  }

  /**
   * Sign and serialize value for use as #default_value.
   *
   * @param string[] $value
   *   The value to serialize.
   *
   * @param string
   *   Signed and JSON-encoded value.
   */
  public function encodeValue($value) {
    if (!$value || !is_array($value)) {
      return '';
    }
    $value['_signature'] = $this->signature($value);
    return Value::encode($value);
  }

  /**
   * Generate the global JS config.
   *
   * @return string[]
   *   JS settings.
   */
  public function getJsConfig() {
    return [
      'endpoint' => "{$this->endpoint}/{$this->datasetKey}/rows",
      'apiKey' => $this->publicKey,
    ];
  }

}
