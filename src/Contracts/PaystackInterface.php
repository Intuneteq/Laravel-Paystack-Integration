<?php

namespace Intune\LaravelPaystack\Contracts;

use Illuminate\Support\Collection;
use Intune\LaravelPaystack\Dtos\CustomerDto;
use Intune\LaravelPaystack\Dtos\SubscriptionDto;
use Intune\LaravelPaystack\Dtos\TransactionInitializationDto;
use Intune\LaravelPaystack\Dtos\TransactionInitPayloadDto;
use Intune\LaravelPaystack\Dtos\TransferDto;
use Intune\LaravelPaystack\Dtos\TransferRecipientDto;
use Intune\LaravelPaystack\Dtos\UserDto;

interface PaystackInterface
{
   public function createCustomer(UserDto $user): CustomerDto;
   public function fetchCustomer(string $email): ?CustomerDto;

   public function initializePurchaseTransaction(TransactionInitPayloadDto $data): TransactionInitializationDto;
   public function initializeSubscriptionTransaction(string $email, int $amount): TransactionInitializationDto;

   public function createSubscription(string $customer_id): SubscriptionDto;
   public function fetchSubscription(string $subscription_id): ?SubscriptionDto;
   public function manageSubscription(string $subscription_id): string;
   public function enableSubscription(string $subscription_id): bool;
   public function disableSubscription(string $subscription_code): bool;

   public function isValidPaystackWebhook($payload, $signature): bool;
   public function validateAccountNumber(string $account_number, string $bank_code): bool;
   public function checkPTBalanceIsSufficient(int $amount): bool;
   public function getBankList(): ?Collection;

   public function initiateTransfer(string $amount, string $recipient_code, string $reference): TransferDto;
   public function createTransferRecipient($name, $account_number, $bank_code): TransferRecipientDto;
}
