<?php

declare(strict_types=1);

namespace ArtMksh\Agent;

use ArtMksh\Agent\Contracts\Agent as AgentContract;
use ArtMksh\Agent\Contracts\Detector;
use BadMethodCallException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class Agent implements AgentContract
{
    protected $app;

    protected $request;

    protected $parsed;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    protected function detectors(): array
    {
        return $this->app['config']['agent.detectors'];
    }

    protected function supportedDetectors(): array
    {
        return array_keys($this->detectors());
    }

    protected function getParsed(string $key): Detector
    {
        return $this->parsed[$key];
    }

    public function parse(Request $request = null): AgentContract
    {
        if ( ! is_null($request)) {
            $this->setRequest($request);
        }

        foreach ($this->supportedDetectors() as $detector) {
            $this->parsed[$detector] = $this->detector($detector)->handle($this->getRequest());
        }

        return $this;
    }

    public function detector(string $key): Detector
    {
        $detector = $this->detectors()[$key];

        return $this->app->make($detector['driver']);
    }

    protected function hasDetector(string $name): bool
    {
        return array_key_exists($name, $this->detectors());
    }

    public function __call($name, $params)
    {
        if ($this->hasDetector($name)) {
            return $this->getParsed($name);
        }

        throw new BadMethodCallException("Method [{$name}] not found");
    }
}