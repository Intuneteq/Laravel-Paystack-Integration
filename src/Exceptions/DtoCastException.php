<?php

namespace Intune\LaravelPaystack\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Custom exception thrown when an invalid cast to a Data Transfer Object (DTO) occurs.
 * 
 * This exception is used to indicate an error when attempting to cast data to a specific DTO 
 * and the cast fails. It returns a JSON response when rendered.
 * 
 * @author @Intuneteq
 * @package Intune\LaravelPaystack\Exceptions
 */
class DtoCastException extends Exception
{
   /**
    * DtoCastException constructor.
    * 
    * @param string $dto The name of the DTO that failed to cast.
    */
   public function __construct(string $dto)
   {
      // Set the exception message and code
      $this->message = "Invalid cast to " . $dto;
      $this->code = 500;
   }

   /**
    * Render the exception as a JSON response.
    * 
    * @return JsonResponse
    */
   public function render(): JsonResponse
   {
      return new JsonResponse([
         'message' => $this->getMessage(),
         'success' => false,
      ], $this->getCode());
   }
}
