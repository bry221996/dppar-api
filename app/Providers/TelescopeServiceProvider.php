<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::tag(function (IncomingEntry $entry) {
            return $entry->isRequest()
                ? [
                    'status:' . $entry->content['response_status'],
                    $entry->content['method'] . ' ' . $entry->content['uri']
                ]
                : [];
        });

        Telescope::filter(function (IncomingEntry $entry) {
            if ($entry->isRequest()) return $entry->content['uri'] !== '/';

            if ($this->app->environment('local') || $this->app->environment('development')) {
                return true;
            }

            return $entry->isReportableException() ||
                $entry->isFailedRequest() ||
                $entry->isFailedJob() ||
                $entry->isScheduledTask() ||
                $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails()
    {
        if ($this->app->environment('local') || $this->app->environment('development')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewTelescope', function ($user = null) {
            return true;
            return in_array($user->email, [
                //
            ]);
        });
    }
}
