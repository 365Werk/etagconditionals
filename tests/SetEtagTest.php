<?php

namespace Werk365\EtagConditionals\Tests;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Werk365\EtagConditionals\Middleware\SetEtag;

final class SetEtagTest extends TestCase
{
    private string $response = 'OK';

    protected function setUp(): void
    {
        parent::setUp();

        \Route::middleware(SetEtag::class)->any('/_test/set-etag', function () {
            return $this->response;
        });
    }

    #[Test]
    public function middleware_sets_etag_header(): void
    {
        $response = $this->get('/_test/set-etag');
        $response->assertHeader('ETag', $value = null);
    }

    #[Test]
    public function etag_header_has_correct_value(): void
    {
        $value = '"'.md5($this->response).'"';
        $response = $this->get('/_test/set-etag');
        $response->assertHeader('ETag', $value);
    }
}
