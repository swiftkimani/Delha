<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::all();
        return response()->json($members);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|string|unique:members',
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $member = Member::create($request->all());
        return response()->json($member, 201);
    }

    public function show($id)
    {
        $member = Member::find($id);
        
        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }
        
        return response()->json($member);
    }

    public function update(Request $request, $id)
    {
        $member = Member::find($id);
        
        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $member->update($request->all());
        return response()->json($member);
    }

    public function destroy($id)
    {
        $member = Member::find($id);
        
        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        $member->delete();
        return response()->json(['message' => 'Member deleted successfully']);
    }
}