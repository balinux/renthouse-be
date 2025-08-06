<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Store;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    // get all transaction
    public function index(Request $request){
        $transactions = Transaction::with('listing')->whereUserId(Auth::user()->id)->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Transaction list',
            'data' => $transactions
        ], JsonResponse::HTTP_OK);
    }

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
            })->count();
            
        if ($runningTransactonCount >= $listing->max_person) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Booking is not available',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }
        return true;
    }

    public function isAvailable(Store $request){
        $this->_checkBookingAvailability($request);
        return response()->json([
            'success' => true,
            'message' => 'Booking is available',
        ], JsonResponse::HTTP_OK);
    }

    public function store(Store $request){
        $this->_checkBookingAvailability($request);

        $transaction = Transaction::create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'listing_id' => $request->listing_id,
            'user_id' => Auth::user()->id,
        ]);

        $transaction->Listing;

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => $transaction
        ], JsonResponse::HTTP_OK);
    }

    public function show(Transaction $transaction) :JsonResponse{
        if ($transaction->user_id != Auth::user()->id) {
            return response()->json([
                'success'=> false,
                'message' => 'You are not authorized to access this transaction',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $transaction->Listing;

        return response()->json([
            'success'=> true,
            'message' => 'Transaction detail',
            'data' => $transaction
        ], JsonResponse::HTTP_OK);
    }
}
