<?php

namespace Drupal\autocomplete_api;

use Drupal\campaignion_auth\AuthAppClient;

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
    $auth = $this->createMock(AuthAppClient::class);
    $client = new Client('http://endpoint', $auth, 'organization', 'sk_test');
    $this->assertTrue($client->verifySignature('countries', $value));
  }

}
