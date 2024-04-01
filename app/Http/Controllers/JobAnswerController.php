<?php

namespace App\Http\Controllers;

use App\Models\CoinsHistory;
use App\Models\JobAnswer;
use App\Models\JobVacancy;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobAnswerController extends Controller
{
    public function create(Request $request, $id)
    {
        $user = User::find(auth()->id());
        $job_vacancy = JobVacancy::find($id);
        $job_answer = JobAnswer::where('job_id', $id)->where('user_id', auth()->id())->get();

        $answer_job_coins = Settings::where('param', 'answer_job')->first();

        $validate = Validator::make($request->all(), [
            'answer' => 'required',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        if (is_null($job_vacancy)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'JobVacancy is not found!',
            ], 200);
        }elseif($job_vacancy->owner_id == auth()->id()){
            return response()->json([
                'status' => 'failed',
                'message' => 'You can not answer for your vacancy!',
            ], 200);
        }elseif(count($job_answer) > 0){
            return response()->json([
                'status' => 'failed',
                'message' => 'You can not send two or more responses to the same job vacancy!',
            ], 200);
        }elseif($user->coins < $answer_job_coins->value){
            return response()->json([
                'status' => 'failed',
                'message' => "You don't have coins for this operation!",
                'data' => $user,
            ], 200);
        }

        $job_answer = JobAnswer::create([
            'job_id' => $id,
            'user_id' => auth()->id(),
            'answer' => $request->answer,
        ]);

        CoinsHistory::create([
            'user_id' => $user->id,
            'operation_type' => 'answer_job',
            'balance_before' => $user->coins,
            'balance_after' => $user->coins - $answer_job_coins->value,
        ]);

        $user->update(['coins' => $user->coins - $answer_job_coins->value]);
        $job_vacancy->update(['answers_count'=> DB::raw('answers_count+1')]);

        $response = [
            'status' => 'success',
            'message' => 'Response to the job vacancy is added successfully.',
            'data' => $job_answer,
        ];

        return response()->json($response, 200);
    }

    public function delete($job_id)
    {
        $job_vacancy = JobVacancy::find($job_id);

        if (is_null($job_vacancy)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'JobVacancy is not found!',
            ], 200);
        }elseif($job_vacancy->owner_id != auth()->id()){
            return response()->json([
                'status' => 'failed',
                'message' => 'You are not owner for this jobvacancy!',
            ], 200);
        }

        JobAnswer::where('job_id', $job_id)->delete();
        $job_vacancy->update(['answers_count'=> 0]);

        return response()->json([
            'status' => 'success',
            'message' => 'JobAnswers are deleted successfully.'
        ], 200);
    }
}
