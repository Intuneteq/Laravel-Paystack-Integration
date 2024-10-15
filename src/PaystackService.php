<?php

namespace Intune\LaravelPaystack;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intune\LaravelPaystack\Contracts\PaystackInterface;
use Intune\LaravelPaystack\Dtos\BankDto;
use Intune\LaravelPaystack\Dtos\CustomerDto;
use Intune\LaravelPaystack\Dtos\SubscriptionDto;
use Intune\LaravelPaystack\Dtos\TransactionInitializationDto;
use Intune\LaravelPaystack\Dtos\TransactionInitPayloadDto;
use Intune\LaravelPaystack\Dtos\TransferDto;
use Intune\LaravelPaystack\Dtos\TransferRecipientDto;
use Intune\LaravelPaystack\Dtos\UserDto;

class PaystackService implements PaystackInterface
{
   private $initialize_transaction_url = 'https://api.paystack.co/transaction/initialize';

   private $subscription_endpoint = 'https://api.paystack.co/subscription';

   private $base_url = 'https://api.paystack.co';

   private $secret_key;

   private $premium_plan_code;

   private $redirect_url;

   private $white_list;

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
    *
    *
    * @throws \Exception
    *
    * @see https://paystack.com/docs/api/customer/
    */
   public function createCustomer(UserDto $user): CustomerDto
   {
      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Content-Type' => 'application/json',
      ])->post($this->base_url . '/customer', $user->toArray())->throw()->json();

      return CustomerDto::create($response['data']);
   }

   /**
    * Fetch a customer from Paystack by email.
    *
    * @param  string  $email  Customer's email
    *
    * @throws Exception
    *
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
    * Initialize a transaction on Paystack.
    *
    * @param  string  $email  User's email
    * @param  int  $amount  Transaction Amount
    * @param  bool  $isSubscription  True if it is a subscription transaction, false otherwise
    *
    * @throws \Exception
    *
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
         Log::critical('Fetch subscription error', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         throw new Exception('Error Initializing Paystack Transaction');
      }

      return TransactionInitializationDto::create($response['data']);
   }

   /**
    * Initialize a purchase transaction on Paystack.
    *
    *
    * @return TransactionInitializationDto
    *
    * @throws \Exception
    */
   public function initializePurchaseTransaction(TransactionInitPayloadDto $data): TransactionInitializationDto
   {
      $payload = $data->toArray();

      $payload = array_merge($payload, [
         'callback_url' => $this->redirect_url,
      ]);

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
         'Cache-Control' => 'no-cache',
         'Content-Type' => 'application/json',
      ])->post($this->initialize_transaction_url, $payload);

      if ($response->failed()) {
         Log::critical('Fetch subscription error', [
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
    * @param  string  $customer_id  The paystack cutomer id of the user
    * @return SubscriptionDto
    *
    * @throws \Exception
    *
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
         Log::critical('Error Occured To Create Subscription', [
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
    * @param  string  $subscription_id  Paystack's subscription for the user
    * @return string A redirect link for the user to manage the subscription on paystack's UI
    *
    * @throws \Exception
    */
   public function manageSubscription(string $subscription_id): string
   {
      $url = "{$this->base_url}/subscription/{$subscription_id}/manage/link";

      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
      ])->get($url);

      if ($response->failed()) {
         Log::critical('Manage Paystack error', [
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
    * @param  string  $subscription_id  Paystack's subscription id of the user
    *
    * @throws \Exception
    */
   public function fetchSubscription(string $subscription_id): ?SubscriptionDto
   {
      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
      ])->get("{$this->base_url}/subscription/{$subscription_id}");

      if ($response->failed()) {
         Log::critical('Fetch subscription error', [
            'status' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         return null;
      }

      return SubscriptionDto::create($response['data']);
   }

   /**
    * Enable a subscription on Paystack.
    *
    * @return array
    *
    * @throws \Exception
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
      ])->post("{$this->base_url}/subscription/enable", $payload)->throw()->json();

      return $response['data']['status'];
   }

   /**
    * Disable a subscription on Paystack.
    *
    * @return bool True if disabled, false otherwise
    *
    * @throws \Exception
    */
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
      ])->post("{$this->base_url}/subscription/disable", $payload)->throw()->json();

      return $response['data']['status'];
   }

   /**
    * Validate a Paystack webhook.
    *
    * @param  string  $payload  The Payload from Paystack
    * @param  string  $signature  The `x-paystack-signature` request header from paystack
    * @return bool True when from paystack, false otherwise
    */
   public function isValidPaystackWebhook($payload, $signature): bool
   {
      $computedSignature = hash_hmac('sha512', $payload, $this->secret_key);

      return $computedSignature === $signature;
   }

   /**
    * Retrive Bank List from Paystack
    *
    * @return null|Collection<int, BankDto>
    */
   public function getBankList(): ?Collection
   {
      $response = Http::withHeaders([
         'Authorization' => 'Bearer ' . $this->secret_key,
      ])->get("{$this->base_url}/bank?country=nigeria");

      if ($response->failed()) {
         Log::error('Error Fetching Bank List from paystack', [
            'code' => $response->status(),
            'message' => $response->reason(),
            'body' => $response->body(),
         ]);

         return null;
      }

      return collect($response['data'])->map(function ($data) {
         return BankDto::create($data);
      });
   }

   /**
    * Validate an account number with Paystack.
    *
    * @param  string  $account_number  The Account Number
    * @param  string  $bank_code  Paystack Bank Code
    * @return bool True when valid, false otherwise
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
    * Check the current PT balance against the amount to be withdrawn.
    *
    * @param  int  $amount  The withdrawal amount initiated
    * @return bool True when there is sufficient balance, false otherwise
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
    * @param  string  $name  Bank account name
    * @param  string  $account_number  Bank account number
    * @param  string  $bank_code  Paystack's bank code
    *
    * @throws \Exception
    */
   public function createTransferRecipient($name, $account_number, $bank_code): TransferRecipientDto
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
    * @param  string  $amount  Transfer ammount
    * @param  string  $recipient_code  The user's paystack recipient code
    * @return TransferDto
    *
    * @throws \Exception
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

   public function getErrorMessage(array $body)
   {
      return $body['message'];
   }
}
