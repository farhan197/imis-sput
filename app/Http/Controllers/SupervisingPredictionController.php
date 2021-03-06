<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SupervisingPrediction;
use App\Http\Requests\SupervisingPredictionRequest;

class SupervisingPredictionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', SupervisingPrediction::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'description';
            $dir = $request->sort ? $request->sort[$sort] : 'asc';

            $supervisingPrediction = SupervisingPrediction::when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('jam', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('description', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $supervisingPrediction->perPage(),
                'total' => $supervisingPrediction->total(),
                'current' => $supervisingPrediction->currentPage(),
                'rows' => $supervisingPrediction->items(),
            ];
        }

        return view('supervisingPrediction.index', [
            'breadcrumbs' => [
                'hcgs/dashboard' => 'HCGS',
                '#' => 'Master Data',
                'supervisingPrediction' => 'Supervising Prediction'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupervisingPredictionRequest $request)
    {
        $this->authorize('create', SupervisingPrediction::class);
        return SupervisingPrediction::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(SupervisingPrediction $supervisingPrediction)
    {
        $this->authorize('view', SupervisingPrediction::class);
        return $supervisingPrediction;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupervisingPredictionRequest $request, SupervisingPrediction $supervisingPrediction)
    {
        $this->authorize('update', SupervisingPrediction::class);
        $supervisingPrediction->update($request->all());
        return $supervisingPrediction;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupervisingPrediction $supervisingPrediction)
    {
        $this->authorize('delete', SupervisingPrediction::class);
        return ['success' => $supervisingPrediction->delete()];
    }
}
