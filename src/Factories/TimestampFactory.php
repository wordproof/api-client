<?php

namespace WordProof\ApiClient\Factories;

use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use WordProof\ApiClient\DTOs\Timestamp;
use WordProof\ApiClient\Exceptions\ApiException;

class TimestampFactory extends HttpRequestFactory
{
    private ClientInterface $client;
    
    
    public function __construct(ClientInterface $client, array $options)
    {
        $this->client = $client;
        
        $this->options = $options;
    }
    
    public function get(int $timestampId)
    {
        $response = $this->client->sendRequest(
            new Request(
                "GET",
                $this->options['endpoint'] . "/timestamps/$timestampId",
                [],
                $this->headers()
            )
        );
    
        $body = json_decode($response->getBody()->getContents(), true);
        
        return new Timestamp($body);
    }
    
    public function post(array $timestampData)
    {
        $request = new Request(
            "POST",
            $this->options['endpoint'] . "/timestamps",
            $timestampData,
            $this->headers()
        );
        
        $response = $this->client->sendRequest($request);
        
        $body = $response->getBody()->getContents();
        
        if ($body === "" || !json_decode($body, true)) {
            throw new ApiException("Incorrect WordProof response");
        }
        
        return $response;
    }
    
    public function bulk(array $timestampsData)
    {
        return $this->post($timestampsData);
    }
}