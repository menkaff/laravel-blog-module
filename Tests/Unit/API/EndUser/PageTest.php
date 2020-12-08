<?php

namespace Modules\Blog\Tests\Unit\API\EndUser;

use Laravel\Passport\Passport;
use Tests\TestCase;

class PageTest extends TestCase
{
    /** @test */
    public function can_end_user_see_all_pages()
    {
        $api_public_model = config('app.api_public_model');
        $api_public_model_name = config('app.api_public_model_name');

        $api_public_object = $api_public_model::first();

        Passport::actingAs(
            $api_public_object,
            [$api_public_model_name],
            $api_public_model_name
        );

        $response = $this->json('GET', '/api/blog/v1/end_user/page');

        $response->assertStatus(200);

        $response->assertJson([
            'is_successful' => true,
        ]);
    }
}
