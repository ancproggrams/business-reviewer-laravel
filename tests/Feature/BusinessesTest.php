<?php

namespace Tests\Feature;

use App\Business;
use App\Category;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Facades\Tests\Setup\BusinessFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusinessesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_a_user_can_add_a_business()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $image = $this->mockImageUpload();
        $business = factory('App\Business')->raw(['front_image' => $image]);

        $this
            ->followingRedirects()
            ->post(route('business.store'), array_merge($business, ['categories' => [1, 2]]))
            ->assertSee($business['name']);

        Storage::disk('testing_upload')->assertExists('businesses/' . $image->hashName());
    }

    public function test_business_generates_a_unique_slug_identifier()
    {
        $image = $this->mockImageUpload();

        $this->withoutExceptionHandling();
        $this->signIn();

        $attributes = ['name' => 'Super Bar And Spa', 'categories' => [1, 2], 'front_image' => $image];

        $this->post(route('business.store'), factory(Business::class)->raw($attributes));
        $this->post(route('business.store'), factory(Business::class)->raw($attributes));
        $this->post(route('business.store'), factory(Business::class)->raw($attributes));

        $this->assertDatabaseCount('businesses', 3);


        $businesses = Business::get();

        $this->assertNotEquals($businesses[0]->slug, $businesses[1]->slug, $businesses[2]->slug);
    }


    public function test_a_guest_can_see_business_list()
    {
        BusinessFactory::create();
        BusinessFactory::create();

        $this->getJson('/businesses')
            ->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_a_guest_can_view_business_and_reviews()
    {
        $this->withoutExceptionHandling();
        $business = BusinessFactory::withReviews(3)->withCategory('Hotel')->create();

        $this->get($business->path())
            ->assertSee($business->name, $business->categories[0]->name, $business->reviews[2]->body);

        $this->assertDatabaseCount('reviews', 3);
    }


    public function test_a_business_must_have_a_title()
    {
        $this->signIn();
        $attributes = factory('App\Business')->raw(['name' => '']);

        $this
            ->post('/businesses', $attributes)
            ->assertSessionHasErrors(['name']);
    }

    public function test_a_business_must_have_categories()
    {
        $this->signIn();
        $attributes = factory('App\Business')->raw();

        $attributes['categories'] = [];

        $this->post('/businesses', $attributes)
            ->assertSessionHasErrors(['categories']);
    }

    public function test_a_business_must_have_a_front_image()
    {
        $this->signIn();
        $attributes = factory('App\Business')->raw(['front_image' => '']);

        $this
            ->post('/businesses', $attributes)
            ->assertSessionHasErrors(['front_image']);
    }

    public function test_a_business_reflects_average_score_by_calculating_review_scores()
    {
        $business = factory('App\Business')->create(['average_review' => 0]);
        $this->signIn();
        $business->addReview('A good review', 5, null, factory('App\User')->create()->id);
        $business->addReview('Another good review', 5, null, factory('App\User')->create()->id);


        $this->assertEquals(5, $business->fresh()->average_review);

        $this->signIn();
        $business->addReview('Average review', 3, null, factory('App\User')->create()->id);
        $business->addReview('Bad review', 2, null, factory('App\User')->create()->id);
        $business->addReview('Very Bad review', 1, null, factory('App\User')->create()->id);


        $this->assertEquals(4, $business->fresh()->average_review);
    }
}
