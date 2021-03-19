<?php

namespace Werk365\EtagConditionals\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use Werk365\EtagConditionals\Middleware\IfNoneMatch;

class IfNoneMatchTest extends TestCase
{
    private string $response = 'OK';

    public function setUp(): void
    {
        parent::setUp();

        \Route::middleware(IfNoneMatch::class)->any('/_test/if-none-match', function () {
            return response($this->response, 200);
        });
    }

    /** @test */
    public function get_request_status_200_with_none_matching_IfNoneMatch()
    {
        $noneMatch = '"'.md5($this->response.'NoneMatch').'"';
        $response = $this->withHeaders([
            'If-None-Match' => $noneMatch,
        ])
        ->get('/_test/if-none-match');

        $response->assertStatus(200);
    }

    /** @test */
    public function get_request_status_304_with_matching_IfNoneMatch()
    {
        $noneMatch = '"'.md5($this->response).'"';
        $response = $this->withHeaders([
            'If-None-Match' => $noneMatch,
        ])
            ->get('/_test/if-none-match');

        $response->assertStatus(304);
    }

    /** @test */
    public function get_request_status_200_with_matching_weaktag_if_weak_is_disabled_in_config()
    {
        Config::set('etagconditionals.if_none_match_weak', false);
        $noneMatch = 'W/"'.md5($this->response).'"';
        $response = $this->withHeaders([
            'If-None-Match' => $noneMatch,
        ])
            ->get('/_test/if-none-match');

        $response->assertStatus(200);
    }

    /** @test */
    public function get_request_status_304_with_matching_weaktag_if_weak_is_enabled_in_config()
    {
        Config::set('etagconditionals.if_none_match_weak', true);
        $noneMatch = 'W/"'.md5($this->response).'"';
        $response = $this->withHeaders([
            'If-None-Match' => $noneMatch,
        ])
        ->get('/_test/if-none-match');

        $response->assertStatus(304);
    }
}
