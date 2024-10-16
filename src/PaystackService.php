<?php

namespace Intune\LaravelPaystack;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intune\LaravelPaystack\Contracts\PaystackInterface;
use Intune\LaravelPaystack\Contracts\TransactionInitPayloadDto;
use Intune\LaravelPaystack\Dtos\BankDto;
use Intune\LaravelPaystack\Dtos\CustomerDto;
use Intune\LaravelPaystack\Dtos\SubscriptionDto;
use Intune\LaravelPaystack\Dtos\TransactionInitializationDto;
use Intune\LaravelPaystack\Dtos\TransferDto;
use Intune\LaravelPaystack\Dtos\TransferRecipientDto;
use Intune\LaravelPaystack\Dtos\UserDto;

class PaystackService implements PaystackInterface
{
   /**
    * URL for initializing transactions.
    *
    * @var string
    */
   private $initialize_transaction_url = 'https://api.paystack.co/transaction/initialize';

   /**
    * URL for subscriptions.
    *
    * @var string
    */
   private $subscription_endpoint = 'https://api.paystack.co/subscription';

   /**
    * Base URL for Paystack API.
    *
    * @var string
    */
   private $base_url = 'https://api.paystack.co';

   /**
    * Paystack secret key.
    *
    * @var string
    */
   private $secret_key;

   /**
    * Premium plan code from configuration.
    *
    * @var string
    */
   private $premium_plan_code;

   /**
    * Callback URL for transactions.
    *
    * @var string
    */
   private $redirect_url;

   /**
    * Whitelisted domains.
    *
    * @var array
    */
   private $white_list;

   /**
    * Create a new PaystackService instance.
    */
   public function __construct()
   {
      $this->secret_key = config('paystack.secret');
      $this->premium_plan_code = config('paystack.plan_code');
      $this->redirect_url = config('paystack.redirect_url');
      $this->white_list = config('paystack.white_list');
   }

   /**
    * Create a new customer on Paystack.
    *
    * @param UserDto $user The user data transfer object
    * @return CustomerDto The created customer data transfer object
    * @throws Exception If an error occurs during the API request
    * @see https://paystack.com/docs/api/customer/
    */
   public function createCustomer(UserDto $user): CustomerDto
   {
      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Content-Type' => 'application/json',
      ])->post("{$this->base_url}/customer", $user->toArray());

      if ($response->failed()) {
         Log::critical('Failed to create customer', [
            'status' => $response->status(),
            'message' => $this->getErrorMessage($response->json()),
         ]);

         throw new Exception('Error creating customer: ' . $this->getErrorMessage($response->json()));
      }

      return CustomerDto::create($response['data']);
   }

   /**
    * Fetch a customer from Paystack by email.
    *
    * @param string $email Customer's email
    * @return CustomerDto|null The fetched customer data transfer object or null if not found
    * @throws Exception If an error occurs during the API request
    * @see https://paystack.com/docs/api/customer/#fetch
    */
   public function fetchCustomer(string $email): ?CustomerDto
   {
      $url = $this->base_url . "/customer/$email";

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
      ])->get($url);

      if ($response->notFound()) {
         return null;
      }

      if ($response->failed()) {
         throw new Exception($response->reason(), $response->status());
      }

      return CustomerDto::create($response['data']);
   }

   /**
    * Initialize a subscription transaction on Paystack.
    *
    * @param string $email User's email
    * @param int $amount Transaction Amount
    * @return TransactionInitializationDto The transaction initialization data transfer object
    * @throws Exception If an error occurs during the API request
    * @see https://paystack.com/docs/api/transaction/#initialize
    */
   public function initializeSubscriptionTransaction(string $email, int $amount): TransactionInitializationDto
   {
      $payload = [
         'email' => $email,
         'amount' => $amount,
         'callback_url' => $this->redirect_url,
         'plan' => $this->premium_plan_code,
      ];

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Cache-Control' => 'no-cache',
         'Content-Type' => 'application/json',
      ])->post($this->initialize_transaction_url, $payload);

      if ($response->failed()) {
         Log::critical('Error initializing subscription transaction', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         throw new Exception('Error Initializing Paystack Transaction');
      }

      return TransactionInitializationDto::create($response['data']);
   }

   /**
    * Initialize a transaction on Paystack.
    *
    * @param TransactionInitPayloadDto $data The transaction initialization payload data transfer object
    * @return TransactionInitializationDto The transaction initialization data transfer object
    * @throws Exception If an error occurs during the API request
    */
   public function initializeTransaction(TransactionInitPayloadDto $data): TransactionInitializationDto
   {
      $payload = array_merge($data->toArray(), [
         'callback_url' => $this->redirect_url,
      ]);

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Cache-Control' => 'no-cache',
         'Content-Type' => 'application/json',
      ])->post($this->initialize_transaction_url, $payload);

      if ($response->failed()) {
         Log::critical('Error initializing purchase transaction', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         throw new Exception('Error Initializing Paystack Transaction For Purchase');
      }

      return TransactionInitializationDto::create($response['data']);
   }

   /**
    * Create a subscription on Paystack.
    *
    * @param string $customer_id The Paystack customer ID of the user
    * @return SubscriptionDto The created subscription data transfer object
    * @throws Exception If an error occurs during the API request
    * @see https://paystack.com/docs/api/subscription#create
    */
   public function createSubscription(string $customer_id): SubscriptionDto
   {
      $payload = [
         'customer' => $customer_id,
         'plan' => $this->premium_plan_code,
      ];

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Content-Type' => 'application/json',
      ])->post($this->subscription_endpoint, $payload);

      if ($response->failed()) {
         Log::critical('Error occurred while creating subscription', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         throw new Exception('Error Subscribing User');
      }

      return SubscriptionDto::create($response['data']);
   }

   /**
    * Manage a subscription on Paystack.
    *
    * @param string $subscription_id Paystack's subscription ID for the user
    * @return string A redirect link for the user to manage the subscription on Paystack's UI
    * @throws Exception If an error occurs during the API request
    */
   public function manageSubscription(string $subscription_id): string
   {
      $url = "{$this->base_url}/subscription/{$subscription_id}/manage/link";

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
      ])->get($url);

      if ($response->failed()) {
         Log::critical('Error managing subscription', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         throw new Exception('Error Managing Subscription');
      }

      return $response['data']['link'];
   }

   /**
    * Fetch a subscription from Paystack.
    *
    * @param string $subscription_id Paystack's subscription ID of the user
    * @return SubscriptionDto|null The fetched subscription data transfer object or null if not found
    * @throws Exception If an error occurs during the API request
    */
   public function fetchSubscription(string $subscription_id): ?SubscriptionDto
   {
      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
      ])->get("{$this->base_url}/subscription/{$subscription_id}");

      if ($response->notFound()) {
         return null;
      }

      if ($response->failed()) {
         throw new Exception('Failed to fetch subscription', $response->status());
      }

      return SubscriptionDto::create($response['data']);
   }

   /**
    * Enable a subscription for a customer on Paystack.
    *
    * @param string $subscription_id The Paystack ID of the subscription to enable
    * @return SubscriptionDto The updated subscription data transfer object
    * @throws Exception If an error occurs during the API request
    * @see https://paystack.com/docs/api/subscription#enable
    */
   public function enableSubscription(string $subscription_id): bool
   {
      $subscription = $this->fetchSubscription($subscription_id);

      $payload = [
         'code' => $subscription_id,
         'token' => $subscription['email_token'],
      ];

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Cache-Control' => 'no-cache',
         'Content-Type' => 'application/json',
      ])->post("{$this->base_url}/subscription/enable", $payload);

      if ($response->failed()) {
         Log::critical('Error occurred while Enabling subscription', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         throw new Exception('Error Enabling User');
      }

      return $response['data']['status'];
   }

   public function disableSubscription(string $subscription_code): bool
   {
      $subscription = $this->fetchSubscription($subscription_code);

      $payload = [
         'code' => $subscription_code,
         'token' => $subscription['email_token'],
      ];

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Cache-Control' => 'no-cache',
         'Content-Type' => 'application/json',
      ])->post("{$this->base_url}/subscription/disable", $payload);

      if ($response->failed()) {
         Log::critical('Error occurred while Disabling subscription', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         throw new Exception('Error Disabling Subscription');
      }

      return $response['data']['status'];
   }

   /**
    * Validate a Paystack webhook request.
    *
    * @param string $payload The webhook payload received from Paystack
    * @return bool True if the webhook is valid; otherwise, false
    * @throws Exception If an error occurs during the validation
    * @see https://paystack.com/docs/api/webhooks#verify
    */
   public function isValidPaystackWebhook(string $payload, string $signature): bool
   {
      $computedSignature = hash_hmac('sha512', $payload, $this->secret_key);

      return $computedSignature === $signature;
   }

   /**
    * Fetch banks from Paystack.
    *
    * @return Collection A collection of bank data transfer objects
    * @param string|null $country Filter list by country
    * @throws Exception If an error occurs during the API request
    * @see https://paystack.com/docs/api/bank
    */
   public function fetchBanks(?string $country = ""): Collection
   {
      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
      ])->get("{$this->base_url}/bank?country=" . $country);

      if ($response->failed()) {
         Log::critical('Failed to fetch banks', [
            'status' => $response->status(),
            'message' => $this->getErrorMessage($response->json()),
         ]);

         throw new Exception('Error fetching banks: ' . $this->getErrorMessage($response->json()));
      }

      return collect($response['data'])->map(function ($item) {
         return BankDto::create($item);
      });
   }

   /**
    * Validate a bank account number on Paystack.
    *
    * @param string $account_number The bank account number to validate
    * @param string $bank_code The bank code for the account
    * @return array The validation result including account name and status
    * @throws Exception If an error occurs during the API request
    * @see https://paystack.com/docs/api/bank#validate
    */
   public function validateAccountNumber(string $account_number, string $bank_code): bool
   {
      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
      ])->get("{$this->base_url}/bank/resolve?account_number=" . $account_number . '&bank_code=' . $bank_code);

      if ($response->failed()) {
         Log::error('Error Validating Account Number', [
            'code' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         return false;
      }

      return $response['status'];
   }

   /**
    * Check if the Paystack PT balance is sufficient for a transfer.
    *
    * @param int $amount The amount to check against the Paystack's balance
    * @return bool True if the balance is sufficient; otherwise, false
    * @throws Exception If an error occurs during the balance check
    * @see https://paystack.com/docs/api/balance#check
    */
   public function checkPTBalanceIsSufficient(int $amount): bool
   {
      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
      ])->get("{$this->base_url}/balance");

      if ($response->failed()) {
         Log::error('Error Checking PT Balance', [
            'code' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         return false;
      }

      $balance = $response['data'][0]['balance'];

      $isSufficent = $balance > $amount;

      if (! $isSufficent) {
         Log::alert('INSUFFICIENT PT BALANCE', [
            'Amount Initiated' => $amount,
            'PT BALANCE' => $balance,
         ]);
      }

      return $balance > $amount;
   }

   /**
    * Create a transfer recipient on Paystack.
    *
    * @param string $name The name of the recipient
    * @param string $account_number The account number of the recipient
    * @param string $bank_code The bank code of the recipient's bank
    * @return TransferRecipientDto The created transfer recipient data transfer object
    * @throws Exception If an error occurs during the API request
    * @see https://paystack.com/docs/api/transfer#recipient
    */
   public function createTransferRecipient(string $name, string $account_number, string $bank_code): TransferRecipientDto
   {
      $payload = [
         'type' => 'nuban',
         'name' => $name,
         'account_number' => $account_number,
         'bank_code' => $bank_code,
         'currency' => 'NGN',
      ];

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Cache-Control' => 'no-cache',
         'Content-Type' => 'application/json',
      ])->post("{$this->base_url}/transferrecipient", $payload);

      if ($response->failed()) {
         Log::critical('ERROR CREATING A RECIPIENT', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         throw new Exception('Error Creating A Recipient');
      }

      return TransferRecipientDto::create($response['data']);
   }

   /**
    * Initiate a transfer on Paystack.
    *
    * @param string $recipient_id The Paystack ID of the recipient
    * @param int $amount The amount to transfer
    * @param string $currency The currency for the transfer (default: 'NGN')
    * @return TransferDto The created transfer data transfer object
    * @throws Exception If an error occurs during the API request
    * @see https://paystack.com/docs/api/transfer
    */
   public function initiateTransfer(string $amount, string $recipient_code, string $reference): TransferDto
   {
      $payload = [
         'source' => 'balance',
         'reason' => 'Payout',
         'amount' => $amount * 100,
         'recipient' => $recipient_code,
         'reference' => $reference,
      ];

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Cache-Control' => 'no-cache',
         'Content-Type' => 'application/json',
      ])->post("{$this->base_url}/transfer", $payload);

      if ($response->failed()) {
         Log::critical('ERROR INITIATING A TRANSFER', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         throw new Exception('Error Initiating Transfer');
      }

      return TransferDto::create($response['data']);
   }


   /**
    * Extract error messages from the API response.
    *
    * @param array $response The API response
    * @return string The extracted error message
    */
   private function getErrorMessage(array $response): string
   {
      return $response['message'] ?? 'Unknown error occurred';
   }
}
