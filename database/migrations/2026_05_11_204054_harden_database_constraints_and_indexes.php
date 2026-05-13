<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HardenDatabaseConstraintsAndIndexes extends Migration
{
    public function up()
    {
        $driver = DB::getDriverName();

        Schema::table('categories', function (Blueprint $table) {
            $table->unique('name', 'categories_name_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('type', 'users_type_index');
            $table->index(['country', 'city'], 'users_country_city_index');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->unique('owner_id', 'businesses_owner_id_unique');
            $table->index(['average_review', 'created_at'], 'businesses_average_review_created_at_index');
            $table->index(['country', 'city'], 'businesses_country_city_index');
        });

        if ($driver !== 'sqlite') {
            Schema::table('businesses', function (Blueprint $table) {
                $table->foreign('owner_id', 'businesses_owner_id_foreign')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }

        Schema::table('business_category', function (Blueprint $table) {
            $table->unique(['business_id', 'category_id'], 'business_category_business_id_category_id_unique');
            $table->index(['category_id', 'business_id'], 'business_category_category_id_business_id_index');
        });

        if ($driver !== 'sqlite') {
            Schema::table('business_category', function (Blueprint $table) {
                $table->foreign('business_id', 'business_category_business_id_foreign')
                    ->references('id')
                    ->on('businesses')
                    ->onDelete('cascade');
                $table->foreign('category_id', 'business_category_category_id_foreign')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('cascade');
            });
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(['business_id', 'user_id'], 'reviews_business_id_user_id_unique');
            $table->index(['business_id', 'showcased', 'created_at'], 'reviews_business_showcased_created_at_index');
            $table->index(['user_id', 'created_at'], 'reviews_user_id_created_at_index');
        });

        if ($driver !== 'sqlite') {
            Schema::table('reviews', function (Blueprint $table) {
                $table->foreign('business_id', 'reviews_business_id_foreign')
                    ->references('id')
                    ->on('businesses')
                    ->onDelete('cascade');
                $table->foreign('user_id', 'reviews_user_id_foreign')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }

        Schema::table('replies', function (Blueprint $table) {
            $table->unique('review_id', 'replies_review_id_unique');
            $table->index(['owner_id', 'created_at'], 'replies_owner_id_created_at_index');
        });

        if ($driver !== 'sqlite') {
            Schema::table('replies', function (Blueprint $table) {
                $table->dropForeign('replies_review_id_foreign');
                $table->foreign('review_id', 'replies_review_id_foreign')
                    ->references('id')
                    ->on('reviews')
                    ->onDelete('cascade');
            });
        }

        Schema::table('review_reactions', function (Blueprint $table) {
            $table->unique(['review_id', 'user_id', 'type'], 'review_reactions_review_user_type_unique');
            $table->index(['review_id', 'type'], 'review_reactions_review_id_type_index');
            $table->index(['user_id', 'created_at'], 'review_reactions_user_id_created_at_index');
        });

        if ($driver !== 'sqlite') {
            Schema::table('review_reactions', function (Blueprint $table) {
                $table->foreign('review_id', 'review_reactions_review_id_foreign')
                    ->references('id')
                    ->on('reviews')
                    ->onDelete('cascade');
                $table->foreign('user_id', 'review_reactions_user_id_foreign')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }

        Schema::table('images', function (Blueprint $table) {
            $table->index(['imageable_type', 'imageable_id', 'created_at'], 'images_imageable_created_at_index');
        });

        Schema::table('views', function (Blueprint $table) {
            $table->index(['viewable_type', 'viewable_id', 'created_at'], 'views_viewable_created_at_index');
        });

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE businesses ADD FULLTEXT businesses_search_fulltext (name, country, city, slug, description)');
        }

        if ($driver === 'pgsql') {
            DB::statement("CREATE INDEX businesses_search_tsvector_index ON businesses USING GIN (to_tsvector('simple', coalesce(name, '') || ' ' || coalesce(country, '') || ' ' || coalesce(city, '') || ' ' || coalesce(slug, '') || ' ' || coalesce(description, '')))");
        }
    }

    public function down()
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE businesses DROP INDEX businesses_search_fulltext');
        }

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS businesses_search_tsvector_index');
        }

        Schema::table('views', function (Blueprint $table) {
            $table->dropIndex('views_viewable_created_at_index');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex('images_imageable_created_at_index');
        });

        if ($driver !== 'sqlite') {
            Schema::table('review_reactions', function (Blueprint $table) {
                $table->dropForeign('review_reactions_user_id_foreign');
                $table->dropForeign('review_reactions_review_id_foreign');
            });
        }

        Schema::table('review_reactions', function (Blueprint $table) {
            $table->dropIndex('review_reactions_user_id_created_at_index');
            $table->dropIndex('review_reactions_review_id_type_index');
            $table->dropUnique('review_reactions_review_user_type_unique');
        });

        if ($driver !== 'sqlite') {
            Schema::table('replies', function (Blueprint $table) {
                $table->dropForeign('replies_review_id_foreign');
            });
        }

        Schema::table('replies', function (Blueprint $table) {
            $table->dropIndex('replies_owner_id_created_at_index');
            $table->dropUnique('replies_review_id_unique');
        });

        if ($driver !== 'sqlite') {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropForeign('reviews_user_id_foreign');
                $table->dropForeign('reviews_business_id_foreign');
            });
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_user_id_created_at_index');
            $table->dropIndex('reviews_business_showcased_created_at_index');
            $table->dropUnique('reviews_business_id_user_id_unique');
        });

        if ($driver !== 'sqlite') {
            Schema::table('business_category', function (Blueprint $table) {
                $table->dropForeign('business_category_category_id_foreign');
                $table->dropForeign('business_category_business_id_foreign');
            });
        }

        Schema::table('business_category', function (Blueprint $table) {
            $table->dropIndex('business_category_category_id_business_id_index');
            $table->dropUnique('business_category_business_id_category_id_unique');
        });

        if ($driver !== 'sqlite') {
            Schema::table('businesses', function (Blueprint $table) {
                $table->dropForeign('businesses_owner_id_foreign');
            });
        }

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex('businesses_country_city_index');
            $table->dropIndex('businesses_average_review_created_at_index');
            $table->dropUnique('businesses_owner_id_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_country_city_index');
            $table->dropIndex('users_type_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_name_unique');
        });
    }
}
