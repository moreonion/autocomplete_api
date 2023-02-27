<?php

namespace Drupal\autocomplete_api;

/**
 * Test the autocomplete API client.
 */
class ClientTest extends \DrupalUnitTestCase {

  /**
   * Test that signature verification works.
   */
  public function testVerifySignature() {
    $value = [
      '_signature' => 'X8cD4vUC8f9lrKs85bhCH6KokOCfB9tng2ChCxvovMQ=',
      'key' => 'AF',
      'label' => 'Afghanistan',
    ];
    $secret_key = 'sk_test';
    $client = new Client('', '', $secret_key);
    $this->assertTrue($client->verifySignature('countries', $value));
  }

}
