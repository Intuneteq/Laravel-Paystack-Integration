<?php

namespace Intune\LaravelPaystack\Contracts;

use Intune\LaravelPaystack\Contracts\IDtoFactory;
use Intune\LaravelPaystack\Exceptions\DtoCastException;

/**
 * Data Transfer Object for initializing a transaction payload.
 * 
 * This DTO is used to encapsulate the data required for initializing
 * a Paystack transaction. It implements the Factory Design Pattern to
 * create instances from an array of data.
 * 
 * @package Intune\LaravelPaystack\Dtos
 */
class TransactionInitPayloadDto implements IDtoFactory
{
   /**
    * @var string $email Customer's email address.
    */
   private string $email;

   /**
    * @var string $amount Transaction amount in the smallest currency unit (e.g., kobo for NGN).
    */
   private string $amount;

   /**
    * @var mixed $metadata Additional transaction metadata.
    */
   private mixed $metadata;

   /**
    * Private constructor to enforce the use of the factory method.
    * 
    * @param string $email
    * @param string $amount
    * @param mixed $metadata
    */
   private function __construct(string $email, string $amount, mixed $metadata)
   {
      $this->email = $email;
      $this->amount = $amount;
      $this->metadata = $metadata;
   }

   /**
    * Get the email associated with the transaction.
    * 
    * @return string Customer's email.
    */
   public function getEmail(): string
   {
      return $this->email;
   }

   /**
    * Get the transaction amount.
    * 
    * @return string Transaction amount.
    */
   public function getAmount(): string
   {
      return $this->amount;
   }

   /**
    * Get the metadata associated with the transaction.
    * 
    * @return mixed Metadata for the transaction.
    */
   public function getMetaData(): mixed
   {
      return $this->metadata;
   }

   /**
    * Convert the DTO properties into an array format.
    * 
    * @return array Associative array of the DTO's properties.
    */
   public function toArray(): array
   {
      return [
         'email' => $this->email,
         'amount' => $this->amount,
         'metadata' => $this->metadata,
      ];
   }

   /**
    * Create an instance of the DTO from an array of data.
    * 
    * @param array $data Input data for the DTO.
    * 
    * @return self
    * 
    * @throws DtoCastException If required fields (email, amount) are missing.
    */
   public static function create(array $data): self
   {
      if (! isset($data['email'], $data['amount'])) {
         throw new DtoCastException(self::class);
      }

      if (!isset($data['metadata'])) {
         $data['metadata'] = [];
      }

      return new self($data['email'], $data['amount'], $data['metadata']);
   }
}
