<?php

namespace Intune\LaravelPaystack\Contracts;

use Illuminate\Support\Collection;
use Intune\LaravelPaystack\Dtos\CustomerDto;
use Intune\LaravelPaystack\Dtos\SubscriptionDto;
use Intune\LaravelPaystack\Dtos\TransactionInitializationDto;
use Intune\LaravelPaystack\Dtos\TransferDto;
use Intune\LaravelPaystack\Dtos\TransferRecipientDto;
use Intune\LaravelPaystack\Dtos\UserDto;

/**
 * Paystack API Service Interface.
 * 
 * This interface defines the minimum contract for interacting with the Paystack API.
 * 
 * @package Intune\LaravelPaystack\Contracts
 */
interface PaystackInterface
{
   /**
    * Create a customer on Paystack.
    * 
    * @param UserDto $user User data to create the customer.
    * 
    * @return CustomerDto
    */
   public function createCustomer(UserDto $user): CustomerDto;

   /**
    * Fetch a customer from Paystack by their email address.
    * 
    * @param string $email Customer's email address.
    * 
    * @return CustomerDto|null Returns the Customer DTO or null if not found.
    */
   public function fetchCustomer(string $email): ?CustomerDto;

   /**
    * Initialize a purchase transaction.
    * 
    * @param TransactionInitPayloadDto $data Payload containing transaction details.
    * 
    * @return TransactionInitializationDto
    */
   public function initializeTransaction(TransactionInitPayloadDto $data): TransactionInitializationDto;

   /**
    * Initialize a subscription transaction.
    * 
    * @param string $email Customer's email address.
    * @param int $amount Amount for the subscription.
    * 
    * @return TransactionInitializationDto
    */
   public function initializeSubscriptionTransaction(string $email, int $amount): TransactionInitializationDto;

   /**
    * Create a subscription for a customer.
    * 
    * @param string $customer_id The Paystack customer ID.
    * 
    * @return SubscriptionDto
    */
   public function createSubscription(string $customer_id): SubscriptionDto;

   /**
    * Fetch a subscription by its ID.
    * 
    * @param string $subscription_id The subscription ID.
    * 
    * @return SubscriptionDto|null Returns the Subscription DTO or null if not found.
    */
   public function fetchSubscription(string $subscription_id): ?SubscriptionDto;

   /**
    * Manage a subscription.
    * 
    * @param string $subscription_id The subscription ID.
    * 
    * @return string Returns the manage link to paystack's dashboard.
    */
   public function manageSubscription(string $subscription_id): string;

   /**
    * Enable a subscription.
    * 
    * @param string $subscription_id The subscription ID.
    * 
    * @return bool Returns true on success, false otherwise.
    */
   public function enableSubscription(string $subscription_id): bool;

   /**
    * Disable a subscription.
    * 
    * @param string $subscription_code The subscription code.
    * 
    * @return bool Returns true on success, false otherwise.
    */
   public function disableSubscription(string $subscription_code): bool;

   /**
    * Validate Paystack webhook payload.
    * 
    * @param mixed $payload The webhook payload.
    * @param string $signature The signature from Paystack for verification.
    * 
    * @return bool Returns true if the webhook is valid, false otherwise.
    */
   public function isValidPaystackWebhook(string $payload, string $signature): bool;

   /**
    * Validate an account number with the provided bank code.
    * 
    * @param string $account_number The account number to validate.
    * @param string $bank_code The bank code.
    * 
    * @return bool Returns true if the account number is valid, false otherwise.
    */
   public function validateAccountNumber(string $account_number, string $bank_code): bool;

   /**
    * Check if Paystack balance is sufficient for a transfer.
    * 
    * @param int $amount The amount to check.
    * 
    * @return bool Returns true if the balance is sufficient, false otherwise.
    */
   public function checkPTBalanceIsSufficient(int $amount): bool;

   /**
    * Get a list of available banks from Paystack.
    * 
    * @return Collection|null Returns a collection of banks or null if not available.
    */
   public function fetchBanks(?string $country = ""): ?Collection;

   /**
    * Initiate a transfer to a recipient.
    * 
    * @param string $amount The amount to transfer.
    * @param string $recipient_code The recipient code.
    * @param string $reference The transfer reference.
    * 
    * @return TransferDto
    */
   public function initiateTransfer(string $amount, string $recipient_code, string $reference): TransferDto;

   /**
    * Create a transfer recipient.
    * 
    * @param string $name Recipient's name.
    * @param string $account_number Recipient's account number.
    * @param string $bank_code Bank code of the recipient's bank.
    * 
    * @return TransferRecipientDto
    */
   public function createTransferRecipient(string $name, string $account_number, string $bank_code): TransferRecipientDto;
}
