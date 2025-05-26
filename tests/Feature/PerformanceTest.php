<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class PerformanceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('migrate:fresh');
        
        User::factory()->count(10000)->create()->each(function ($user) {
            Post::factory()->count(10)->create(['user_id' => $user->id]);
        });
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('selectAll')]
    #[Group('selectAllEloquent')]
    public function testEloquentAllPerformance()
    {
        $start = microtime(true);
        $this->assertEquals(10000, User::all()->count());
        fwrite(STDERR, "\nEloquent all: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('selectAll')]
    #[Group('selectAllQueryBuilder')]
    public function testQuerybuilderAllPerformance()
    {
        $start = microtime(true);
        $this->assertEquals(10000, DB::table('users')->get()->count());
        fwrite(STDERR, "\nQuery Builder all: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('selectFilter')]
    #[Group('selectFilterEloquent')]
    public function testEloquentFilterPerformance()
    {
        $start = microtime(true);
        $this->assertGreaterThan(0, User::where('email', 'like', '%@example.com')->get()->count());
        fwrite(STDERR, "\nEloquent filter: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('selectFilter')]
    #[Group('selectFilterQueryBuilder')]
    public function testQuerybuilderFilterPerformance()
    {
        $start = microtime(true);
        $this->assertGreaterThan(0, DB::table('users')->where('email', 'like', '%@example.com')->get()->count());
        fwrite(STDERR, "\nQuery Builder filter: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('selectPaginate')]
    #[Group('selectPaginateEloquent')]
    public function testEloquentPaginatePerformance()
    {
        $start = microtime(true);
        $this->assertEquals(10000, User::orderBy('created_at', 'desc')->paginate(10000)->count());
        fwrite(STDERR, "\nEloquent paginate: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('selectPaginate')]
    #[Group('selectPaginateQueryBuilder')]
    public function testQuerybuilderPaginatePerformance()
    {
        $start = microtime(true);
        $this->assertEquals(10000, DB::table('users')->orderBy('created_at', 'desc')->paginate(10000)->count());
        fwrite(STDERR, "\nQuery Builder paginate: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('insert')]
    #[Group('insertEloquent')]
    public function testEloquentInsertPerformance()
    {
        $data = [];
        for ($i = 0; $i < 1000; $i++) {
            $data[] = [
                'name' => 'User ' . uniqid(),
                'email' => uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $start = microtime(true);
        foreach ($data as $userData) {
            User::create($userData);
        }
        $this->assertDatabaseHas('users', ['email' => $data[0]['email']]);
        fwrite(STDERR, "\nEloquent insert (1000): " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('insert')]
    #[Group('insertQueryBuilder')]
    public function testQuerybuilderInsertPerformance()
    {
        $data = [];
        for ($i = 0; $i < 1000; $i++) {
            $data[] = [
                'name' => 'User ' . uniqid(),
                'email' => uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $start = microtime(true);
        DB::table('users')->insert($data);
        $this->assertDatabaseHas('users', ['email' => $data[0]['email']]);
        fwrite(STDERR, "\nQuery Builder insert (1000): " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('update')]
    #[Group('updateEloquent')]
    public function testEloquentUpdatePerformance()
    {
        $start = microtime(true);
        $updated = User::where('email', 'like', '%@example.com')->update(['name' => 'Updated User']);
        $this->assertGreaterThan(0, $updated);
        fwrite(STDERR, "\nEloquent update: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('update')]
    #[Group('updateQueryBuilder')]
    public function testQuerybuilderUpdatePerformance()
    {
        $start = microtime(true);
        $updated = DB::table('users')->where('email', 'like', '%@example.com')->update(['name' => 'Updated User']);
        $this->assertGreaterThan(0, $updated);
        fwrite(STDERR, "\nQuery Builder update: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('delete')]
    #[Group('deleteEloquent')]
    public function testEloquentDeletePerformance()
    {
        $start = microtime(true);
        $deleted = User::where('email', 'like', '%@example.com')->delete();
        $this->assertGreaterThan(0, $deleted);
        fwrite(STDERR, "\nEloquent delete: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('delete')]
    #[Group('deleteQueryBuilder')]
    public function testQuerybuilderDeletePerformance()
    {
        $start = microtime(true);
        $deleted = DB::table('users')->where('email', 'like', '%@example.com')->delete();
        $this->assertGreaterThan(0, $deleted);
        fwrite(STDERR, "\nQuery Builder delete: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('lazyVsEagerLoading')]
    public function testLazyVsEagerLoadingPerformance()
    {
        // Lazy Loading
        $start = microtime(true);
        $users = User::all();

        foreach ($users as $user) {
            $user->posts->count();
        }

        $lazyTime = microtime(true) - $start;

        // Eager Loading
        $start = microtime(true);
        $users = User::with('posts')->get();

        foreach ($users as $user) {
            $user->posts->count();
        }
        
        $eagerTime = microtime(true) - $start;

        fwrite(STDERR, "\nLazy Loading: {$lazyTime} seconds\n");
        fwrite(STDERR, "Eager Loading: {$eagerTime} seconds\n");
        $this->assertTrue($eagerTime < $lazyTime);
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('joinAggregationQueryBuilder')]
    public function testQuerybuilderJoinAggregationPerformance()
    {
        $start = microtime(true);
        $result = DB::table('users')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->select('users.id', \Illuminate\Support\Facades\DB::raw('count(posts.id) as post_count'))
            ->groupBy('users.id')
            ->get();

        $this->assertEquals(10000, $result->count());
        fwrite(STDERR, "\nJoin + Aggregation: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('massInsertTransactionEloquent')]
    public function testMassInsertTransactionPerformance()
    {
        $user = User::first();
        $data = [];

        for ($i = 0; $i < 1000; $i++) {
            $data[] = [
                'user_id' => $user->id,
                'title' => 'Post ' . uniqid(),
                'body' => 'Body ' . uniqid(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $start = microtime(true);
        DB::transaction(function () use ($data) {
            Post::insert($data);
        });

        $this->assertDatabaseHas('posts', ['title' => $data[0]['title']]);
        fwrite(STDERR, "\nMass Insert Transaction: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('updateBatchVsIndividualEloquent')]
    public function testUpdateBatchVsIndividualPerformance()
    {
        $user = User::first();

        // Individual update
        $posts = $user->posts;
        $start = microtime(true);
        
        foreach ($posts as $post) {
            $post->update(['title' => 'Updated Title']);
        }

        $individualTime = microtime(true) - $start;

        // Batch update
        $posts = $user->posts;
        $start = microtime(true);
        Post::where('user_id', $user->id)->update(['title' => 'Batch Updated Title']);

        $batchTime = microtime(true) - $start;

        fwrite(STDERR, "\nUpdate Individual: {$individualTime} seconds\n");
        fwrite(STDERR, "Update Batch: {$batchTime} seconds\n");
        $this->assertTrue($batchTime < $individualTime);
    }
} 