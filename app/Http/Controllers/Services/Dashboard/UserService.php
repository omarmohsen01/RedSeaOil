<?php

namespace App\Http\Controllers\Services\Dashboard;
use App\Http\Controllers\Interfaces\Dashboard\UserServiceInterface;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface{
    protected $user;
    public function __construct(User $user)
    {
        $this->user=$user;
    }
    public function indexUser($data)
    {
        return $this->user->filter($data->query())->paginate(5);
    }
    public function userStore($data)
    {
        $this->user->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'type'=>$data->type
        ]);
    }
    public function userUpdate($data,$user)
    {
        $user->update($data->all());
    }
    public function userDestroy($id)
    {
        $this->user->destroy($id);
    }
}
