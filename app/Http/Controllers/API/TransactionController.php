<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Store;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    //melakukan pengecekan apakah jadwal boking tersedia atau tidak
    private function _checkBookingAvailability(Store $request)
    {
        $listing = Listing::find($request->listing_id);
        $runningTransactonCount = Transaction::whereListingId($listing->id)
            ->whereNot('status', 'canceled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($subquery) use ($request) {
                        $subquery->where('start_date', '<', $request->start_date)
                            ->where('end_date', '>', $request->end_date);
                    });
            });
        if ($runningTransactonCount >= $listing->max_person) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Booking is not available',
                'data' => null
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }
        return true;
    }
}
