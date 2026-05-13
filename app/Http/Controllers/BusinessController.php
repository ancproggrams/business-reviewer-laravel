<?php

namespace App\Http\Controllers;

use App\Business;
use App\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessStoreRequest;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses =  $this->filterWithParams();

        if(request()->wantsJson()){
            return $businesses;
        }else{
            return view('business.index', compact('businesses'));
        }
    }

    public function create()
    {
        if (auth()->user()->business()->exists()) {
            return redirect(auth()->user()->business->path())
                ->with('status', 'Your account already has a business profile.');
        }

        $categories = Category::all();
        return view('business.create', compact('categories'));
    }

    public function store(BusinessStoreRequest $request)
    {
        $validated = $request->validated();

        $business = $this->storeBusiness($validated);

        $business->categories()->sync($validated['categories']);

        return redirect($business->path())
            ->with('status', 'Your business profile has been created.');
    }

    public function show(Business $business)
    {
        $session_key = 'business-'.$business->id;
        if(!Session::has($session_key)){
            $business->incrementViewCount();
            Session::put($session_key,Str::random(2));
        }

        $business->load(['categories', 'images', 'reviews.author.avatar', 'reviews.image', 'reviews.reply']);

        return view('business.show', compact('business'));
    }

    protected function filterWithParams()
    {
        $queryBuilder = Business::query();

        if (request()->filled('category')) {
            $category = Category::where('name', request()->category)->first();
            $queryBuilder = $category ? $category->businesses() : Business::query()->whereRaw('1 = 0');
        }

        if (request()->filled('search')) {
            $queryBuilder = $this->applySearch($queryBuilder, request()->search);
        }

        if (request()->filled('rated')) {
            $queryBuilder =  $queryBuilder->where('average_review', '=', request()->rated);
        }

        $allowedSorts = ['average_review', 'created_at', 'name'];
        if (request()->filled('orderBy') && in_array(request()->orderBy, $allowedSorts, true)) {
            $queryBuilder = $queryBuilder->orderBy(request()->orderBy, 'DESC');
        }

        if(request()->wantsJson()){
            $limit = min(max((int) request()->input('limit', request()->input('take', 100)), 1), 100);
            return $queryBuilder->with('categories')->limit($limit)->get();
        }else{
            return $queryBuilder->with('categories')->paginate(20)->appends(request()->query());
        }
    }

    protected function applySearch($queryBuilder, $searchQuery)
    {
        $searchQuery = trim($searchQuery);

        if ($searchQuery === '') {
            return $queryBuilder;
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            return $queryBuilder->whereRaw(
                'MATCH (name, country, city, slug, description) AGAINST (? IN BOOLEAN MODE)',
                [$this->mysqlBooleanSearchTerm($searchQuery)]
            );
        }

        if ($driver === 'pgsql') {
            return $queryBuilder->whereRaw(
                "to_tsvector('simple', coalesce(name, '') || ' ' || coalesce(country, '') || ' ' || coalesce(city, '') || ' ' || coalesce(slug, '') || ' ' || coalesce(description, '')) @@ plainto_tsquery('simple', ?)",
                [$searchQuery]
            );
        }

        return $queryBuilder->where(function ($query) use ($searchQuery) {
            $query
                ->orWhere('name', 'LIKE', "%{$searchQuery}%")
                ->orWhere('country', 'LIKE', "%{$searchQuery}%")
                ->orWhere('city', 'LIKE', "%{$searchQuery}%")
                ->orWhere('slug', 'LIKE', "%{$searchQuery}%")
                ->orWhere('description', 'LIKE', "%{$searchQuery}%");
        });
    }

    protected function mysqlBooleanSearchTerm($searchQuery)
    {
        $terms = preg_split('/\s+/', $searchQuery);
        $terms = array_filter(array_map(function ($term) {
            $term = preg_replace('/[^[:alnum:]_-]/u', '', $term);
            return $term ? '+' . $term . '*' : null;
        }, $terms));

        return $terms ? implode(' ', $terms) : $searchQuery;
    }

    protected function storeBusiness($request)
    {
        $imagePath = $request['front_image']->store('businesses');

        unset($request['categories']);
        unset($request['front_image']);

        $attributes['owner_id'] = auth()->id();
        $attributes['front_image'] = $imagePath;
        $attributes['slug'] = $this->generateUniqueSlug($request['name']);
        $attributes['geo_location'] = $request['geo_location'] ?? '';

        return Business::create(array_merge($request, $attributes));
    }

    protected function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);

        if (Business::where('slug', $slug)->exists()) {
            $slug = Str::random(5) . '-' . $slug;
        };

        return $slug;
    }
}
