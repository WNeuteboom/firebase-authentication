<?php

namespace WNeuteboom\FirebaseAuthentication;

use Auth;
use Firebase\Auth\Token\HttpKeyStore;
use Firebase\Auth\Token\Verifier;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class FirebaseAuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::viaRequest('firebase', function ($request) {
            return app(FirebaseGuard::class)->user($request);
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Verifier::class, function ($app) {
            $keyStore = new HttpKeyStore(null, cache()->store());

            return new Verifier(config('firebase.project_id', env('GOOGLE_CLOUD_PROJECT')), $keyStore);
        });
    }
}
