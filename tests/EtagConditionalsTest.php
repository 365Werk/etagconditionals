<?php

namespace Werk365\EtagConditionals\Tests;

use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Werk365\EtagConditionals\EtagConditionals;

class EtagConditionalsTest extends TestCase
{
    private string $response = 'OK';

    public function tearDown(): void
    {
        EtagConditionals::etagGenerateUsing(null);
    }

    /** @test */
    public function get_default_etag()
    {
        $request = Request::create('/', 'GET');
        $response = response($this->response, 200);

        $this->assertEquals('"e0aa021e21dddbd6d8cecec71e9cf564"', EtagConditionals::getEtag($request, $response));
    }

    /** @test */
    public function get_etag_with_callback_md5()
    {
        $request = Request::create('/', 'GET');
        $response = response($this->response, 200);

        EtagConditionals::etagGenerateUsing(function (Request $request, Response $response) {
            return md5($response->getContent());
        });

        $this->assertEquals('"e0aa021e21dddbd6d8cecec71e9cf564"', EtagConditionals::getEtag($request, $response));
    }

    /** @test */
    public function get_etag_with_callback_sophisticated()
    {
        $request = Request::create('/', 'GET');
        $response = response($this->response, 200);

        EtagConditionals::etagGenerateUsing(function (Request $request, Response $response) {
            return 'sophisticated';
        });

        $this->assertEquals('"sophisticated"', EtagConditionals::getEtag($request, $response));
    }

    /** @test */
    public function get_etag_with_callback_with_quotes()
    {
        $request = Request::create('/', 'GET');
        $response = response($this->response, 200);

        EtagConditionals::etagGenerateUsing(function (Request $request, Response $response) {
            return '"sophisticated"';
        });

        $this->assertEquals('"sophisticated"', EtagConditionals::getEtag($request, $response));
    }
}
