<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabasePreflightCheck extends Command
{
    protected $signature = 'db:preflight-constraints {--json : Output the full report as JSON}';

    protected $description = 'Report data issues that would block database constraints and indexes.';

    public function handle()
    {
        $report = [];
        $blockers = 0;

        foreach ($this->checks() as $check) {
            $rows = $this->rows($check['sql'], isset($check['bindings']) ? $check['bindings'] : []);
            $count = count($rows);

            $report[] = [
                'name' => $check['name'],
                'severity' => $check['severity'],
                'count' => $count,
                'rows' => $rows,
            ];

            if ($check['severity'] === 'blocker') {
                $blockers += $count;
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT));
            return $blockers > 0 ? 1 : 0;
        }

        $this->table(
            ['check', 'severity', 'count'],
            array_map(function ($item) {
                return [$item['name'], $item['severity'], $item['count']];
            }, $report)
        );

        foreach ($report as $item) {
            if ($item['count'] === 0) {
                continue;
            }

            $this->warn($item['name']);
            $rows = array_slice($item['rows'], 0, 25);
            $this->table(array_keys($rows[0]), $rows);

            if ($item['count'] > 25) {
                $this->line('Showing first 25 rows only.');
            }
        }

        if ($blockers > 0) {
            $this->error($blockers . ' blocking row(s) found. Clean these up before running constraint migrations.');
            return 1;
        }

        $this->info('No migration blockers found.');
        return 0;
    }

    protected function rows($sql, array $bindings = [])
    {
        return array_map(function ($row) {
            return (array) $row;
        }, DB::select($sql, $bindings));
    }

    protected function checks()
    {
        return [
            [
                'name' => 'duplicate categories.name',
                'severity' => 'blocker',
                'sql' => 'SELECT LOWER(name) AS normalized_name, COUNT(*) AS duplicate_count, MIN(id) AS first_id
                    FROM categories
                    GROUP BY LOWER(name)
                    HAVING COUNT(*) > 1',
            ],
            [
                'name' => 'multiple businesses per owner_id',
                'severity' => 'blocker',
                'sql' => 'SELECT owner_id, COUNT(*) AS business_count
                    FROM businesses
                    GROUP BY owner_id
                    HAVING COUNT(*) > 1',
            ],
            [
                'name' => 'duplicate reviews per business_id/user_id',
                'severity' => 'blocker',
                'sql' => 'SELECT business_id, user_id, COUNT(*) AS review_count
                    FROM reviews
                    GROUP BY business_id, user_id
                    HAVING COUNT(*) > 1',
            ],
            [
                'name' => 'duplicate review_reactions per review_id/user_id/type',
                'severity' => 'blocker',
                'sql' => 'SELECT review_id, user_id, type, COUNT(*) AS reaction_count
                    FROM review_reactions
                    GROUP BY review_id, user_id, type
                    HAVING COUNT(*) > 1',
            ],
            [
                'name' => 'replies.review_id without matching reviews.id',
                'severity' => 'blocker',
                'sql' => 'SELECT replies.id, replies.review_id
                    FROM replies
                    LEFT JOIN reviews ON reviews.id = replies.review_id
                    WHERE reviews.id IS NULL',
            ],
            [
                'name' => 'business_category orphan rows',
                'severity' => 'blocker',
                'sql' => 'SELECT business_category.id, business_category.business_id, business_category.category_id
                    FROM business_category
                    LEFT JOIN businesses ON businesses.id = business_category.business_id
                    LEFT JOIN categories ON categories.id = business_category.category_id
                    WHERE businesses.id IS NULL OR categories.id IS NULL',
            ],
            [
                'name' => 'reviews orphan rows',
                'severity' => 'blocker',
                'sql' => 'SELECT reviews.id, reviews.business_id, reviews.user_id
                    FROM reviews
                    LEFT JOIN businesses ON businesses.id = reviews.business_id
                    LEFT JOIN users ON users.id = reviews.user_id
                    WHERE businesses.id IS NULL OR users.id IS NULL',
            ],
            [
                'name' => 'review_reactions orphan rows',
                'severity' => 'blocker',
                'sql' => 'SELECT review_reactions.id, review_reactions.review_id, review_reactions.user_id
                    FROM review_reactions
                    LEFT JOIN reviews ON reviews.id = review_reactions.review_id
                    LEFT JOIN users ON users.id = review_reactions.user_id
                    WHERE reviews.id IS NULL OR users.id IS NULL',
            ],
            [
                'name' => 'images orphan rows',
                'severity' => 'blocker',
                'sql' => 'SELECT images.id, images.imageable_type, images.imageable_id
                    FROM images
                    LEFT JOIN businesses ON images.imageable_type = ? AND businesses.id = images.imageable_id
                    LEFT JOIN reviews ON images.imageable_type = ? AND reviews.id = images.imageable_id
                    LEFT JOIN users ON images.imageable_type = ? AND users.id = images.imageable_id
                    WHERE (images.imageable_type = ? AND businesses.id IS NULL)
                       OR (images.imageable_type = ? AND reviews.id IS NULL)
                       OR (images.imageable_type = ? AND users.id IS NULL)',
                'bindings' => ['App\\Business', 'App\\Review', 'App\\User', 'App\\Business', 'App\\Review', 'App\\User'],
            ],
            [
                'name' => 'images unsupported morph types',
                'severity' => 'blocker',
                'sql' => 'SELECT id, imageable_type, imageable_id
                    FROM images
                    WHERE imageable_type NOT IN (?, ?, ?)',
                'bindings' => ['App\\Business', 'App\\Review', 'App\\User'],
            ],
            [
                'name' => 'views orphan rows',
                'severity' => 'blocker',
                'sql' => 'SELECT views.id, views.viewable_type, views.viewable_id
                    FROM views
                    LEFT JOIN businesses ON views.viewable_type = ? AND businesses.id = views.viewable_id
                    LEFT JOIN reviews ON views.viewable_type = ? AND reviews.id = views.viewable_id
                    LEFT JOIN users ON views.viewable_type = ? AND users.id = views.viewable_id
                    WHERE (views.viewable_type = ? AND businesses.id IS NULL)
                       OR (views.viewable_type = ? AND reviews.id IS NULL)
                       OR (views.viewable_type = ? AND users.id IS NULL)',
                'bindings' => ['App\\Business', 'App\\Review', 'App\\User', 'App\\Business', 'App\\Review', 'App\\User'],
            ],
            [
                'name' => 'views unsupported morph types',
                'severity' => 'blocker',
                'sql' => 'SELECT id, viewable_type, viewable_id
                    FROM views
                    WHERE viewable_type NOT IN (?, ?, ?)',
                'bindings' => ['App\\Business', 'App\\Review', 'App\\User'],
            ],
            [
                'name' => 'invalid reviews.rating values',
                'severity' => 'blocker',
                'sql' => 'SELECT id, rating FROM reviews WHERE rating IS NULL OR rating < 1 OR rating > 5',
            ],
            [
                'name' => 'invalid businesses.average_review values',
                'severity' => 'blocker',
                'sql' => 'SELECT id, average_review FROM businesses WHERE average_review IS NULL OR average_review < 0 OR average_review > 5',
            ],
            [
                'name' => 'invalid users.type values',
                'severity' => 'blocker',
                'sql' => "SELECT id, type FROM users WHERE type IS NULL OR type NOT IN ('reviewer', 'business', 'admin')",
            ],
            [
                'name' => 'invalid review_reactions.type values',
                'severity' => 'blocker',
                'sql' => "SELECT id, type FROM review_reactions WHERE type IS NULL OR type NOT IN ('funny', 'useful')",
            ],
            [
                'name' => 'case-insensitive duplicate users.email',
                'severity' => 'blocker',
                'sql' => 'SELECT LOWER(email) AS normalized_email, COUNT(*) AS duplicate_count, MIN(id) AS first_id
                    FROM users
                    GROUP BY LOWER(email)
                    HAVING COUNT(*) > 1',
            ],
            [
                'name' => 'case-insensitive duplicate businesses.email',
                'severity' => 'warning',
                'sql' => 'SELECT LOWER(email) AS normalized_email, COUNT(*) AS duplicate_count, MIN(id) AS first_id
                    FROM businesses
                    GROUP BY LOWER(email)
                    HAVING COUNT(*) > 1',
            ],
        ];
    }
}
