# WordProof API client

## Requirements
- PHP 5.6.20 | ^7.0 | ^8.0  
- cURL extension  
- JSON extension

## Install

```
composer require wordproof/api-client
```

## Usage

```injectablephp

use WordProof\ApiClient\WordProofApi;

$wordproof = new WordProofApi('SOMEAPIKEY');

$response = $wordproof->timestamp()->post([
            'foo' => 'bar',
            'another_foo' => 'another_bar',
        ]);
```
You can specify some options:
```injectablephp
use WordProof\ApiClient\WordProofApi;

$wordproof = new WordProofApi('SOMEAPIKEY');

$response = $wordproof->withOptions([
                'endpoint' => 'http://endpoint.com'
            ])
            ->timestamp()->post([
                'foo' => 'bar',
                'another_foo' => 'another_bar',
            ]);
```
You can use your own PSR Request and StreamFactory implementations:

```injectablephp
use WordProof\ApiClient\WordProofApi;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\StreamFactory;

$wordproof = new WordProofApi('SOMEAPIKEY');

$request = new Request(
        'http://endpoint.com',
        'POST',
        (new StreamFactory())->createStream(json_encode([
            'foo' => 'bar',
            'another_foo' => 'another_bar',
        ])),
        ['Content-Type' => 'application/json']
);

$response = $wordproof->sendRequest($request);
```