<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Controllers\Controller;
use App\Notifications\CredentialNotification;
use App\Http\Requests\Admin\User\CreateRequest;
use App\Http\Requests\Admin\User\UpdateRequest;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $list = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::exact('role'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('unit_id'),
                AllowedFilter::exact('sub_unit_id'),
                AllowedFilter::exact('station_id'),
                AllowedFilter::scope('search'),
            ])
            ->with(['offices:id,name', 'classifications:id,name'])
            ->paginate($request->per_page ?? 10);

        return response($list);
    }

    public function store(CreateRequest $request)
    {
        $generatedPassword = Str::random(8);

        $data = $request->userData();
        $data['password'] = Hash::make($generatedPassword);

        $user =  User::create($data);

        $user->classifications()->sync($request->classificationsData());
        $user->offices()->sync($request->officesData());

        $user->notify(new CredentialNotification($generatedPassword));

        return response([
            'message' => 'User created successfully.',
            'data' => $user
        ]);
    }

    public function show(User $user)
    {
        $user->loadMissing(['offices:id,name', 'classifications:id,name']);

        return response([
            'message' => 'User fetched successfully.',
            'data' => $user
        ]);
    }

    public function update(UpdateRequest $request, User $user)
    {
        $data = $request->userData();

        $user->update($data);

        $user->classifications()->sync($request->classificationsData());
        $user->offices()->sync($request->officesData());

        if ($user->fresh()->is_inactive) {
            $user->tokens()->delete();
        }

        return response([
            'message' => 'User updated successfully.',
            'data' => $user
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response([
            'message' => 'User updated successfully.',
            'data' => $user
        ]);
    }
}
