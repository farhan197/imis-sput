<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jetty;
use App\StockArea;
use App\Hopper;
use App\Http\Requests\JettyRequest;
use DB;

class JettyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', Jetty::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'name';
            $dir = $request->sort ? $request->sort[$sort] : 'asc';

            $jetty = Jetty::when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('description', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $jetty->perPage(),
                'total' => $jetty->total(),
                'current' => $jetty->currentPage(),
                'rows' => $jetty->items(),
            ];
        }

        return view('jetty.index', [
            'breadcrumbs' => [
                'plant/dashboard' => 'Plant',
                '#' => 'Master Data',
                'jetty' => 'Jetty'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JettyRequest $request)
    {
        $this->authorize('create', Jetty::class);
        $jetty = Jetty::create($request->all());
        $jetty->hoppers()->createMany($request->hoppers);
        return $jetty;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Jetty $jetty)
    {
        $this->authorize('view', Jetty::class);
        return $jetty;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(JettyRequest $request, Jetty $jetty)
    {
        $this->authorize('update', Jetty::class);
        $jetty->update($request->all());

        foreach ($request->hoppers as $h)
        {
            if (isset($h['id'])) {
                Hopper::find($h['id'])->update($h);
            }

            else {
                $jetty->hoppers()->create($h);
            }
        }

        return $jetty;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Jetty $jetty)
    {
        $this->authorize('delete', Jetty::class);
        StockArea::where('jetty_id', $jetty->id)->delete();
        Hopper::where('jetty_id', $jetty->id)->delete();
        return ['success' => $jetty->delete()];
    }

    public function productivity(Request $request)
    {
        // 244	: JETTY-H
        // 245	: JETTY-J
        // 246	: JETTY-U
        // 247	: JETTY-K

        try {
            return DB::connection('beltscale')->select("SELECT * FROM DataLive");
        } catch (\Exception $e) {
            return [
                ['NodeId' => 244, 'Weight' => 0, 'TPH' => 0],
                ['NodeId' => 245, 'Weight' => 0, 'TPH' => 0],
                ['NodeId' => 246, 'Weight' => 0, 'TPH' => 0],
                ['NodeId' => 247, 'Weight' => 0, 'TPH' => 0]
            ];
        }
    }

    public function dwellingTime(Request $request)
    {
        return view('jetty.dwellingTime', [
            'breadcrumbs' => [
                'operation/dashboard' => 'Operation',
                '#' => 'Status Jetty',
                'jetty/dwellingTime' => 'Dwelling Time'
            ]
        ]);
    }

    public function stockSummary(Request $request)
    {
        $jetty = Jetty::find($request->jetty_id);

    }
}
