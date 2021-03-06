<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Authorization;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', User::class);

        if ($request->ajax())
        {
            $pageSize = $request->rowCount > 0 ? $request->rowCount : 1000000;
            $request['page'] = $request->current;
            $sort = $request->sort ? key($request->sort) : 'users.name';
            $dir = $request->sort ? $request->sort[$sort] : 'asc';

            $users = User::selectRaw('users.*, customers.name AS customer, contractors.name AS contractor')
                ->join('customers', 'customers.id', '=', 'users.customer_id', 'LEFT')
                ->join('contractors', 'contractors.id', '=', 'users.contractor_id', 'LEFT')
                ->when($request->searchPhrase, function($query) use ($request) {
                    return $query->where('users.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('users.email', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('customers.name', 'LIKE', '%'.$request->searchPhrase.'%')
                        ->orWhere('contractors.name', 'LIKE', '%'.$request->searchPhrase.'%');
                })->orderBy($sort, $dir)->paginate($pageSize);

            return [
                'rowCount' => $users->perPage(),
                'total' => $users->total(),
                'current' => $users->currentPage(),
                'rows' => $users->items(),
            ];
        }
        return view('user.index', [
            'breadcrumbs' => [
                '#' => 'Administration',
                'user' => 'Users'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);
        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        $input['api_token'] = str_random(60);

        if ($request->customer_id) {
            $input['super_admin'] = 0;
            $input['contractor_id'] = null;
        }

        if ($request->contractor_id) {
            $input['super_admin'] = 0;
            $input['customer_id'] = null;
        }

        $user = User::create($input);
        $authorizations = [];

        foreach ($request->auth['controller'] as $i => $c)
        {
            $authorizations[] = [
                'user_id' => $user->id,
                'controller' => $c,
                'view' => $request->auth['view'][$i],
                'create' => $request->auth['create'][$i],
                'update' => $request->auth['update'][$i],
                'delete' => $request->auth['delete'][$i],
                'export' => $request->auth['export'][$i],
                'import' => $request->auth['import'][$i],
            ];
        }

        Authorization::insert($authorizations);
        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', User::class);
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', User::class);
        $input = $request->all();

        if ($request->password) {
            $input['password'] = bcrypt($request->password);
        }

        if ($request->customer_id) {
            $input['super_admin'] = 0;
        }

        $user->update($input);

        foreach ($request->auth['controller'] as $i => $c)
        {
            $authorization = [
                'view' => $request->auth['view'][$i],
                'create' => $request->auth['create'][$i],
                'update' => $request->auth['update'][$i],
                'delete' => $request->auth['delete'][$i],
                'export' => $request->auth['export'][$i],
                'import' => $request->auth['import'][$i],
            ];

            $exists = Authorization::where('user_id', $user->id)
                ->where('controller', $c)
                ->first();

            if ($exists) {
                $exists->update($authorization);
            }

            else {
                $authorization['user_id'] = $user->id;
                $authorization['controller'] = $c;
                Authorization::insert($authorization);
            }
        }

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', User::class);
        $user->authorizations()->delete();
        return ['success' => $user->delete()];
    }

    public function profile(Request $request)
    {
        $user = auth()->user();

        if ($request->update)
        {
            $input = $request->all();

            if ($request->password) {
                $input['password'] = bcrypt($request->password);
            }

            $user->update($input);
        }

        return view('user.profile', [
            'user' => $user
        ]);
    }

    public function getAuth(User $user)
    {
        return $user->authorizations;
    }
}
