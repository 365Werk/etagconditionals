<?php

namespace Werk365\EtagConditionals\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Werk365\EtagConditionals\Middleware\IfMatch;

final class IfMatchTest extends TestCase
{
    private string $response = 'OK';

    protected function setUp(): void
    {
        parent::setUp();

        \Route::middleware(IfMatch::class)->any('/_test/if-match', function () {
            return response($this->response, 200);
        });
    }

    #[Test]
    public function patch_request_returns_200_if_matching_IfMatch(): void
    {
        $ifMatch = '"'.md5($this->response).'"';
        $response = $this->withHeaders([
            'If-Match' => $ifMatch,
        ])
        ->patch('/_test/if-match');

        $response->assertStatus(200);
    }

    #[Test]
    public function patch_request_returns_200_if_matching_IfMatch_in_list_of_etags(): void
    {
        $ifMatch = '"'.md5('first').'", "'.md5($this->response).'","'.md5('last').'"';
        $response = $this->withHeaders([
            'If-Match' => $ifMatch,
        ])
            ->patch('/_test/if-match');

        $response->assertStatus(200);
    }

    #[Test]
    public function patch_request_returns_200_if_wildcard_is_used(): void
    {
        $ifMatch = '"'.md5('first').'", "*","'.md5('last').'"';
        $response = $this->withHeaders([
            'If-Match' => $ifMatch,
        ])
            ->patch('/_test/if-match');

        $response->assertStatus(200);
    }

    #[Test]
    public function patch_request_returns_412_if_none_matching_IfMatch(): void
    {
        $ifMatch = '"'.md5($this->response.'ifMatch').'"';
        $response = $this->withHeaders([
            'If-Match' => $ifMatch,
        ])
            ->patch('/_test/if-match');

        $response->assertStatus(412);
    }

    #[Test]
    public function patch_request_returns_412_if_none_matching_IfMatch_in_list_of_etags(): void
    {
        $ifMatch = '"'.md5('first').'", "'.md5($this->response.'ifMatch').'","'.md5('last').'"';
        $response = $this->withHeaders([
            'If-Match' => $ifMatch,
        ])
            ->patch('/_test/if-match');

        $response->assertStatus(412);
    }

    #[Test]
    public function patch_request_returns_200_if_matching_weaktag_when_weak_is_enabled_in_config(): void
    {
        Config::set('etagconditionals.if_match_weak', true);
        $ifMatch = 'W/"'.md5($this->response).'"';
        $response = $this->withHeaders([
            'If-Match' => $ifMatch,
        ])
            ->patch('/_test/if-match');

        $response->assertStatus(200);
    }

    #[Test]
    public function patch_request_returns_412_if_matching_weaktag_when_weak_is_disabled_in_config(): void
    {
        Config::set('etagconditionals.if_match_weak', false);
        $ifMatch = 'W/"'.md5($this->response).'"';
        $response = $this->withHeaders([
            'If-Match' => $ifMatch,
        ])
            ->patch('/_test/if-match');

        $response->assertStatus(412);
    }
}
