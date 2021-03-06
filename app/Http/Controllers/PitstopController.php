<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pitstop;
use App\Http\Requests\PitstopRequest;
use App\Exports\PitstopExport;
use Excel;

class PitstopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', Pitstop::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'pitstops.time_in';
            $dir = $request->sort ? $request->sort[$sort] : 'desc';

            $pitstop = Pitstop::selectRaw('
                    pitstops.*,
                    units.name AS unit,
                    unit_categories.name AS unit_category,
                    locations.name AS location
                ')
                ->join('units', 'units.id', '=', 'pitstops.unit_id')
                ->join('unit_categories', 'unit_categories.id', '=', 'units.unit_category_id')
                ->join('locations', 'locations.id', '=', 'pitstops.location_id')
                ->when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('locations.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('units.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('unit_categories.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('pitstops.description', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $pitstop->perPage(),
                'total' => $pitstop->total(),
                'current' => $pitstop->currentPage(),
                'rows' => $pitstop->items(),
            ];
        }

        return view('pitstop.index', [
            'breadcrumbs' => [
                'plant/dashboard' => 'Plant',
                'pitstop' => 'Daily Check'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PitstopRequest $request)
    {
        $this->authorize('create', Pitstop::class);
        $input = $request->all();
        $input['user_id'] = auth()->user()->id;
        return Pitstop::create($input);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Pitstop $pitstop)
    {
        $this->authorize('view', Pitstop::class);
        return $pitstop;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PitstopRequest $request, Pitstop $pitstop)
    {
        $this->authorize('update', Pitstop::class);
        $pitstop->update($request->all());
        return $pitstop;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pitstop $pitstop)
    {
        $this->authorize('delete', Pitstop::class);
        return ['success' => $pitstop->delete()];
    }

    public function achievementDailyCheck()
    {
        $this->authorize('view', Pitstop::class);
        return [
            'plan' => \App\DailyCheckSetting::where('day', date('w'))->count(),
            'actual' => Pitstop::where('status', 1)->count()
        ];
    }

    public function export(Request $request)
    {
        $this->authorize('export', Pitstop::class);
        return Excel::download(new PitstopExport($request), "daily-check-{$request->from}-to-{$request->to}.xlsx");
    }
}
