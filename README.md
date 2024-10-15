# Laravel Paystack Package

A Laravel package for seamless integration with the Paystack payment gateway. This package provides various functionalities, including customer management, transaction initialization, subscription handling, and more.

## Features

- Create and manage customers on Paystack.
- Initialize transactions for purchases and subscriptions.
- Handle subscriptions: create, manage, enable, and disable.
- Validate account numbers and retrieve a list of banks.
- Initiate transfers and check balances.

## Installation

You can install the package via Composer:

```bash
composer require intune/laravel-paystack
```

## Configuration

After installation, publish the configuration file using the following command:

```bash
php artisan vendor:publish --provider="Intune/laravel-paystack\PaystackServiceProvider"
```

This will create a `paystack.php` file in your `config` directory, where you can set your Paystack API credentials:

```php
return [
    'secret' => env('PAYSTACK_SECRET_KEY'),
    'plan_code' => env('PAYSTACK_PLAN_CODE'),
    'redirect_url' => env('PAYSTACK_REDIRECT_URL'),
    'white_list' => env('PAYSTACK_WHITE_LIST'),
];
```

Make sure to add the following environment variables to your `.env` file:

```plaintext
PAYSTACK_SECRET_KEY=your_paystack_secret_key
PAYSTACK_PLAN_CODE=your_paystack_plan_code
PAYSTACK_REDIRECT_URL=your_redirect_url
PAYSTACK_WHITE_LIST=your_white_list
```

## Usage

### Creating a Customer

To create a new customer on Paystack:

```php
use Intune\LaravelPaystack\Dtos\UserDto;
use Intune\LaravelPaystack\PaystackService;

$paystackService = app(PaystackService::class);

$userDto = UserDto::create([
    'email' => 'user@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'phone_number' => '1234567890', // Optional
]);

$customer = $paystackService->createCustomer($userDto);
```

### Initializing a Transaction

To initialize a transaction:

```php
$email = 'customer@example.com';
$amount = 1000; // Amount in kobo (1000 kobo = 10 NGN)

$dto = TransactionInitPayloadDto::create([
    'email' => $email,
    'amount' => $amount,
    // other transaction details
]);

$transaction = $paystackService->initializePurchaseTransaction($dto);
```

### Subscribing a Customer

To create a subscription for a customer:

```php
$customer_id = 'customer_id_from_paystack';

$subscription = $paystackService->createSubscription($customer_id);
```

### Managing a Subscription

To get a link for managing a subscription:

```php
$subscription_id = 'subscription_id';

$manageLink = $paystackService->manageSubscription($subscription_id);
```

### Validating a Webhook

To validate a Paystack webhook:

```php
$payload = request()->getContent();
$signature = request()->header('x-paystack-signature');

if ($paystackService->isValidPaystackWebhook($payload, $signature)) {
    // Valid webhook
}
```

## Available Methods

- `createCustomer(UserDto $user): CustomerDto`
- `fetchCustomer(string $email): ?CustomerDto`
- `initializePurchaseTransaction(TransactionInitPayloadDto $data): TransactionInitializationDto`
- `initializeSubscriptionTransaction(string $email, int $amount): TransactionInitializationDto`
- `createSubscription(string $customer_id): SubscriptionDto`
- `manageSubscription(string $subscription_id): string`
- `fetchSubscription(string $subscription_id): ?SubscriptionDto`
- `enableSubscription(string $subscription_id): bool`
- `disableSubscription(string $subscription_code): bool`
- `isValidPaystackWebhook($payload, $signature): bool`
- `getBankList(): ?Collection`
- `validateAccountNumber(string $account_number, string $bank_code): bool`
- `checkPTBalanceIsSufficient(int $amount): bool`
- `createTransferRecipient($name, $account_number, $bank_code): TransferRecipientDto`
- `initiateTransfer(string $amount, string $recipient_code, string $reference): TransferDto`

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.
