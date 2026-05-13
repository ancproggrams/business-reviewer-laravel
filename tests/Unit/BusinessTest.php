<?php

namespace Tests\Unit;

use App\User;
use App\View;
use App\Review;
use App\Business;
use App\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusinessTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_it_has_an_owner()
    {
        $this->withoutExceptionHandling();
        $business = factory(Business::class)->create();
        $this->assertInstanceOf(User::class, $business->owner);
    }

    public function test_it_checks_if_authenticated_user_is_owner()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();

        $business = factory(Business::class)->create(['owner_id' => auth()->id()]);
        $this->assertTrue($user->ownerOf($business));
    }

    public function test_it_can_add_reviews()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $business = factory(Business::class)->create();

        $business->addReview('A review', 4);

        $this->assertDatabaseHas(
            'reviews',
            ['body' => 'A review']
        );
    }

    public function test_it_can_add_categories()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $business = factory(Business::class)->create();
        $category =  factory(Category::class)->create();

        $business->addCategory($category->id);

        $this->assertDatabaseHas('business_category', ['category_id' => $category->id, 'business_id' => $business->id]);
    }

    public function test_it_has_a_path()
    {
        $business = factory(Business::class)->create();

        $this->assertEquals($business->path(), '/businesses/' . $business->slug);
    }

    public function test_it_has_an_image_path()
    {
        $business = factory(Business::class)->create();

        $this->assertEquals($business->image(),   'storage/' . $business->front_image);
    }
    public function test_it_checks_if_a_user_already_reviewed()
    {
        $this->signIn();
        $business = factory(Business::class)->create();

        $this->assertFalse($business->reviewedAlready());

        $business->addReview('I am a review', 2);

        $this->assertTrue($business->reviewedAlready());
    }

    public function test_it_has_a_review_count()
    {
        $this->signIn();
        $business = factory(Business::class)->create();

        $this->assertEquals(0, $business->reviewCount());

        $business->addReview('The first review', 4, null, factory(User::class)->create()->id);

        $this->assertEquals(1, $business->reviewCount());

        $business->addReview('The second review', 3, null, factory(User::class)->create()->id);
        $business->addReview('The third review', 2, null, factory(User::class)->create()->id);

        $this->assertEquals(3, $business->reviewCount());
    }


    public function test_it_has_views()
    {
        $this->signIn();
        $business = factory(Business::class)->create();
        $business->incrementViewCount();

        $this->assertInstanceOf(View::class, $business->views[0]);
    }

    public function test_it_has_a_comment_count()
    {
        $this->signIn();
        $business = factory(Business::class)->create();

        $business->incrementViewCount();

        $this->assertEquals(1,  $business->viewCount());

        $business->incrementViewCount();
        $business->incrementViewCount();

        $this->assertEquals(3,  $business->fresh()->viewCount());
    }
}
