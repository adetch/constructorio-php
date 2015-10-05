<?php

require_once "src/ConstructorIO.php";

class ConstructorIOTest extends PHPUnit_Framework_TestCase {

  public function testEncodesParameters() {
    $constructor = new ConstructorIO("boinka", "doinka");
    $params = array("foo" => array(1,2), "bar" => array("baz" => array("a", "b")));
    $serializedParams = $constructor->serializeParams($params);
    $this->assertEquals($serializedParams,"foo%5B0%5D=1&foo%5B1%5D=2&bar%5Bbaz%5D%5B0%5D=a&bar%5Bbaz%5D%5B1%5D=b");
  }

  public function testCreatesUrlsCorrectly() {
    $constructor = new ConstructorIO("boinka", "a-test-autocomplete-key");
    $generatedUrl = $constructor->makeUrl('v1/test');
    $this->assertEquals($generatedUrl, "https://ac.cnstrc.com/v1/test?autocomplete_key=a-test-autocomplete-key");
  }

  public function testSetApiToken() {
    $apiToken = 'a-test-api-key';
    $constructor = new ConstructorIO($apiToken, "boinka");
    $this->assertEquals($apiToken, $constructor->apiToken);
  }

  public function testSetACKey() {
    $autocompleteKey = 'a-test-autocomplete-key';
    $constructor = new ConstructorIO("boinka", $autocompleteKey);
    $this->assertEquals($autocompleteKey, $constructor->autocompleteKey);
  }

  /*
   * The official fake account apiToken is YSOxV00F0Kk2R0KnPQN8
   * The official fake account acKey is ZqXaOfXuBWD4s3XzCI1q
   */
  
  public function testHealthCheck() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $resp = $constructor->healthCheck();
    $this->assertTrue($resp);
  }

  public function testVerify() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $resp = $constructor->verify();
    $this->assertTrue($resp);
  }

  public function testACQuery() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    // let's have api token and ac key plox
    $autocompletes = $constructor->query("S");
    $this->assertNotNull($autocompletes);
    $this->assertArrayHasKey("suggestions", $autocompletes);
  }

  public function testAdd() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $randitem = substr(md5(rand()), 0, 7);
    $resp = $constructor->add($randitem, "Search Suggestions");
    $this->assertTrue($resp);
  }

  public function testAddOptional() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $randitem = substr(md5(rand()), 0, 7);
    $kwargs = array(
      "suggested_score" => 1337
    );
    $resp = $constructor->add($randitem, "Search Suggestions", $kwargs);
    $this->assertTrue($resp);
  }

  public function testRemove() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    // this is live state, folks
    $randitem = substr(md5(rand()), 0, 7);
    $resp_add = $constructor->add($randitem, "Search Suggestions");
    $this->assertTrue($resp_add);
    sleep(2); // because item addition may be made async
    $resp = $constructor->remove($randitem, "Search Suggestions");
    $this->assertTrue($resp);
  }

  // no optional params for remove

  public function testModify() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $randitem = substr(md5(rand()), 0, 7);
    $resp_add = $constructor->add($randitem, "Search Suggestions");
    $this->assertTrue($resp_add);
    sleep(2); // because item addition may be made async
    $resp = $constructor->modify($randitem, $randitem, "Search Suggestions", array("suggested_score" => 100));
    $this->assertTrue($resp);
  }

  public function testModifyOptional() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $randitem = substr(md5(rand()), 0, 7);
    $resp_add = $constructor->add($randitem, "Search Suggestions");
    $this->assertTrue($resp_add);
    sleep(2); // because item addition may be made async
    $kwargs = array(
      "suggested_score" => 1337
    );
    $resp = $constructor->modify($randitem, $randitem, "Search Suggestions", $kwargs);
    $this->assertTrue($resp);
  }

  public function testConversion() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $resp = $constructor->trackConversion("Stanley_Steamer", "Search Suggestions");
    $this->assertTrue($resp);
  }

  public function testConversionOptional() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $randitem = substr(md5(rand()), 0, 7);
    $resp_add = $constructor->add($randitem, "Search Suggestions");
    $this->assertTrue($resp_add);
    sleep(2); // because item addition may be made async
    $resp = $constructor->trackSearch("Stanley_Steamer", "Search Suggestions", array("item" => $randitem));
  }
  
  public function testSearch() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $resp = $constructor->trackSearch("Stanley_Steamer", "Search Suggestions");
    $this->assertTrue($resp);
  }

  public function testSearchOptional() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $resp = $constructor->trackSearch("Stanley_Steamer", "Search Suggestions", array("num_results" => 10));
    $this->assertTrue($resp);
  }

  public function testClickThrough() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $resp = $constructor->trackClickThrough("Stanley_Steamer", "Search Suggestions");
    $this->assertTrue($resp);
  }

  public function testClickThroughOptional() {
    $constructor = new ConstructorIO("YSOxV00F0Kk2R0KnPQN8", "ZqXaOfXuBWD4s3XzCI1q");
    $randitem = substr(md5(rand()), 0, 7);
    $resp_add = $constructor->add($randitem, "Search Suggestions");
    $this->assertTrue($resp_add);
    sleep(2); // because item addition may be made async
    $kwargs = array(
      "item" => $randitem,
      "revenue" => 1337
    );
    $resp = $constructor->trackClickThrough("Stanley_Steamer", "Search Suggestions", $kwargs);
    $this->assertTrue($resp);
  }
}

?>
