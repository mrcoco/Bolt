<?php

namespace Bolt\Http\Controllers;

use Auth;
use Bolt\Http\Controllers\BoltUploader as Uploader;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('avatar', ['only' => [
            'changeAvatar',
        ]]);

        $this->middleware('auth', ['only' => [
            'edit',
            'update',
        ]]);

        $this->middleware('userUpdate', ['only' => [
            'update',
        ]]);
    }

    public function changeAvatar(Request $request, Uploader $uploader)
    {
        $file = $request->file('file');

        $result = $uploader->uploadAvatar($file);

        $user = Auth::user();
        $user->avatar = $result['url'];
        $user->save();

        return redirect()->back();
    }

    public function edit()
    {
        $user = Auth::user();
        $title = 'Edit Your Profile';

        return view('user.edit', compact('user', 'title'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $user->update($request->all());

        return redirect('dashboard');
    }
}
