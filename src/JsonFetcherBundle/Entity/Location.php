<?php

namespace JsonFetcherBundle\Entity;

class Location
{
    private $name;
    private $lat;
    private $long;
    
    public function __construct(string $name, float $lat, float $long)
    {
        $this->name = $name;
        $this->lat = $lat;
        $this->long = $long;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLong(): float
    {
        return $this->long;
    }
}
