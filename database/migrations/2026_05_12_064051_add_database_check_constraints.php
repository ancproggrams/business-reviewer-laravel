<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDatabaseCheckConstraints extends Migration
{
    public function up()
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_rating_between_1_and_5_check CHECK (rating BETWEEN 1 AND 5)');
            DB::statement('ALTER TABLE businesses ADD CONSTRAINT businesses_average_review_between_0_and_5_check CHECK (average_review BETWEEN 0 AND 5)');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_type_allowed_check CHECK (`type` IN ('reviewer', 'business', 'admin'))");
            DB::statement("ALTER TABLE review_reactions ADD CONSTRAINT review_reactions_type_allowed_check CHECK (`type` IN ('funny', 'useful'))");
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_rating_between_1_and_5_check CHECK (rating BETWEEN 1 AND 5) NOT VALID');
            DB::statement('ALTER TABLE businesses ADD CONSTRAINT businesses_average_review_between_0_and_5_check CHECK (average_review BETWEEN 0 AND 5) NOT VALID');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_type_allowed_check CHECK (type IN ('reviewer', 'business', 'admin')) NOT VALID");
            DB::statement("ALTER TABLE review_reactions ADD CONSTRAINT review_reactions_type_allowed_check CHECK (type IN ('funny', 'useful')) NOT VALID");
            DB::statement('CREATE UNIQUE INDEX users_email_lower_unique ON users (LOWER(email))');
        }
    }

    public function down()
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE review_reactions DROP CHECK review_reactions_type_allowed_check');
            DB::statement('ALTER TABLE users DROP CHECK users_type_allowed_check');
            DB::statement('ALTER TABLE businesses DROP CHECK businesses_average_review_between_0_and_5_check');
            DB::statement('ALTER TABLE reviews DROP CHECK reviews_rating_between_1_and_5_check');
        }

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS users_email_lower_unique');
            DB::statement('ALTER TABLE review_reactions DROP CONSTRAINT IF EXISTS review_reactions_type_allowed_check');
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_type_allowed_check');
            DB::statement('ALTER TABLE businesses DROP CONSTRAINT IF EXISTS businesses_average_review_between_0_and_5_check');
            DB::statement('ALTER TABLE reviews DROP CONSTRAINT IF EXISTS reviews_rating_between_1_and_5_check');
        }
    }
}
