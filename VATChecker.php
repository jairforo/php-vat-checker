<?php

$request = new HttpRequest();
$request->setUrl('http://ec.europa.eu/taxation_customs/vies/services/checkVatService');
$request->setMethod(HTTP_METH_POST);

$request->setHeaders(array(
  'cache-control' => 'no-cache',
  'Connection' => 'keep-alive',
  'Content-Length' => '544',
  'Accept-Encoding' => 'gzip, deflate',
  'Host' => 'ec.europa.eu',
  'Postman-Token' => '617c609d-4695-4572-9cae-845731f3bf19,5c00b047-a205-4e9a-93e2-31fc69cb4a4c',
  'Cache-Control' => 'no-cache',
  'Accept' => '*/*',
  'User-Agent' => 'PostmanRuntime/7.20.1',
  'Content-Type' => 'application/xml'
));

$request->setBody('<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
  xmlns:tns1="urn:ec.europa.eu:taxud:vies:services:checkVat:types"
  xmlns:impl="urn:ec.europa.eu:taxud:vies:services:checkVat">
  <soap:Header>
  </soap:Header>
  <soap:Body>
    <tns1:checkVat xmlns:tns1="urn:ec.europa.eu:taxud:vies:services:checkVat:types"
     xmlns="urn:ec.europa.eu:taxud:vies:services:checkVat:types">
     <tns1:countryCode>NL</tns1:countryCode>
     <tns1:vatNumber>854502130B01</tns1:vatNumber>
    </tns1:checkVat>
  </soap:Body>
</soap:Envelope>');

try {
  $response = $request->send();

  echo $response->getBody();
} catch (HttpException $ex) {
  echo $ex;
}
