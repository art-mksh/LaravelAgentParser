<?php

declare(strict_types=1);

namespace ArtMksh\Agent\Contracts;

use Illuminate\Http\Request;

interface Detector
{
    public function handle(Request $request): Detector;
}