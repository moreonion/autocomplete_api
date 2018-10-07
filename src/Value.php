<?php

namespace Drupal\autocomplete_field;

class Value {

  public static function split($value) {
    if (is_array($value)) {
      return $value;
    }
    else {
      $data = drupal_json_decode($value);
      return [
        'key' => $data['key'],
        'label' => $data['label'],
      ];
    }
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
