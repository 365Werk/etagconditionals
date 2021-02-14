<?php

namespace Tests\Feature;

use Tests\TestCase;

class SetEtagTest extends TestCase
{
    private string $response = "OK";

    protected function setUp():void
    {
        parent::setUp();

        \Route::middleware('setEtag')->any('/_test/set-etag', function () {
            return $this->response;
        });
    }

    /** @test */
    public function middleware_sets_etag_header()
    {
        $response = $this->get('/_test/set-etag');
        $response->assertHeader('ETag', $value = null);
    }

    /** @test */
    public function etag_header_has_correct_value()
    {
        $value = '"' . md5($this->response) . '"';
        $response = $this->get('/_test/set-etag');
        $response->assertHeader('ETag', $value);
    }
}
