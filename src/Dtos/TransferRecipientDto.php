<?php

namespace Intune\LaravelPaystack\Dtos;

use Intune\LaravelPaystack\Exceptions\DtoCastException;

/**
 * Data Transfer Object for Transfer Recipient requests from Paystack.
 *
 * This DTO encapsulates the data associated with a transfer recipient created or managed via the Paystack API.
 *
 * @author @Intuneteq
 * @version 1.0
 * @since 15-10-2024
 * @package Intune\LaravelPaystack\Dtos
 */
class TransferRecipientDto implements IDtoFactory
{
   /**
    * TransferRecipientDto constructor.
    *
    * @param string $code       The unique code associated with the transfer recipient.
    * @param string $name       The name of the transfer recipient.
    * @param string $created_at  The timestamp indicating when the transfer recipient was created.
    */
   private function __construct(
      private string $code,
      private string $name,
      private string $created_at
   ) {}

   /**
    * Get the recipient code.
    *
    * @return string The unique code of the transfer recipient.
    */
   public function getCode(): string
   {
      return $this->code;
   }

   /**
    * Get the recipient name.
    *
    * @return string The name of the transfer recipient.
    */
   public function getName(): string
   {
      return $this->name;
   }

   /**
    * Get the creation date of the recipient.
    *
    * @return string The timestamp when the recipient was created.
    */
   public function getCreatedAt(): string
   {
      return $this->created_at;
   }

   /**
    * Convert the DTO properties to an associative array.
    *
    * @return array Associative array representation of the DTO's properties.
    */
   public function toArray(): array
   {
      return [
         'recipient_code' => $this->code,
         'name' => $this->name,
         'createdAt' => $this->created_at,
      ];
   }

   /**
    * Create a new instance of TransferRecipientDto from an array of data.
    *
    * @param array $data The array containing transfer recipient data.
    *
    * @return self
    * @throws DtoCastException If required fields (recipient_code, name, createdAt) are missing.
    */
   public static function create(array $data): self
   {
      if (! isset($data['recipient_code'], $data['name'], $data['createdAt'])) {
         throw new DtoCastException(self::class);
      }

      return new self($data['recipient_code'], $data['name'], $data['createdAt']);
   }
}
