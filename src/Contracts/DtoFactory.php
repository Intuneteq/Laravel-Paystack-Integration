<?php

namespace Intune\LaravelPaystack\Dtos;

/**
 * @author @Intuneteq
 *
 * @version 1.0
 *
 * @since 26-06-2024
 *
 * Implement Factory Design Pattern to create Data Transfer Objects.
 *
 * This serves as a layer for external API calls.
 */
interface IDtoFactory
{
   /**
    * Create an instance of the DTO from an array of data.
    */
   public static function create(array $data): self;

   /**
    * Get formatted properties.
    */
   public function toArray(): array;
}
