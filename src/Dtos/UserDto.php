<?php

namespace Intune\LaravelPaystack\Dtos;

use Intune\LaravelPaystack\Dtos\IDtoFactory;
use Intune\LaravelPaystack\Exceptions\DtoCastException;

class UserDto implements IDtoFactory
{
   public function __construct(
      private string $email,
      private string $first_name,
      private string $last_name,
      private ?string $phone,
   ) {}

   /**
    * Get the email.
    */
   public function getEmail(): string
   {
      return $this->email;
   }

   /**
    * Get the first name.
    */
   public function getFirstName(): string
   {
      return $this->first_name;
   }

   /**
    * Get the last name.
    */
   public function getLastName(): string
   {
      return $this->last_name;
   }

   /**
    * Get the Phone number.
    */
   public function getPhoneNumber(): ?string
   {
      return $this->phone;
   }

   /**
    * Get formatted properties.
    */
   public function toArray(): array
   {
      return [
         'email' => $this->getEmail(),
         'first_name' => $this->getFirstName(),
         'last_name' => $this->getLastName(),
         'phone' => $this->getPhoneNumber(),
      ];
   }

   public static function create(array $customer): self
   {
      if (! isset($customer['email'], $customer['first_name'], $customer['last_name'])) {
         throw new DtoCastException(self::class);
      }

      return new self(
         $customer['email'],
         $customer['first_name'],
         $customer['last_name'],
         $customer['phone_number']
      );
   }
}
