<?php

namespace Intune\LaravelPaystack\Dtos;

use Intune\LaravelPaystack\Contracts\IDtoFactory;
use Intune\LaravelPaystack\Exceptions\DtoCastException;

class TransactionVerificationDto implements IDtoFactory
{
   private function __construct(
      private int $id,
      private string $domain,
      private string $transactionStatus,
      private string $reference,
      private ?string $receiptNumber,
      private int $amount,
      private ?string $gatewayResponse,
      private ?string $paidAt,
      private ?string $createdAt,
      private string $channel,
      private string $currency,
      private ?string $ipAddress,
      private array $log,
      private int $fees,
      private ?array $authorization,
      private ?CustomerDto $customer
   ) {}

   public static function create(array $data): self
   {
      if (! isset(
         $data['id'],
         $data['domain'],
         $data['status'],
         $data['reference'],
         $data['amount'],
         $data['channel'],
         $data['currency']
      )) {
         throw new DtoCastException(self::class);
      }

      return new self(
         $data['id'],
         $data['domain'],
         $data['status'],
         $data['reference'],
         $data['receipt_number'] ?? null,
         $data['amount'],
         $data['gateway_response'] ?? null,
         $data['paid_at'] ?? null,
         $data['created_at'] ?? null,
         $data['channel'],
         $data['currency'],
         $data['ip_address'] ?? null,
         $data['log'] ?? [],
         $data['fees'] ?? 0,
         $data['authorization'] ?? null,
         $data['customer'] ? CustomerDto::create($data['customer']) : null
      );
   }

   public function toArray(): array
   {
      return [
         'id' => $this->id,
         'domain' => $this->domain,
         'status' => $this->transactionStatus,
         'reference' => $this->reference,
         'receipt_number' => $this->receiptNumber,
         'amount' => $this->amount,
         'gateway_response' => $this->gatewayResponse,
         'paid_at' => $this->paidAt,
         'created_at' => $this->createdAt,
         'channel' => $this->channel,
         'currency' => $this->currency,
         'ip_address' => $this->ipAddress,
         'log' => $this->log,
         'fees' => $this->fees,
         'authorization' => $this->authorization,
         'customer' => $this->customer,
      ];
   }

   // Getters for each property
   public function getId(): int
   {
      return $this->id;
   }

   public function getDomain(): string
   {
      return $this->domain;
   }

   public function getTransactionStatus(): string
   {
      return $this->transactionStatus;
   }

   public function getReference(): string
   {
      return $this->reference;
   }

   public function getReceiptNumber(): ?string
   {
      return $this->receiptNumber;
   }

   public function getAmount(): int
   {
      return $this->amount;
   }

   public function getGatewayResponse(): ?string
   {
      return $this->gatewayResponse;
   }

   public function getPaidAt(): ?string
   {
      return $this->paidAt;
   }

   public function getCreatedAt(): ?string
   {
      return $this->createdAt;
   }

   public function getChannel(): string
   {
      return $this->channel;
   }

   public function getCurrency(): string
   {
      return $this->currency;
   }

   public function getIpAddress(): ?string
   {
      return $this->ipAddress;
   }

   public function getLog(): array
   {
      return $this->log;
   }

   public function getFees(): int
   {
      return $this->fees;
   }

   public function getAuthorization(): ?array
   {
      return $this->authorization;
   }

   public function getCustomer(): ?CustomerDto
   {
      return $this->customer;
   }
}
