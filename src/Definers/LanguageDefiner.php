<?php

declare(strict_types=1);

namespace ArtMksh\Agent\Detectors;

use ArtMksh\Agent\Contracts\Detector;
use Illuminate\Http\Request;

class LanguageDetector implements Detector
{
    protected $languages;

    public function languages(): array
    {
        return $this->languages;
    }

    public function keys(): array
    {
        return array_keys($this->languages());
    }

    public function handle(Request $request): Detector
    {
        return $this->fromAcceptLanguage(
            $request->server('HTTP_ACCEPT_LANGUAGE')
        );
    }

    public function fromAcceptLanguage(string $acceptLanguage): Detector
    {
        $this->languages = [];

        if (!empty($acceptLanguage)) {
            $this->parse($acceptLanguage);
        }

        return $this;
    }

    protected function parse(string $acceptLanguage): void
    {
        foreach (explode(',', $acceptLanguage) as $piece) {
            $parts = explode(';', $piece);
            $language = strtolower($parts[0]);
            $priority = empty($parts[1]) ? 1. : floatval(str_replace('q=', '', $parts[1]));
            $this->languages[$language] = $priority;
        }

        arsort($this->languages);
    }
}