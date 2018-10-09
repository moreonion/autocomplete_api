<?php

namespace Drupal\autocomplete_api;

/**
 * Functionality for webform values.
 */
class Value {

  /**
   * Decode a string-encoded value.
   *
   * @param mixed $value
   *   The value to decode.
   *
   * @return string[]
   *   Associative array with two keys:
   *   - key: The selected key.
   *   - label: The selected label.
   */
  public static function decode($value) {
    if (!is_array($value)) {
      $value = drupal_json_decode($value);
      if (!is_array($value)) {
        return NULL;
      }
    }
    $value += [
      'key' => NULL,
      'label' => NULL,
    ];
    return [
      'key' => $value['key'],
      'label' => $value['label'],
    ];
  }

  /**
   * Encode a value for being used as a form-API default value.
   *
   * @param mixed $value
   *   The value to encode.
   *
   * @return string
   *   The encoded value.
   */
  public static function encode($value) {
    if (is_array($value)) {
      return drupal_json_encode($value);
    }
    else {
      return $value;
    }
  }

}
