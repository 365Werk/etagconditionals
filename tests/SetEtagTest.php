<?php

namespace Werk365\EtagConditionals\Tests;

use Orchestra\Testbench\TestCase;
use Werk365\EtagConditionals\Middleware\SetEtag;

class SetEtagTest extends TestCase
{
    private string $response = 'OK';

    public function setUp(): void
    {
        parent::setUp();

        \Route::middleware(SetEtag::class)->any('/_test/set-etag', function () {
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
        $value = '"'.md5($this->response).'"';
        $response = $this->get('/_test/set-etag');
        $response->assertHeader('ETag', $value);
    }
}
