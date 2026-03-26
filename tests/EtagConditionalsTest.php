<?php

namespace Werk365\EtagConditionals\Tests;

use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Werk365\EtagConditionals\EtagConditionals;

final class EtagConditionalsTest extends TestCase
{
    private string $response = 'OK';

    protected function tearDown(): void
    {
        EtagConditionals::etagGenerateUsing(null);
    }

    #[Test]
    public function get_default_etag(): void
    {
        $request = Request::create('/', 'GET');
        $response = response($this->response, 200);

        $this->assertEquals('"e0aa021e21dddbd6d8cecec71e9cf564"', EtagConditionals::getEtag($request, $response));
    }

    #[Test]
    public function get_etag_with_callback_md5(): void
    {
        $request = Request::create('/', 'GET');
        $response = response($this->response, 200);

        EtagConditionals::etagGenerateUsing(function (Request $request, Response $response) {
            return md5($response->getContent());
        });

        $this->assertEquals('"e0aa021e21dddbd6d8cecec71e9cf564"', EtagConditionals::getEtag($request, $response));
    }

    #[Test]
    public function get_etag_with_callback_sophisticated(): void
    {
        $request = Request::create('/', 'GET');
        $response = response($this->response, 200);

        EtagConditionals::etagGenerateUsing(function (Request $request, Response $response) {
            return 'sophisticated';
        });

        $this->assertEquals('"sophisticated"', EtagConditionals::getEtag($request, $response));
    }

    #[Test]
    public function get_etag_with_callback_with_quotes(): void
    {
        $request = Request::create('/', 'GET');
        $response = response($this->response, 200);

        EtagConditionals::etagGenerateUsing(function (Request $request, Response $response) {
            return '"sophisticated"';
        });

        $this->assertEquals('"sophisticated"', EtagConditionals::getEtag($request, $response));
    }
}
