<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class CustomUserProvider extends EloquentUserProvider implements UserProviderContract {

  public function validateCredentials(UserContract $user, array $credentials)
  {
    $plain = $credentials['password'];

    //return $this->normal->check($plain, $user->getAuthPassword());
    return ($plain == strval($user->getAuthPassword()) );
  }

}