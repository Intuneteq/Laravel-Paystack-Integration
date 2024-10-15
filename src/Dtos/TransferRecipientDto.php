<?php

namespace Intune\LaravelPaystack\Dtos;

use Intune\LaravelPaystack\Exceptions\DtoCastException;

/**
 * @author @Intuneteq
 *
 * @version 1.0
 *
 * @since 30-06-2024
 *
 * Data Transfer Object for Transfer Recipient requests from paystack
 */
class TransferRecipientDto implements IDtoFactory
{
   /**
    * TransferRecipientDto constructor.
    *
    * @param  string  $code  The recipient code.
    * @param  string  $name  The recipient name.
    * @param  string  $created_at  The creation date.
    */
   public function __construct(
      private string $code,
      private string $name,
      private string $created_at
   ) {}

   /**
    * Get the recipient code.
    */
   public function getCode(): string
   {
      return $this->code;
   }

   /**
    * Get the recipient name.
    */
   public function getName(): string
   {
      return $this->name;
   }

   /**
    * Get the creation date.
    */
   public function getCreatedAt(): string
   {
      return $this->created_at;
   }

   /**
    * Convert the DTO to an array.
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
    * Create a new instance of TransferRecipientDto from array data.
    *
    * @param  array  $data  The array containing transfer recipient data.
    *
    * @throws ServerErrorException If required fields are missing in $data.
    */
   public static function create(array $data): self
   {
      if (! isset($data['recipient_code'], $data['name'], $data['createdAt'])) {
         throw new DtoCastException(self::class);
      }

      return new self($data['recipient_code'], $data['name'], $data['createdAt']);
   }
}
