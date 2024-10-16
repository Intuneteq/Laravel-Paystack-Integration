<?php

namespace Intune\LaravelPaystack\Contracts;

/**
 * Interface for a Data Transfer Object (DTO) Factory.
 * 
 * This interface follows the Factory Design Pattern to create DTO instances
 * from raw data, typically received from external API calls to paystack. The DTO provides
 * a structured and formatted representation of the data for internal usage.
 * 
 * @package Intune\LaravelPaystack\Dtos
 */
interface IDtoFactory
{
    /**
     * Create a new DTO instance from the provided data.
     * 
     * This method should parse and map the given array of data into a DTO object.
     * 
     * @param array $data The raw data to be converted into a DTO.
     * 
     * @return self
     */
    public static function create(array $data): self;

    /**
     * Convert the DTO properties into an associative array.
     * 
     * This method formats the DTO's properties into an array, typically for 
     * further processing or for returning standardized data.
     * 
     * @return array An associative array of the DTO's properties.
     */
    public function toArray(): array;
}
