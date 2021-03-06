<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComponentCriteria;
use App\Http\Requests\ComponentCriteriaRequest;

class ComponentCriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', ComponentCriteria::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'code';
            $dir = $request->sort ? $request->sort[$sort] : 'asc';

            $componentCriteria = ComponentCriteria::when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('code', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('description', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $componentCriteria->perPage(),
                'total' => $componentCriteria->total(),
                'current' => $componentCriteria->currentPage(),
                'rows' => $componentCriteria->items(),
            ];
        }

        return view('componentCriteria.index', [
            'breadcrumbs' => [
                'plant/dashboard' => 'Plant',
                '#' => 'Master Data',
                'componentCriteria' => 'Component Criteria'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ComponentCriteriaRequest $request)
    {
        $this->authorize('create', ComponentCriteria::class);
        return ComponentCriteria::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ComponentCriteria $componentCriteria)
    {
        $this->authorize('view', ComponentCriteria::class);
        return $componentCriteria;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ComponentCriteriaRequest $request, ComponentCriteria $componentCriteria)
    {
        $this->authorize('update', ComponentCriteria::class);
        $componentCriteria->update($request->all());
        return $componentCriteria;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComponentCriteria $componentCriteria)
    {
        $this->authorize('delete', ComponentCriteria::class);
        return ['success' => $componentCriteria->delete()];
    }
}
