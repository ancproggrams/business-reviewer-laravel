<?php

namespace App\Http\Controllers;

use App\Business;
use App\Category;
use Illuminate\Support\Str;
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
        return view('business.show', compact('business'));
    }

    protected function filterWithParams()
    {
        $queryBuilder = new Business;
        if (request()->category) {
            $queryBuilder = Category::where('name', request()->category)->first()->businesses();
        }
        if (request()->search){
            $search_query = request()->search;

            $queryBuilder = 
            $queryBuilder->where(function($query) use ($search_query) {
                    $query
                    ->orWhere('name','LIKE',"%{$search_query}%")
                    ->orWhere('country','LIKE',"%{$search_query}%")
                    ->orWhere('city','LIKE',"%{$search_query}%")
                    ->orWhere('slug','LIKE',"%{$search_query}%")
                    ->orWhere('description','LIKE',"%{$search_query}%");
            });
        }
        if (request()->rated) {
            $queryBuilder =  $queryBuilder->where('average_review', '=', request()->rated);
        }

        if (request()->orderBy) {
            $queryBuilder = $queryBuilder->orderBy(request()->orderBy, 'DESC');
        }
        if(request()->wantsJson()){
            return $queryBuilder->with('categories')->get();
        }else{
            return $queryBuilder->get();
        }
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
