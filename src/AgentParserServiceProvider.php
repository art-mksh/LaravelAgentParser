<?php

declare(strict_types=1);

namespace ArtMksh\Agent;

use ArtMksh\Support\Providers\PackageServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class AgentServiceProvider extends PackageServiceProvider implements DeferrableProvider
{
    protected $package = 'agent';

    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->singleton(Contracts\Agent::class, Agent::class);
    }

    public function provides(): array
    {
        return [
            Contracts\Agent::class,
        ];
    }
}