<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscriptionTypeId' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $subscriptionType = SubscriptionType::find($request->subscriptionTypeId);
        if (!$subscriptionType) {
            return response()->json(['error' => 'Missing subscription type'], 400);
        }
        $user = $request->user();
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'name' => $subscriptionType->name,
            'book_id' => $subscriptionType->book_id,
            'price' => $subscriptionType->price,
            'status' => 'pending',
            "start_time"=>date('Y-m-d h:i:s'),
            "end_time"=>date('Y-m-d h:i:s',time() + $subscriptionType->duration * 24 * 60 * 60),
            "confirmed"=>0
        ]);
        return response()->json(new SubscriptionResource($subscription));
    }

    public function accept(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->admin) {
            return response()->json(["error" => "Missing permissions"], 403);
        }
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return response()->json(['error' => 'Missing subscription'], 404);
        }
        if ($subscription->status != 'pending') {
            return response()->json(['error' => 'Subscription is in invalid status'], 400);
        }
        $subscription->update([
            'start_time' => time(),
            'end_time' => time() + $subscription->duration * 24 * 60 * 60,
            'status' => 'accepted'
        ]);
        return response()->json(new SubscriptionResource($subscription));
    }

    public function reject(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->admin) {
            return response()->json(["error" => "Missing permissions"], 403);
        }
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return response()->json(['error' => 'Missing subscription'], 404);
        }
        if ($subscription->status != 'pending') {
            return response()->json(['error' => 'Subscription is in invalid status'], 400);
        }
        $subscription->update([
            'status' => 'rejected'
        ]);
        return response()->json(new SubscriptionResource($subscription));
    }
}
