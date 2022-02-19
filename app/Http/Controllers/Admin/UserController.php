<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Notifications\CredentialNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
            ])
            ->paginate($request->per_page ?? 10);

        return response($list);
    }

    public function store(UserRequest $request)
    {
        $generatedPassword = Str::random(8);

        $data = $request->validated();
        $data['password'] = Hash::make($generatedPassword);

        $user =  User::create($data);

        $user->notify(new CredentialNotification($generatedPassword));

        return response([
            'message' => 'User created successfully.',
            'data' => $user
        ]);
    }

    public function show(User $user)
    {
        return response([
            'message' => 'User fetched successfully.',
            'data' => $user
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->validated());

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
