<?php

namespace Werk365\EtagConditionals\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Werk365\EtagConditionals\Middleware\SetEtag;

class SetEtagTest extends TestCase
{
    private string $response = 'OK';

    public function setUp(): void
    {
        parent::setUp();

        Route::middleware(SetEtag::class)->any('/_test/set-etag', function () {
            return $this->response;
        });
    }

    #[Test]
    public function middleware_sets_etag_header()
    {
        $response = $this->get('/_test/set-etag');
        $response->assertHeader('ETag', $value = null);
    }

    #[Test]
    public function etag_header_has_correct_value()
    {
        $value = '"'.md5($this->response).'"';
        $response = $this->get('/_test/set-etag');
        $response->assertHeader('ETag', $value);
    }
}
