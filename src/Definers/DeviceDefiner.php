<?php

declare(strict_types=1);

namespace ArtMksh\Agent\Detectors;

use ArtMksh\Agent\Contracts\Detector;
use DeviceDetector\DeviceDetector as BaseDetector;
use Illuminate\Http\Request;

class DeviceDetector implements Detector
{
    protected $detector;

    public function handle(Request $request): Detector
    {
        return $this->fromUserAgent(
            $request->server('HTTP_USER_AGENT')
        );
    }

    public function fromUserAgent(string $userAgent): Detector
    {
        $this->detector = tap(new BaseDetector($userAgent), function (BaseDetector $detector) {
            $detector->parse();
        });

        return $this;
    }

    public function osName(): string
    {
        return $this->getOs('name');
    }

    public function osShortName(): string
    {
        return $this->getOs('short_name');
    }

    public function osVersion(): string
    {
        return $this->getOs('version');
    }

    public function clientName()
    {
        return $this->getClient('name');
    }

    public function clientShortName()
    {
        return $this->getClient('short_name');
    }

    public function clientVersion(): string
    {
        return $this->getClient('version');
    }

    public function botName(): string
    {
        return $this->getBot()['name'] ?? BaseDetector::UNKNOWN;
    }

    public function isVisitor(): bool
    {
        return !$this->isBot();
    }

    public function isClientName(string $name): bool
    {
        return in_array($name, [
            $this->clientName(),
            $this->clientShortName(),
        ]);
    }

    public function isOsName(string $name): bool
    {
        return in_array($name, [
            $this->osName(),
            $this->osShortName(),
        ]);
    }

    public function __call(string $name, array $params)
    {
        return call_user_func_array([$this->detector, $name], $params);
    }
}