<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreresponseRequest;
use App\Http\Requests\UpdateresponseRequest;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($form_slug)
    {
        $form = Form::query()->where('slug', $form_slug)->first();
        $userId = Auth::user()->id;
        $response = Response::query()->where('form_id', $form->id)->get();
    
        if($form->id != $userId){
            return response()->json([
                'message'=> 'Forbidden access'
            ], 403);
        }
        if(!$form){
            return response()->json([
                'message'=> 'Form not found',
            ], 404);
        }
        if(!$response){
            return response()->json([
                'message'=> 'There is no response yet',
            ], 404);
        }

        return response()->json([
            'message' => 'Get response success',
            'responses' => $response,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $form_slug)
    {
        $form = Form::query()->where('slug', $form_slug)->first();
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.value' => [function($att, $val, $fail){
                
            }]
        ]);
        if($validator->fails()){
            return response()->json([
                'message'=> "Invalid field",
                'error'=> $validator->errors()
            ], 422);
        }
        $userDomain = explode('@', $user->email);
        $formDomain = $form->allowed_domains->domain ?? NULL;

        if($userDomain[1] != $formDomain){
            return response()->json([
                'message' => "Forbidden access",
            ], 403);
        }


        $checkResponse = Response::query()->where('form_id', $form->id)->first()->form_id ?? NULL;
        if($form->limit_one_response == true && $checkResponse == $user->id){
            return response()->json([
                'message' => "You cannot submit form twice",
            ], 422);
        }
        
        $response = Response::create([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'date' => now(),
        ]);

        $answers = $request->answers;
        // $a =[];
        foreach($answers as $answer){
            $answer['response_id'] = $response->id;
            Answer::create($answer);
        }
        return response()->json([
            'message' => "Submit response success",
        ]); 
    }

    /**
     * Display the specified resource.
     */
    public function show(response $response)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(response $response)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateresponseRequest $request, response $response)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(response $response)
    {
        //
    }
}
