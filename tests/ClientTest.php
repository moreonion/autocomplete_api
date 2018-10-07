<?php

namespace Drupal\autocomplete_field;

/**
 * Test the autocomplete API client.
 */
class ClientTest extends \DrupalUnitTestCase {

  /**
   * Test that signature verification works.
   */
  public function testVerifySignature() {
    $value = [
      '_signature' => 'rFDxNP90iDvDUdgg0Of/1Y5UVl48XyosKNdznrG+lig=',
      'key' => 'AF',
      'label' => 'Afghanistan',
    ];
    $secret_key = 'sk_test';
    $client = new Client('', '', $secret_key);
    $this->assertTrue($client->verifySignature($value));
  }

}
