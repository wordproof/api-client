<?php

namespace WordProof\ApiClient\DTOs;

use Spatie\DataTransferObject\DataTransferObject;

class Timestamp extends DataTransferObject
{
    public string $uid;
    
    public string $hash_input;
    
    public string $date_modified;
    
    public string $title;
    
    public string $meta_title;
    
    public string $content;
    
    public string $url;
    
}