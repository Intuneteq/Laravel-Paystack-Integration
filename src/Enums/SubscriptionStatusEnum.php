<?php

namespace Intune\LaravelPaystack\Enums;

/**
 * Enum representing various subscription statuses in Paystack.
 * 
 * This enum defines the possible statuses that a subscription can have within the Paystack system.
 * 
 * @package Intune\LaravelPaystack\Enums
 */
enum SubscriptionStatusEnum: string
{
    /**
     * Represents a subscription that is currently active.
     * 
     * @var string
     */
    case ACTIVE = 'active';

    /**
     * Represents a subscription that will not be renewed after its current cycle.
     * 
     * @var string
     */
    case NON_RENEWING = 'non-renewing';

    /**
     * Represents a subscription that requires attention (e.g., due to payment issues).
     * 
     * @var string
     */
    case ATTENTION = 'attention';

    /**
     * Represents a subscription that has been cancelled.
     * 
     * @var string
     */
    case CANCELLED = 'cancelled';

    /**
     * Represents a subscription that is pending activation or confirmation.
     * 
     * @var string
     */
    case PENDING = 'pending';
}
