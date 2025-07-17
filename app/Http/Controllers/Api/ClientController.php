<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function search(Request $request)
    {
        // Untuk dropdown search
        $search = $request->get('search');
        $selected = $request->get('selected', []);

        if (!empty($selected)) {
            return Client::whereIn('id', (array) $selected)
                ->get(['id', 'name']);
        }

        return Client::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->limit(10)
            ->get(['id', 'name']);
    }
}
