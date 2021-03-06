<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jabatan;
use App\Http\Requests\JabatanRequest;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', Jabatan::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'name';
            $dir = $request->sort ? $request->sort[$sort] : 'asc';

            $jabatan = Jabatan::when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('description', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $jabatan->perPage(),
                'total' => $jabatan->total(),
                'current' => $jabatan->currentPage(),
                'rows' => $jabatan->items(),
            ];
        }

        return view('jabatan.index', [
            'breadcrumbs' => [
                'hcgs/dashboard' => 'HCGS',
                '#' => 'Master Data',
                'jabatan' => 'Jabatan'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JabatanRequest $request)
    {
        $this->authorize('create', Jabatan::class);
        return Jabatan::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Jabatan $jabatan)
    {
        $this->authorize('view', Jabatan::class);
        return $jabatan;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(JabatanRequest $request, Jabatan $jabatan)
    {
        $this->authorize('update', Jabatan::class);
        $jabatan->update($request->all());
        return $jabatan;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Jabatan $jabatan)
    {
        $this->authorize('delete', Jabatan::class);
        return ['success' => $jabatan->delete()];
    }
}
