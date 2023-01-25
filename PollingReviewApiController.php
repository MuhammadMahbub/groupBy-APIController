<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PollingReview;
use App\Http\Controllers\Controller;
use App\Models\PollingQuestion;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PollingReviewApiController extends Controller
{
    public function store(Request $request)
    {

        $MAC = exec('getmac'); 
        $MAC = strtok($MAC, ' '); 

        $rules = array(
            'question_id' => 'required',
            'polling_option_id' => 'required',
        );

        $valiodator = Validator::make($request->all(), $rules);
        if($valiodator->fails()){
            return response()->json($valiodator->errors(),401);
        }else{
            $review = new PollingReview();

            // $review->user_id = 1;
            $review->user_id = Auth::id();
            $review->question_id = $request->question_id;
            $review->polling_option_id = $request->polling_option_id;
            $review->ip_address = $request->ip();
            $review->mac_id = $MAC;
            $review->save();
            $review_id = $review->id;

            return $this->abc($review_id);
        }
    }
    

    public function abc($id){
        $poll_que_id=PollingReview::findOrFail($id)->question_id;

        $aaa=PollingReview::where('question_id',$poll_que_id)->get();

        $result = DB::table('polling_reviews') 
            ->select(DB::raw('count(*) as count, polling_option_id'))
            ->where('question_id', '=', $poll_que_id)
            ->groupBy('polling_option_id')
            ->get();

            $cou = $aaa->count(); 

        return ["total_count" => $cou, "question_id" => $poll_que_id, "data" => $result ];

    }
}
