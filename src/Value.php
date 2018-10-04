<?php

namespace Drupal\autocomplete_field;

class Value {

  public static function split($value) {
    if (is_array($value)) {
      return $value;
    }
    else {
      $parts = explode('|', $value, 3);
      return [
        'key' => $parts[1],
        'unique_key' => $parts[0],
        'label' => $parts[2],
      ];
    }
  }

  public static function encode($value) {
    if (is_array($value)) {
      return "{$value['unique_key']}|{$value['key']}|{$value['label']}";
    }
    else {
      return $value;
    }
  }

}
