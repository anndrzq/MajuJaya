<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserDataController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('gender_label', function($row){
                    return $row->gender == 'L' ? 'Laki-laki' : 'Perempuan';
                })
                ->addColumn('action', function($row){
                    return '
                        <div class="d-flex gap-2">
                            <button onclick="viewUser(\''.$row->id.'\')" class="btn btn-info btn-sm">Detail</button>
                            <button onclick="editUser(\''.$row->id.'\')" class="btn btn-success btn-sm">Edit</button>
                            <button onclick="deleteUser(\''.$row->id.'\')" class="btn btn-danger btn-sm">Hapus</button>
                        </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('content.dashboard.masterData.UserData.index');
    }

    public function store(Request $request)
    {
        $id = $request->user_id;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($id ? ',' . $id : ''),
            'gender' => 'required|in:L,P',
        ];

        if (!$id) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        User::updateOrCreate(
            ['id' => $id ?: null],
            $validated
        );

        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }
}
