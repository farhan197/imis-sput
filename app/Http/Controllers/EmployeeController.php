<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Exports\EmployeeExportQuery;
use App\Http\Requests\EmployeeRequest;
use Excel;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', Employee::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'name';
            $dir = $request->sort ? $request->sort[$sort] : 'ASC';

            $employee = Employee::selectRaw('
                    employees.*,
                    departments.name AS department,
                    offices.name AS office,
                    owners.name AS employer,
                    positions.name AS position,
                    dormitory_rooms.name AS room,
                    dormitories.name AS dormitory
                ')
                ->join('departments', 'departments.id', '=', 'employees.department_id')
                ->join('offices', 'offices.id', '=', 'employees.office_id', 'LEFT')
                ->join('owners', 'owners.id', '=', 'employees.owner_id', 'LEFT')
                ->join('positions', 'positions.id', '=', 'employees.position_id')
                ->join('dormitory_rooms', 'dormitory_rooms.id', '=', 'employees.dormitory_room_id', 'LEFT')
                ->join('dormitories', 'dormitories.id', '=', 'dormitory_rooms.dormitory_id', 'LEFT')
                ->when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('employees.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('employees.nrp', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('departments.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('offices.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('owners.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('positions.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('dormitories.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('dormitory_rooms.name', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $employee->perPage(),
                'total' => $employee->total(),
                'current' => $employee->currentPage(),
                'rows' => $employee->items(),
            ];
        }

        return view('employee.index', [
            'breadcrumbs' => [
                'hcgs/dashboard' => 'HCGS',
                '#' => 'Employee Management'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        $this->authorize('create', Employee::class);
        return Employee::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        $this->authorize('view', Employee::class);
        return $employee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        $this->authorize('update', Employee::class);
        $employee->update($request->all());
        return $employee;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $this->authorize('delete', Employee::class);
        return ['success' => $employee->delete()];
    }

    public function export(Request $request)
    {
        $this->authorize('export', Employee::class);
        return Excel::download(new EmployeeExportQuery($request), 'employees.xlsx');
    }

    public function generateNameTag(Employee $employee = null)
    {
        $this->authorize('export', Employee::class);

        $employees = $employee
            ? [$employee]
            : Employee::orderBy('name', 'ASC')->get();

        return view('employee.name_tag', ['employees' => $employees]);
    }
}
