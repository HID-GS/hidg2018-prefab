<?php

namespace Drupal\Tests\salesforce_encrypt\Unit;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\State\State;
use Drupal\Tests\UnitTestCase;
use Drupal\encrypt\EncryptServiceInterface;
use Drupal\encrypt\EncryptionProfileInterface;
use Drupal\encrypt\EncryptionProfileManagerInterface;
use Drupal\salesforce_encrypt\Rest\RestClient;
use GuzzleHttp\Client;
use Drupal\Component\Datetime\TimeInterface;

/**
 * @coversDefaultClass \Drupal\salesforce_encrypt\Rest\RestClient
 * @group salesforce
 */
class RestClientTest extends UnitTestCase {

  static $modules = ['key', 'encrypt', 'salesforce', 'salesforce_encrypt'];

  /**
   *
   */
  public function setUp() {
    parent::setUp();
    $this->accessToken = 'foo';
    $this->refreshToken = 'bar';
    $this->identity = ['zee' => 'bang'];

    $this->httpClient = $this->getMock(Client::CLASS);
    $this->configFactory =
      $this->getMockBuilder(ConfigFactory::CLASS)
        ->disableOriginalConstructor()
        ->getMock();
    $this->state =
      $this->getMockBuilder(State::CLASS)
        ->disableOriginalConstructor()
        ->getMock();
    $this->cache = $this->getMock(CacheBackendInterface::CLASS);
    $this->json = $this->getMock('Drupal\Component\Serialization\Json');
    $this->encryption = $this->getMock(EncryptServiceInterface::CLASS);
    $this->profileManager = $this->getMock(EncryptionProfileManagerInterface::CLASS);
    $this->lock = $this->getMock(LockBackendInterface::CLASS);
    $this->encryptionProfile = $this->getMock(EncryptionProfileInterface::CLASS);
    $this->json = $this->getMock(Json::CLASS);
    $this->time = $this->getMock(TimeInterface::CLASS);
    $this->client = $this->getMock(RestClient::CLASS, ['_getEncryptionProfile'], [$this->httpClient, $this->configFactory, $this->state, $this->cache, $this->json, $this->time, $this->encryption, $this->profileManager, $this->lock]);
  }

  /**
   * @covers ::encrypt
   *
   * encrypt is protected, so we get at it through ::getAccessToken
   * This test covers the case where access token is NULL.
   */
  public function testEncryptNull() {
    // Test unencrypted.
    $this->state->expects($this->any())
      ->method('get')
      ->willReturn(NULL);
    $this->client->expects($this->any())
      ->method('_getEncryptionProfile')
      ->willReturn(NULL);
    $this->assertFalse($this->client->getAccessToken());
  }

  /**
   * @covers ::encrypt
   *
   * This test covers the case where access token is not NULL.
   */
  public function testEncryptNotNull() {
    // Test unencrypted.
    $this->state->expects($this->any())
      ->method('get')
      ->willReturn('not null');
    $this->client->expects($this->any())
      ->method('_getEncryptionProfile')
      ->willReturn($this->encryptionProfile);
    $this->encryption->expects($this->any())
      ->method('decrypt')
      ->willReturn($this->accessToken);
    $this->assertEquals($this->accessToken, $this->client->getAccessToken());
  }

}
