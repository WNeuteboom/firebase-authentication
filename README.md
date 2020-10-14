# Firebase Authentication for Laravel

Firebase authentication API driver for Laravel/WNeuteboom.

## Overview

The driver contains a firebase guard that authenticates user by Firebase Authentication JWT token. To login use [Firebase Authentication](https://firebase.google.com/docs/auth/web/firebaseui).

## Installation

1) Install the package using composer:
```
composer require wneuteboom/firebase-authentication
```

2) Update config/auth.php.

```
'guards' => [
    'web' => [
        'driver' => 'firebase',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'token',
        'provider' => 'users',
    ],
],
```

3) Update your User model with `WNeuteboom\FirebaseAuthentication\FirebaseAuthenticable` trait

Eloquent example:
```
<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use WNeuteboom\FirebaseAuthentication\FirebaseAuthenticable;

class User extends Authenticatable
{
    use Notifiable, FirebaseAuthenticable;

    protected $firebaseIdColumn = "firebase_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firebase_id', 'name', 'email', 'picture'
    ];
}

```
Firequent example:
```
<?php

namespace App;

use WNeuteboom\FirebaseAuthentication\FirebaseAuthenticable;
use WNeuteboom\Firequent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Model implements Authenticatable
{
    use Notifiable, FirebaseAuthenticable;

    protected $firebaseIdColumn = "firebase_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'picture'
    ];

}

```

4. If you are using Eloquent you need to create or update migration for users table manually.
```
$table->string('firebase_id')->unique();
$table->string('name');
$table->string('email')->unique();
$table->string('picture');
$table->timestamps();
```

## Web guard

In order to use firebase authentication in web routes you must attach bearer token to each http request.

You can also store bearer token in `bearer_token` cookie variable and add to your `Kernel.php`:
```
    protected $middlewareGroups = [
        'web' => [
            ...
            \WNeuteboom\FirebaseAuthentication\Http\Middleware\AddAccessTokenFromCookie::class,
            ...
        ],

        ...
    ];
```

If you are using `EncryptCookies` middleware you must set:
```
    protected $except = [
        ...
        'bearer_token',
        ...
    ];
```

## Usage

Attach to each API call regular bearer token provided by Firebase Authentication.
