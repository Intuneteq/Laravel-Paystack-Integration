<?php

namespace Intune\LaravelPaystack\Dtos;

use Illuminate\Support\Collection;
use Intune\LaravelPaystack\Contracts\IDtoFactory;
use Intune\LaravelPaystack\Dtos\SubscriptionDto;
use Intune\LaravelPaystack\Enums\SubscriptionStatusEnum;
use Intune\LaravelPaystack\Exceptions\DtoCastException;

/**
 * @author @Intuneteq
 *
 * @version 1.0
 *
 * @since 26-06-2024
 *
 * Data Transfer Object for customer requests from paystack
 */
class CustomerDto implements IDtoFactory
{
   /**
    * CustomerDto constructor.
    *
    * @param  string  $id  Customer Id from payment gateway
    * @param  string  $email  Email From Payment Gateway
    * @param  string  $code  Customer Code from Payment Gateway
    * @param  string  $first_name  The first name saved in the payment gateway
    * @param  string  $last_name  The last name saved in the payment gateway
    * @param  string  $createdAt  Date of creation
    * @param  Collection  $subscriptions  The Customer's subscriptions
    */
   private function __construct(
      private string $id,
      private string $email,
      private string $code,
      private ?string $first_name,
      private ?string $last_name,
      private string $createdAt,
      private Collection $subscriptions
   ) {}

   /**
    * Get the ID.
    */
   public function getId(): string
   {
      return $this->id;
   }

   /**
    * Get the email.
    */
   public function getEmail(): string
   {
      return $this->email;
   }

   /**
    * Get the code.
    */
   public function getCode(): string
   {
      return $this->code;
   }

   /**
    * Get the first name.
    */
   public function getFirstName(): ?string
   {
      return $this->first_name;
   }

   /**
    * Get the last name.
    */
   public function getLastName(): ?string
   {
      return $this->last_name;
   }

   /**
    * Get the created at timestamp.
    */
   public function getCreatedAt(): string
   {
      return $this->createdAt;
   }

   /**
    * Get the subscriptions.
    *
    * @return Collection<int, SubscriptionDto>
    */
   public function getSubscriptions(): Collection
   {
      return $this->subscriptions;
   }

   /**
    * Check if the customer is subscribed based on their subscription statuses.
    *
    * @return bool True if the customer is subscribed, false otherwise.
    */
   public function isSubscribed(): bool
   {
      if ($this->subscriptions->isEmpty()) {
         return false;
      }

      $validStatuses = [
         SubscriptionStatusEnum::ACTIVE->value,
         SubscriptionStatusEnum::NON_RENEWING->value,
         SubscriptionStatusEnum::ATTENTION->value,
         SubscriptionStatusEnum::PENDING->value,
      ];

      return $this->subscriptions->contains(function (SubscriptionDto $subscription) use ($validStatuses) {
         return in_array($subscription->getStatus()->value, $validStatuses, true);
      });
   }

   /**
    * Get formatted properties.
    */
   public function toArray(): array
   {
      return [
         'id' => $this->getId(),
         'email' => $this->getEmail(),
         'code' => $this->getCode(),
         'first_name' => $this->getFirstName(),
         'last_name' => $this->getLastName(),
         'created_at' => $this->getCreatedAt(),
         'subscriptions' => $this->subscriptions->map(function (SubscriptionDto $subscription) {
            return $subscription->toArray();
         }),
      ];
   }

   /**
    * Create an instance of CustomerDto from an array of data.
    *
    * @throws DtoCastException
    */
   public static function create(array $customer): self
   {
      if (! isset($customer['id'], $customer['email'], $customer['customer_code'], $customer['subscriptions'], $customer['createdAt'])) {
         throw new DtoCastException(self::class);
      }

      // Create SubscriptionDto objects from the array data
      $subscriptions = collect($customer['subscriptions'])->map(function ($subscriptionData) {
         return SubscriptionDto::create($subscriptionData);
      });

      return new self(
         $customer['id'],
         $customer['email'],
         $customer['customer_code'],
         $customer['first_name'],
         $customer['last_name'],
         $customer['createdAt'],
         $subscriptions
      );
   }
}
