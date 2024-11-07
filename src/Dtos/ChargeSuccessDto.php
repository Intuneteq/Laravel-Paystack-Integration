<?php

namespace Intune\LaravelPaystack\Dtos;

use Intune\LaravelPaystack\Contracts\IDtoFactory;
use Intune\LaravelPaystack\Exceptions\DtoCastException;
use Intune\LaravelPaystack\Dtos\CustomerDto;

class ChargeSuccessDto implements IDtoFactory
{
   private function __construct(
      private int $id,
      private string $domain,
      private string $status,
      private string $reference,
      private int $amount,
      private ?string $message,
      private string $gatewayResponse,
      private string $paidAt,
      private string $createdAt,
      private string $channel,
      private string $currency,
      private ?string $ipAddress,
      private mixed $metadata,
      private ?array $feesBreakdown,
      private ?array $log,
      private int $fees,
      private ?array $feesSplit,
      private array $authorization,
      private CustomerDto $customer,
      private array $plan,
      private array $subaccount,
      private array $split,
      private ?string $orderId,
      private int $requestedAmount,
      private ?array $posTransactionData,
      private array $source
   ) {}

   public static function create(array $data): self
   {
      if (! isset(
         $data['id'],
         $data['domain'],
         $data['status'],
         $data['reference'],
         $data['amount'],
         $data['gateway_response'],
         $data['paid_at'],
         $data['created_at'],
         $data['channel'],
         $data['currency'],
         $data['authorization'],
         $data['customer'],
         $data['plan'],
         $data['subaccount'],
         $data['split'],
         $data['source']
      )) {
         throw new DtoCastException(self::class);
      }

      return new self(
         $data['id'],
         $data['domain'],
         $data['status'],
         $data['reference'],
         $data['amount'],
         $data['message'] ?? null,
         $data['gateway_response'],
         $data['paid_at'],
         $data['created_at'],
         $data['channel'],
         $data['currency'],
         $data['ip_address'] ?? null,
         $data['metadata'] ?? null,
         $data['fees_breakdown'] ?? null,
         $data['log'] ?? null,
         $data['fees'] ?? 0,
         $data['fees_split'] ?? null,
         $data['authorization'],
         CustomerDto::create($data['customer']),
         $data['plan'],
         $data['subaccount'],
         $data['split'],
         $data['order_id'] ?? null,
         $data['requested_amount'],
         $data['pos_transaction_data'] ?? null,
         $data['source']
      );
   }

   public function toArray(): array
   {
      return [
         'id' => $this->id,
         'domain' => $this->domain,
         'status' => $this->status,
         'reference' => $this->reference,
         'amount' => $this->amount,
         'message' => $this->message,
         'gateway_response' => $this->gatewayResponse,
         'paid_at' => $this->paidAt,
         'created_at' => $this->createdAt,
         'channel' => $this->channel,
         'currency' => $this->currency,
         'ip_address' => $this->ipAddress,
         'metadata' => $this->metadata,
         'fees_breakdown' => $this->feesBreakdown,
         'log' => $this->log,
         'fees' => $this->fees,
         'fees_split' => $this->feesSplit,
         'authorization' => $this->authorization,
         'customer' => $this->customer,
         'plan' => $this->plan,
         'subaccount' => $this->subaccount,
         'split' => $this->split,
         'order_id' => $this->orderId,
         'requested_amount' => $this->requestedAmount,
         'pos_transaction_data' => $this->posTransactionData,
         'source' => $this->source,
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

   public function getStatus(): string
   {
      return $this->status;
   }

   public function getReference(): string
   {
      return $this->reference;
   }

   public function getAmount(): int
   {
      return $this->amount;
   }

   public function getAmountInNaira(): int
   {
      return $this->amount / 100;
   }

   public function getMessage(): ?string
   {
      return $this->message;
   }

   public function getGatewayResponse(): string
   {
      return $this->gatewayResponse;
   }

   public function getPaidAt(): string
   {
      return $this->paidAt;
   }

   public function getCreatedAt(): string
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

   public function getMetadata(): mixed
   {
      return $this->metadata;
   }

   public function getFeesBreakdown(): ?array
   {
      return $this->feesBreakdown;
   }

   public function getLog(): ?array
   {
      return $this->log;
   }

   public function getFees(): int
   {
      return $this->fees;
   }

   public function getFeesSplit(): ?array
   {
      return $this->feesSplit;
   }

   public function getAuthorization(): array
   {
      return $this->authorization;
   }

   public function getCustomer(): CustomerDto
   {
      return $this->customer;
   }

   public function getPlan(): array
   {
      return $this->plan;
   }

   public function getSubaccount(): array
   {
      return $this->subaccount;
   }

   public function getSplit(): array
   {
      return $this->split;
   }

   public function getOrderId(): ?string
   {
      return $this->orderId;
   }

   public function getRequestedAmount(): int
   {
      return $this->requestedAmount;
   }

   public function getPosTransactionData(): ?array
   {
      return $this->posTransactionData;
   }

   public function getSource(): array
   {
      return $this->source;
   }
}