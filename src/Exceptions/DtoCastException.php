<?php

namespace Intune\LaravelPaystack\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class DtoCastException extends Exception
{
   public function __construct(string $dto = '')
   {
      $this->message = "Invalid cast to " . $dto;
      $this->code = 500;
   }

   public function render()
   {
      return new JsonResponse([
         'message' => $this->getMessage(),
         'success' => false,
      ], $this->getCode());
   }
}
