<?php

namespace Tests\Feature;

use Tests\TestCase;

class IfMatchTest extends TestCase
{
    private string $response = 'OK';

    protected function setUp(): void
    {
        parent::setUp();

        \Route::middleware('ifMatch')->any('/_test/if-match', function () {
            return response($this->response, 200);
        });
    }

    /** @test */
    public function patch_request_returns_200_if_matching_IfMatch()
    {
        $ifMatch = '"'.md5($this->response).'"';
        $response = $this->withHeaders([
            'If-Match' => $ifMatch,
        ])
        ->patch('/_test/if-match');

        $response->assertStatus(200);
    }

    /** @test */
    public function patch_request_returns_412_if_none_matching_IfMatch()
    {
        $ifMatch = '"'.md5($this->response.'ifMatch').'"';
        $response = $this->withHeaders([
            'If-Match' => $ifMatch,
        ])
            ->patch('/_test/if-match');

        $response->assertStatus(412);
    }
}
