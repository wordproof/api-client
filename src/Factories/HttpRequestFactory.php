<?php


namespace WordProof\ApiClient\Factories;


class HttpRequestFactory
{
    /**
     * Common options
     * @var array
     */
    protected array $options;
    
    /**
     * Generate an array of common headers
     * @return string[]
     */
    public function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->options['token'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}