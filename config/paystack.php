<?php

return [
   'secret' => env('PAYSTACK_SECRET_KEY'),
   'plan_code' => env('PAYSTACK_PREMIUM_PLAN_CODE'),
   'redirect_url' => env('PAYSTACK_REDIRECT_URL'),
   'white_list' => []
];
