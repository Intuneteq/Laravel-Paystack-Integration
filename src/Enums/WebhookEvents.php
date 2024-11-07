<?php

namespace Intune\LaravelPaystack\Enums;

/**
 * 
 * 
 * @package Intune\LaravelPaystack\Enums
 */
enum WebhookEvents: string
{
   /**
    * 
    * 
    * @var string
    */
   case SUBSCRIPTION_CREATE = 'subscription.create';

   /**
    * 
    * 
    * @var string
    */
   case SUBSCRIPTION_NOT_RENEW = 'subscription.not_renew';

   /**
    * 
    * 
    * @var string
    */
   case SUBSCRIPTION_DISABLE = 'subscription.disable';

   /**
    * 
    * 
    * @var string
    */
   case SUBSCRIPTION_EXPIRING_CARDS = 'subscription.expiring_cards';

   /**
    * 
    * 
    * @var string
    */
   case CHARGE_SUCCESS = 'charge.success';

   /**
    * 
    * 
    * @var string
    */
   case INVOICE_CREATE = 'invoice.create';

   /**
    * 
    * 
    * @var string
    */
   case INVOICE_UPDATE = 'invoice.update';
   /**
    * 
    * 
    * @var string
    */
   case INVOICE_PAYMENT_FAILED = 'invoice.payment_failed';

   /**
    * 
    * 
    * @var string
    */
   case TRANSFER_SUCCESS = 'transfer.success';

   /**
    * 
    * 
    * @var string
    */
   case TRANSFER_FAILED = 'transfer.failed';

   /**
    * 
    * 
    * @var string
    */
   case TRANSFER_REVERSED = 'transfer.reversed';
}
