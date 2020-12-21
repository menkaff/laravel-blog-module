<?php

namespace Modules\Blog\Tests\Unit\API\Admin;

use Laravel\Passport\Passport;
use Modules\Blog\Models\Page;
use Tests\TestCase;

class PageTest extends TestCase
{
    /** @test */
    public function can_admin_see_all_pages()
    {
        $api_admin_model = config('app.api_admin_model');
        $api_admin_model_name = config('app.api_admin_model_name');

        $api_admin_object = $api_admin_model::first();

        Passport::actingAs(
            $api_admin_object,
            [$api_admin_model_name],
            $api_admin_model_name
        );

        $response = $this->json('GET', '/api/blog/v1/admin/page');

        $response->assertStatus(200);

        $response->assertJson([
            'is_successful' => true,
        ]);
    }

    /** @test */
    public function can_admin_store_a_page()
    {
        $api_admin_model = config('app.api_admin_model');
        $api_admin_model_name = config('app.api_admin_model_name');

        $api_admin_object = $api_admin_model::first();

        Passport::actingAs(
            $api_admin_object,
            [$api_admin_model_name],
            $api_admin_model_name
        );

        $page_fake = factory(Page::class)->make()->toArray();

        $page_fake['user_id'] = $api_admin_object->id;
        $page_fake['user_table'] = $api_admin_object->getTable();

        $response = $this->json('POST', '/api/blog/v1/admin/page/store', $page_fake);

        $response->assertStatus(200);

        $response->assertJson([
            'is_successful' => true,
        ]);
    }
}
