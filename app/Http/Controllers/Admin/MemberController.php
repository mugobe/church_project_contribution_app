<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CapacityTier;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with(['user', 'capacityTier'])
            ->latest()
            ->paginate(20);

        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        $tiers = CapacityTier::where('is_active', true)->get();
        return view('admin.members.create', compact('tiers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string',
            'capacity_tier_id' => 'nullable|exists:capacity_tiers,id',
            'joined_date'      => 'nullable|date',
            'status'           => 'required|in:active,inactive,suspended',
        ]);

        DB::transaction(function () use ($request) {
            // Generate member number
            $lastMember = Member::latest('id')->first();
            $nextNumber = $lastMember ? (intval(substr($lastMember->member_number, 4)) + 1) : 1;
            $memberNumber = 'SDC-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Create user account
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make('password'), // default password
                'role'     => 'member',
            ]);

            // Create member profile
            Member::create([
                'user_id'          => $user->id,
                'capacity_tier_id' => $request->capacity_tier_id,
                'member_number'    => $memberNumber,
                'phone'            => $request->phone,
                'address'          => $request->address,
                'joined_date'      => $request->joined_date ?? now(),
                'status'           => $request->status,
            ]);
        });

        return redirect()->route('admin.members.index')
            ->with('success', 'Member created successfully. Default password is "password".');
    }

    public function show(Member $member)
    {
        $member->load(['user', 'capacityTier', 'allocations.project', 'contributions.project', 'pledges.project']);
        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $tiers = CapacityTier::where('is_active', true)->get();
        return view('admin.members.edit', compact('member', 'tiers'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $member->user_id,
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string',
            'capacity_tier_id' => 'nullable|exists:capacity_tiers,id',
            'joined_date'      => 'nullable|date',
            'status'           => 'required|in:active,inactive,suspended',
        ]);

        DB::transaction(function () use ($request, $member) {
            $member->user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);

            $member->update([
                'capacity_tier_id' => $request->capacity_tier_id,
                'phone'            => $request->phone,
                'address'          => $request->address,
                'joined_date'      => $request->joined_date,
                'status'           => $request->status,
            ]);
        });

        return redirect()->route('admin.members.index')
            ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('admin.members.index')
            ->with('success', 'Member removed.');
    }
}