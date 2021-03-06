<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StaffCategory;
use App\Http\Requests\StaffCategoryRequest;

class StaffCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', StaffCategory::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'name';
            $dir = $request->sort ? $request->sort[$sort] : 'asc';

            $staffCategory = StaffCategory::when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('description', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $staffCategory->perPage(),
                'total' => $staffCategory->total(),
                'current' => $staffCategory->currentPage(),
                'rows' => $staffCategory->items(),
            ];
        }

        return view('staffCategory.index', [
            'breadcrumbs' => [
                'hcgs/dashboard' => 'HCGS',
                '#' => 'Master Data',
                'staffCategory' => 'Staff Categories'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StaffCategoryRequest $request)
    {
        $this->authorize('create', StaffCategory::class);
        return StaffCategory::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(StaffCategory $staffCategory)
    {
        $this->authorize('view', StaffCategory::class);
        return $staffCategory;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StaffCategoryRequest $request, StaffCategory $staffCategory)
    {
        $this->authorize('update', StaffCategory::class);
        $staffCategory->update($request->all());
        return $staffCategory;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(StaffCategory $staffCategory)
    {
        $this->authorize('delete', StaffCategory::class);
        return ['success' => $staffCategory->delete()];
    }
}
