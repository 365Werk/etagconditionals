<?php

namespace Tests\Feature;

use Tests\TestCase;

class IfNoneMatchTest extends TestCase
{
    private string $response = 'OK';

    protected function setUp(): void
    {
        parent::setUp();

        \Route::middleware('ifNoneMatch')->any('/_test/if-none-match', function () {
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
}
