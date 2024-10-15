<?php

namespace Intune\LaravelPaystack\Dtos;

use Intune\LaravelPaystack\Exceptions\DtoCastException;

class TransactionInitPayloadDto implements IDtoFactory
{
   public function __construct(
      private string $email,
      private ?string $amount,
      private mixed $metadata,
   ) {}

   public function getEmail()
   {
      return $this->email;
   }

   public function getAmount()
   {
      return $this->amount;
   }

   public function getMetaData()
   {
      return $this->metadata;
   }

   public function toArray(): array
   {
      return [
         'email' => $this->email,
         'amount' => $this->amount,
         'metadata' => $this->metadata
      ];
   }

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