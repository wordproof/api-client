<?php

namespace WordProof\ApiClient\Factories;

use Laminas\Diactoros\Request;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Client\ClientInterface;
use WordProof\ApiClient\DTOs\Timestamp;
use WordProof\ApiClient\Exceptions\ApiException;

class TimestampFactory extends HttpRequestFactory
{
    private ClientInterface $client;
    
    private StreamFactory $stream;
    
    public function __construct(ClientInterface $client, array $options)
    {
        $this->client = $client;
        
        $this->stream = new StreamFactory();
        
        $this->options = $options;
    }
    
    public function get(int $timestampId)
    {
        $response = $this->client->sendRequest(
            new Request(
                $this->options['endpoint'] . "/timestamps/$timestampId",
                "GET",
                $this->stream->createStream(),
                $this->headers()
            )
        );
        
        return new Timestamp(json_decode($response->getBody()->getContents(), true));
    }
    
    public function post(array $timestampData)
    {
        $request = new Request(
            $this->options['endpoint'] . "/timestamps/",
            "POST",
            $this->stream->createStream(json_encode($timestampData)),
            $this->headers()
        );
        
        $response = $this->client->sendRequest($request);
        
        $body = $response->getBody()->getContents();
        
        ray()->clearScreen();
        ray($request);
        ray($response);
        ray($body);
        
        if ($body === "" || !json_decode($body, true)) {
            throw new ApiException("Incorrect WordProof response");
        }
        
        return new Timestamp(json_decode($body, true));
    }
    
    public function bulk(array $timestampsData)
    {
        return $this->post($timestampsData);
    }
}