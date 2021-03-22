<?php


namespace WordProof\ApiClient\DTOs;


class DataTransferObject
{
    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }
}