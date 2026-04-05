<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user()->loadCount(['orders', 'addresses']);
        $recentOrders = $user->orders()->withCount('items')->latest()->take(3)->get();
        $addresses = $user->addresses()->latest()->take(3)->get();

        return view('web.account.profile', [
            'user' => $user,
            'recentOrders' => $recentOrders,
            'addresses' => $addresses,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $data['profile_photo_path'] = $path;
        }

        unset($data['photo']);

        if ($data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }
}
