<?php

namespace Tests\Fakes;

class FakeTokenService
{
    public function create()
    {
        return (object)[
            'accessToken' => 'secret123'
        ];
    }
}
