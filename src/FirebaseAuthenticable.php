<?php

namespace WNeuteboom\FirebaseAuthentication;

use Illuminate\Support\Facades\Crypt;

trait FirebaseAuthenticable
{
    /**
     * The claims decoded from the JWT token.
     *
     * @var array
     */
    protected $claims;

    /**
     * Get User by claim.
     *
     * @param array $claims
     *
     * @return self
     */
    public function resolveByClaims(array $claims): object
    {
        $firebaseId = (string) $claims['sub'];

        $attributes = $this->transformClaims($claims);

        return $this->updateOrCreateUser($firebaseId, $attributes);
    }

    /**
     * Update or create user.
     *
     * @param int|string $firebaseId
     * @param array      $attributes
     *
     * @return self
     */
    public function updateOrCreateUser($firebaseId, array $attributes): object
    {
        if ($user = $this->where($this->getAuthIdentifierName(), $firebaseId)->limit(1)->first()) {
            $user
                ->fill($attributes);

            if ($user->isDirty()) {
                $user->save();
            }

            return $user;
        }

        $user = $this->fill($attributes);
        $user->{$this->getAuthIdentifierName()} = $firebaseId;
        $user->save();

        return $user;
    }

    /**
     * Transform claims to attributes.
     *
     * @param array $claims
     *
     * @return array
     */
    public function transformClaims(array $claims): array
    {
        $attributes = [
            'email' => (string) $claims['email'],
        ];

        if (!empty($claims['name'])) {
            $attributes['name'] = (string) $claims['name'];
        }

        if (!empty($claims['picture'])) {
            $attributes['picture'] = (string) $claims['picture'];
        }

        return $attributes;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->firebaseIdColumn ?? 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return Crypt::encryptString($this->getAuthIdentifier());
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        if (! empty($this->getRememberTokenName())) {
            return (string) $this->{$this->getRememberTokenName()};
        }
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        if (! empty($this->getRememberTokenName())) {
            $this->{$this->getRememberTokenName()} = $value;
        }
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->rememberTokenName;
    }
}
