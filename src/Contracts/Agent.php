<?php

declare(strict_types=1);

namespace ArtMksh\Agent\Contracts;

use Illuminate\Http\Request;

interface Agent
{
    public function setRequest(Request $request);

    public function parse(Request $request = null): Agent;

    public function detector(string $key): Detector;
}