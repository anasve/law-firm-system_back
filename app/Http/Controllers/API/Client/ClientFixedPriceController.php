<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use App\Models\FixedPrice;
use Illuminate\Http\Request;

class ClientFixedPriceController extends Controller
{
    /**
     * Get all active fixed prices
     */
    public function index()
    {
        $prices = FixedPrice::active()->latest()->get();

        return response()->json([
            'data' => $prices,
        ]);
    }
}

