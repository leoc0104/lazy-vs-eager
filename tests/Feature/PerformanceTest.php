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
        
        User::factory()->count(100)->create()->each(function ($user) {
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
        $this->assertEquals(100, User::all()->count());
        fwrite(STDERR, "\nEloquent all: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('selectAll')]
    #[Group('selectAllQueryBuilder')]
    public function testQuerybuilderAllPerformance()
    {
        $start = microtime(true);
        $this->assertEquals(100, DB::table('users')->get()->count());
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
        $this->assertEquals(100, User::orderBy('created_at', 'desc')->paginate(100)->count());
        fwrite(STDERR, "\nEloquent paginate: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('selectPaginate')]
    #[Group('selectPaginateQueryBuilder')]
    public function testQuerybuilderPaginatePerformance()
    {
        $start = microtime(true);
        $this->assertEquals(100, DB::table('users')->orderBy('created_at', 'desc')->paginate(100)->count());
        fwrite(STDERR, "\nQuery Builder paginate: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('insert')]
    #[Group('insertEloquent')]
    public function testEloquentInsertPerformance()
    {
        $data = [];
        for ($i = 0; $i < 100; $i++) {
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
        fwrite(STDERR, "\nEloquent insert (100): " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('querybuilder')]
    #[Group('insert')]
    #[Group('insertQueryBuilder')]
    public function testQuerybuilderInsertPerformance()
    {
        $data = [];
        for ($i = 0; $i < 100; $i++) {
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
        fwrite(STDERR, "\nQuery Builder insert (100): " . (microtime(true) - $start) . " seconds\n");
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

        $this->assertEquals(100, $result->count());
        fwrite(STDERR, "\nQuery Builder Join + Aggregation: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('joinAggregationEloquent')]
    public function testEloquentJoinAggregationPerformance()
    {
        $start = microtime(true);
        $result = User::join('posts', 'users.id', '=', 'posts.user_id')
            ->select('users.id', DB::raw('count(posts.id) as post_count'))
            ->groupBy('users.id')
            ->get();

        $this->assertEquals(100, $result->count());
        fwrite(STDERR, "\nEloquent Join + Aggregation: " . (microtime(true) - $start) . " seconds\n");
    }

    #[Group('performance')]
    #[Group('eloquent')]
    #[Group('massInsertTransactionEloquent')]
    public function testMassInsertTransactionPerformance()
    {
        $user = User::first();
        $data = [];

        for ($i = 0; $i < 100; $i++) {
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
    #[Group('querybuilderVsSqlRaw')]
    public function testQueryBuilderVsSqlRawPerformance()
    {
        // Query Builder
        $start = microtime(true);
        $resultQueryBuilder = DB::table('users')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->select('users.id', DB::raw('count(posts.id) as post_count'))
            ->groupBy('users.id')
            ->get();
        $queryBuilderTime = microtime(true) - $start;

        // SQL Raw
        $start = microtime(true);
        $resultSqlRaw = DB::select('
            SELECT users.id, COUNT(posts.id) as post_count
            FROM users
            JOIN posts ON users.id = posts.user_id
            GROUP BY users.id
        ');
        $sqlRawTime = microtime(true) - $start;

        fwrite(STDERR, "\nQuery Builder: {$queryBuilderTime} seconds\n");
        fwrite(STDERR, "SQL Raw: {$sqlRawTime} seconds\n");
        $this->assertEquals(count($resultQueryBuilder), count($resultSqlRaw));
    }
} 