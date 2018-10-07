<?php

namespace Drupal\autocomplete_field;

use Drupal\little_helpers\Rest\Client as _Client;
use Drupal\little_helpers\Rest\HttpError;


class Client extends _Client {

  const API_VERSION = 'v1';

  protected $publicKey;
  protected $secretKey;

  public static function fromConfig() {
    $credentials = variable_get_value('autocomplete_field_credentials');
    return new static($credentials['endpoint'], $credentials['public_key'], $credentials['secret_key']);
  }

  public function __construct($endpoint, $public_key, $secret_key) {
    $this->publicKey = $public_key;
    $this->secretKey = $secret_key;
    parent::__construct($endpoint . '/' . static::API_VERSION);
  }

  /**
   * Add API-key to query parameters.
   */
  protected function send($path, array $query = [], $data = NULL, array $options = []) {
    $options['headers']['Authorization'] = $this->publicKey;
    return parent::send($path, $query, $data, $options);
  }

  public function verifySignature($value) {
    if (is_array($value) && !empty($value['_signature'])) {
      $actual_signature = strtr($value['_signature'], ['+' => '-', '/' => '_', '=' => '']);
      $signature = $this->signature($value);
      return $actual_signature === $signature;
    }
    return FALSE;
  }

  public function signature(array $value) {
    unset($value['_signature']);
    ksort($value);
    $parts = [];
    foreach ($value as $k => $v) {
      $parts[] = "$k=$v";
    }
    $serialized = implode('&', $parts);
    return drupal_hmac_base64($serialized, $this->secretKey);
  }

  public function encodeValue($value) {
    if (!$value || !is_array($value)) {
      return '';
    }
    $value['_signature'] = $this->signature[$value];
    return Value::encode($value);
  }

  public function getJsConfig($dataset_key) {
    return [
      'endpoint' => "{$this->endpoint}/$dataset_key/rows",
      'apiKey' => $this->publicKey,
    ];
  }

}
