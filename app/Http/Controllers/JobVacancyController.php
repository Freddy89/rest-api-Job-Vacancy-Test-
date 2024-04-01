<?php

namespace App\Http\Controllers;

use App\Models\CoinsHistory;
use App\Models\JobVacancy;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobVacancyController extends Controller
{
    protected $model;

    public function __construct(JobVacancy $model)
    {
        $this->model = $model;
    }
    public function index(Request $request)
    {
        $job_vacancies = $this->model
            ->filter($request->all())
            ->get();

        if (count($job_vacancies) == 0) {
            return response()->json([
                'status' => 'failed',
                'message' => 'No jobvacancy found!',
            ], 200);
        }

        $response = [
            'status' => 'success',
            'message' => 'JobVacancies are fetched successfully.',
            'query' => $this->model
                ->filter($request->all())
                ->toSql(),
            'filters' => $request->all(),
            'data' => $job_vacancies,
        ];

        return response()->json($response, 200);
    }

    public function filter(Request $request)
    {
        $vacancies = JobVacancy::latest()->get();

        if (is_null($vacancies->first())) {
            return response()->json([
                'status' => 'failed',
                'message' => 'No jobvacancy found!',
            ], 200);
        }

        $response = [
            'status' => 'success',
            'message' => 'JobVacancies are received successfully.',
            'data' => $vacancies,
        ];

        return response()->json($response, 200);
    }

    public function create(Request $request)
    {
        $user = User::find(auth()->id());
        $created_job_coins = Settings::where('param', 'create_job')->first();

        $validate = Validator::make($request->all(), [
            'title' => 'required|string|max:250',
            'description' => 'required|string|'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        if($this->getjobs_24hours(auth()->id()) >= 2){
            return response()->json([
                'status' => 'failed',
                'message' => "You cannot post more than two job vacancies per 24 hours!",
            ], 200);
        }

        if($user->coins < $created_job_coins->value){
            return response()->json([
                'status' => 'failed',
                'message' => "You don't have coins for this operation!",
                'data' => $user,
            ], 200);
        }

        $job_vacancy = JobVacancy::create(array_merge($request->all(), ['owner_id' => auth()->id()]));

        CoinsHistory::create([
            'user_id' => $user->id,
            'operation_type' => 'create_job',
            'balance_before' => $user->coins,
            'balance_after' => $user->coins - $created_job_coins->value,
        ]);

        $user->update(['coins' => $user->coins - $created_job_coins->value]);

        $response = [
            'status' => 'success',
            'message' => 'JobVacancy is added successfully.',
            'data' => $job_vacancy,
        ];

        return response()->json($response, 200);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $job_vacancy = JobVacancy::find($id);

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

        $job_vacancy->update($request->all());

        $response = [
            'status' => 'success',
            'message' => 'JobVacancy is updated successfully.',
            'data' => $job_vacancy,
        ];

        return response()->json($response, 200);
    }

    public function delete($id)
    {
        $job_vacancy = JobVacancy::find($id);

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

        JobVacancy::destroy($id);
        return response()->json([
            'status' => 'success',
            'message' => 'JobVacancy is deleted successfully.'
        ], 200);
    }

    public function getjobs_24hours(int $user_id): int
    {
        $jobs = JobVacancy::where('owner_id',$user_id)->where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())->get();

        return count($jobs);
    }
}
