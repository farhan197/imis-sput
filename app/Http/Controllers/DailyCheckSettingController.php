<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DailyCheckSetting;
use App\Http\Requests\DailyCheckSettingRequest;
use DB;

class DailyCheckSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', DailyCheckSetting::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'day';
            $dir = $request->sort ? $request->sort[$sort] : 'asc';

            $dailyCheckSetting = DailyCheckSetting::selectRaw('
                    daily_check_settings.*,
                    units.name AS unit
                ')
                ->join('units', 'units.id', '=', 'daily_check_settings.unit_id')
                ->when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('units.name', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $dailyCheckSetting->perPage(),
                'total' => $dailyCheckSetting->total(),
                'current' => $dailyCheckSetting->currentPage(),
                'rows' => $dailyCheckSetting->items(),
            ];
        }

        return view('dailyCheckSetting.index', [
            'breadcrumbs' => [
                'plant/dashboard' => 'Plant',
                '#' => 'Master Data',
                'dailyCheckSetting' => 'Daily Check Setting'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DailyCheckSettingRequest $request)
    {
        $this->authorize('create', DailyCheckSetting::class);
        $input = $request->all();
        $input['user_id'] = auth()->user()->id;
        return DailyCheckSetting::create($input);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DailyCheckSetting $dailyCheckSetting)
    {
        $this->authorize('view', DailyCheckSetting::class);
        return $dailyCheckSetting;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DailyCheckSettingRequest $request, DailyCheckSetting $dailyCheckSetting)
    {
        $this->authorize('update', DailyCheckSetting::class);
        $input = $request->all();
        $input['user_id'] = auth()->user()->id;
        $dailyCheckSetting->update($input);
        return $dailyCheckSetting;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DailyCheckSetting $dailyCheckSetting)
    {
        $this->authorize('delete', DailyCheckSetting::class);
        return ['success' => $dailyCheckSetting->delete()];
    }

    public function getData()
    {
        $this->authorize('view', DailyCheckSetting::class);

        $sql = "SELECT DISTINCT(day),
                (SELECT GROUP_CONCAT(units.name)
                    FROM daily_check_settings
                    JOIN units ON units.id = daily_check_settings.unit_id
                    WHERE daily_check_settings.day = d.day
                    ORDER BY units.name ASC) AS units
            FROM daily_check_settings d
            ORDER BY day ASC";

        return DB::select(DB::raw($sql));
    }

    public function unScheduled()
    {
        $this->authorize('view', DailyCheckSetting::class);

        $sql = "SELECT units.name AS unit
            FROM units
            WHERE id NOT IN (SELECT unit_id FROM daily_check_settings)";

        return DB::select(DB::raw($sql));
    }

    public function todayPlan()
    {
        $this->authorize('view', DailyCheckSetting::class);
        $sql = "SELECT u.*
            FROM units u
            JOIN daily_check_settings d ON d.unit_id = u.id
            WHERE d.day = :day";

        return DB::select(DB::raw($sql), ['day' => date('w')]);
    }

    public function tomorrowPlan()
    {
        $this->authorize('view', DailyCheckSetting::class);
        $sql = "SELECT u.*
            FROM units u
            JOIN daily_check_settings d ON d.unit_id = u.id
            WHERE d.day = :day";

        return DB::select(DB::raw($sql), ['day' => date('w') < 6 ? date('w') + 1 : 0]);
    }
}
