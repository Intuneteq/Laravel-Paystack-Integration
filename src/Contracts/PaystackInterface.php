<?php

namespace Intune\LaravelPaystack\Contracts;

use Intune\LaravelPaystack\Dtos\UserDto;

interface PaystackInterface
{
   public function createCustomer(UserDto $user);
}
