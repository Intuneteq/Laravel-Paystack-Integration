<?php

namespace Intune\LaravelPaystack\Dtos;

use Intune\LaravelPaystack\Contracts\IDtoFactory;
use Intune\LaravelPaystack\Exceptions\DtoCastException;

/**
 * Data Transfer Object for User data associated with Paystack API.
 *
 * This DTO encapsulates the data related to a user, such as their email, name, and phone number.
 *
 * @author @Intuneteq
 * @version 1.0
 * @since 15-10-2024
 * @package Intune\LaravelPaystack\Dtos
 */
class UserDto implements IDtoFactory
{
   /**
    * UserDto constructor.
    *
    * @param string      $email      The email address of the user.
    * @param string      $first_name The first name of the user.
    * @param string      $last_name  The last name of the user.
    * @param string|null $phone      The phone number of the user (optional).
    */
   private function __construct(
      private string $email,
      private string $first_name,
      private string $last_name,
      private ?string $phone,
   ) {}

   /**
    * Get the user's email address.
    *
    * @return string The email address of the user.
    */
   public function getEmail(): string
   {
      return $this->email;
   }

   /**
    * Get the user's first name.
    *
    * @return string The first name of the user.
    */
   public function getFirstName(): string
   {
      return $this->first_name;
   }

   /**
    * Get the user's last name.
    *
    * @return string The last name of the user.
    */
   public function getLastName(): string
   {
      return $this->last_name;
   }

   /**
    * Get the user's phone number.
    *
    * @return string|null The phone number of the user, or null if not provided.
    */
   public function getPhoneNumber(): ?string
   {
      return $this->phone;
   }

   /**
    * Convert the DTO properties to an associative array.
    *
    * @return array Associative array representation of the user's data.
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

   /**
    * Create a new instance of UserDto from an array of user data.
    *
    * @param array $customer The array containing user data.
    *
    * @return self
    * @throws DtoCastException If required fields (email, first_name, last_name) are missing.
    */
   public static function create(array $customer): self
   {
      if (! isset($customer['email'], $customer['first_name'], $customer['last_name'])) {
         throw new DtoCastException(self::class);
      }

      return new self(
         $customer['email'],
         $customer['first_name'],
         $customer['last_name'],
         $customer['phone_number'] ?? null // Use null if phone_number is not set
      );
   }
}
