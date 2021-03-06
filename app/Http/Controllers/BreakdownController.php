<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Breakdown;
use App\Http\Requests\BreakdownRequest;
use Carbon\Carbon;
use App\Exports\BreakdownExport;
use Excel;

class BreakdownController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', Breakdown::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'time_in';
            $dir = $request->sort ? $request->sort[$sort] : 'desc';

            $breakdown = Breakdown::selectRaw('
                    breakdowns.*,
                    units.name AS unit,
                    locations.name AS location,
                    CONCAT(breakdown_categories.name, " - ", breakdown_categories.description_en) AS breakdown_category,
                    breakdown_statuses.code AS breakdown_status,
                    unit_categories.name AS unit_category,
                    CONCAT(component_criterias.code, " - ", component_criterias.description) AS component_criteria
                ')
                ->join('units', 'units.id', '=', 'breakdowns.unit_id')
                ->join('unit_categories', 'unit_categories.id', '=', 'units.unit_category_id')
                ->join('locations', 'locations.id', '=', 'breakdowns.location_id')
                ->join('breakdown_categories', 'breakdown_categories.id', '=', 'breakdowns.breakdown_category_id')
                ->join('breakdown_statuses', 'breakdown_statuses.id', '=', 'breakdowns.breakdown_status_id', 'LEFT')
                ->join('component_criterias', 'component_criterias.id', '=', 'breakdowns.component_criteria_id', 'LEFT')
                ->when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('units.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('breakdowns.wo_number', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('unit_categories.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('locations.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('breakdown_categories.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('breakdown_statuses.code', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('component_criterias.code', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('component_criterias.description', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $breakdown->perPage(),
                'total' => $breakdown->total(),
                'current' => $breakdown->currentPage(),
                'rows' => $breakdown->items(),
            ];
        }

        return view('breakdown.index', [
            'breadcrumbs' => [
                'plant/dashboard' => 'Operation',
                'breakdown' => 'Breakdown OCR'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BreakdownRequest $request)
    {
        $this->authorize('create', Breakdown::class);
        $input = $request->all();
        $input['user_id'] = auth()->user()->id;
        $breakdown = Breakdown::create($input);
        $breakdown->unit->update(['status' => 0]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Breakdown $breakdown)
    {
        $this->authorize('view', Breakdown::class);
        return $breakdown;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BreakdownRequest $request, Breakdown $breakdown)
    {
        $this->authorize('update', Breakdown::class);
        $input = $request->all();

        if ($request->pcr == 1) {
            $input['update_pcr_by'] = auth()->user()->id;
            $input['update_pcr_time'] = Carbon::now();
        }

        if ($request->status == 1) {
            $input['time_close'] = Carbon::now();
        }

        $breakdown->update($input);
        $breakdown->unit->update(['status' => $breakdown->status]);

        if ($request->warning_part && !$breakdown->warningPart) {
            $breakdown->warningPart()->create();
        }

        return $breakdown;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Breakdown $breakdown)
    {
        $this->authorize('delete', Breakdown::class);
        $breakdown->unit->update(['status' => 1]);
        return ['success' => $breakdown->delete()];
    }

    public function getUnitReady()
    {
        return Breakdown::selectRaw('
                breakdowns.*,
                units.name AS unit
            ')
            ->join('units', 'units.id', '=', 'breakdowns.unit_id')
            ->where('breakdowns.status', 1)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();
    }

    public function export(Request $request)
    {
        $this->authorize('export', Breakdown::class);
        return Excel::download(new BreakdownExport($request), "breakdowns-{$request->from}-to-{$request->to}.xlsx");
    }
}
