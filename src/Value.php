<?php

namespace Drupal\autocomplete_api;

class Value {

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

  public static function encode($value) {
    if (is_array($value)) {
      return drupal_json_encode($value);
    }
    else {
      return $value;
    }
  }

}
