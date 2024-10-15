<?php

namespace Intune\LaravelPaystack\Dtos;

use Illuminate\Support\Collection;
use Intune\LaravelPaystack\Enums\SubscriptionStatusEnum;
use Intune\LaravelPaystack\Exceptions\DtoCastException;

/**
 * @author @Intuneteq
 *
 * @version 1.0
 *
 * @since 26-06-2024
 *
 * Data Transfer Object for subscription requests from paystack
 */
class SubscriptionDto implements IDtoFactory
{
   /**
    * SubscriptionDto constructor.
    *
    * @param  string  $id  Subscription Id from the Payment Gateway
    * @param  string  $code  Subscription code from the Payment Gateway
    * @param  SubscriptionStatusEnum  $status  Subscription status
    * @param  string  $next_payment_date  Date of Next Payment
    * @param  string  $createdAt  Subcription Date
    *
    * @var Collection<int, InvoiceDto> A collection of all the subscription invoices
    */
   private function __construct(
      private string $id,
      private string $code,
      private int $amount,
      private SubscriptionStatusEnum $status,
      private string $next_payment_date,
      private string $createdAt,
      private Collection $invoices,
      private ?InvoiceDto $most_recent_invoice
   ) {}

   /**
    * Get the subscription ID.
    */
   public function getId(): string
   {
      return $this->id;
   }

   /**
    * Get the subscription code.
    */
   public function getCode(): string
   {
      return $this->code;
   }

   /**
    * Get the subscription amount.
    */
   public function getAmount(): int
   {
      return $this->amount / 100; // convert to naira
   }

   /**
    * Get the subscription status.
    */
   public function getStatus(): SubscriptionStatusEnum
   {
      return $this->status;
   }

   /**
    * Get the subscription next payment date timestamp.
    */
   public function getNextPaymentDate(): string
   {
      return $this->next_payment_date;
   }

   /**
    * Get the subscription creation timestamp.
    */
   public function getCreatedAt(): string
   {
      return $this->createdAt;
   }

   /**
    * Get the subscription invoices.
    */
   public function getInvoices(): Collection
   {
      return $this->invoices;
   }

   /**
    * Get the subscription invoices formatted response.
    *
    * @return array
    */
   public function getPlans()
   {
      return $this->invoices->map(function (InvoiceDto $invoice) {
         return $invoice->toArray();
      });
   }

   public function getMostRecentInvoice(): InvoiceDto
   {
      return $this->most_recent_invoice;
   }

   /**
    * Get formatted properties.
    */
   public function toArray(): array
   {
      return [
         'id' => $this->getId(),
         'code' => $this->getCode(),
         'amount' => $this->getAmount(),
         'status' => $this->getStatus()->value,
         'next_payment_date' => $this->getNextPaymentDate(),
         'created_at' => $this->getCreatedAt(),
         'plans' => $this->getPlans(),
      ];
   }

   public function getTotalBilling(): int
   {
      return $this->invoices->sum(fn(InvoiceDto $invoice) => $invoice->getAmount());
   }

   /**
    * Create an instance of SubscriptionDto from an array of data.
    *
    * @throws DtoCastException
    */
   public static function create(array $data): self
   {
      if (! isset($data['subscription_code'], $data['status'], $data['next_payment_date'], $data['invoices'])) {
         throw new DtoCastException(self::class);
      }

      // Create SubscriptionDto objects from the array data
      $invoices = collect($data['invoices'])->map(function ($invoice) {
         return InvoiceDto::create($invoice);
      });

      $most_recent_invoice = null;

      if (isset($data['most_recent_invoice'])) {
         $most_recent_invoice = InvoiceDto::create($data['most_recent_invoice']);
      }

      $status = SubscriptionStatusEnum::from($data['status']);

      return new self(
         $data['id'] ?? '',
         $data['subscription_code'],
         $data['amount'] ?? '',
         $status,
         $data['next_payment_date'],
         $data['createdAt'] ?? '',
         $invoices,
         $most_recent_invoice
      );
   }
}
