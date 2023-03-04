<?php

namespace App\Http\Controllers;

use App\Http\Resources\PayoutResource;
use App\Payout;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PayoutResource::collection(Payout::all());
    }

    public function mark(Request $request) {

        $id = $request->payoutID;
        $status = $request->status;
        $details = $request->details;

        $payout = Payout::find($id);

        $payout->status = $status;

        $payout->details = $details;

        // decrement the artist funds (case: payed)
        if( $status === 'payed' ) {
            $payout->artist()->decrement('funds', $payout->amount);
        }

        $payout->save();

        return response()->json(200);
    }
}
