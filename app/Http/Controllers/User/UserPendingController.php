<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\User;
use App\Models\Approved;
use App\Models\TrackingLog;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User_transaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class UserPendingController extends Controller
{
     public function userpendingtransaction(Request $request){

        $userId = Auth::id();

        // Kuhaa ang mga transactions nga ang destination kay ang ID sa aktibong user
        $transactions = Transaction::select('*')->where('destination', $userId)->where('status', 'pending')
        ->get();

        $users = User::select('*')->where('id', '!=', 1)->where('id', '!=', Auth::id())->get();

        // I-display o gamita ang mga transactions
   
      
        return view('User.Transaction.pending', compact('transactions', 'users'));
         
    }

    public function approveTransaction(Request $request){
      $id = $request->id;

      
        Transaction::where('id',$id)->update(array(
          'destination' => $request->to_destination
        ));

        Approved::insert(array(
          'transaction_id' => $id,
          'from_id' => Auth::id()
        ));

        TrackingLog::insert(array(
          'transaction_id' => $id,
          'from_id' => Auth::id(),
          'to_id' => $request->to_destination,
          'title' => 'Approval',
          'short_description' => Str::title(Auth::user()->name). ' approved and was sent to '. Str::title($request->to_name),
          'department' => $request->to_department,
          'updated_at' => Carbon::now()
        ));

        return response()->json([
          'status_code' => 1
        ]);
    
    }
}


