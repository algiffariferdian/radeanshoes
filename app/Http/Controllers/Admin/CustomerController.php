<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::query()
            ->where('role', UserRole::Customer)
            ->withCount(['orders', 'addresses'])
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }
}
