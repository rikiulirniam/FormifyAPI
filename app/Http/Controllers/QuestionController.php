<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mockery\Undefined;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        // $question = Question::query()->with()
        return response()->json([
            "slug" => $slug,
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
    {   $form = Form::query()->where('slug', $form_slug)->first();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'choice_type' => 'required|in:short answer,paragraph,date,multiple choice,dropdown,checkboxes',
            'choices' => 'required_if:choice_type,multiple choice,dropdown,checkboxes',
            'is_required' => "boolean",
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors(),
            ], 422);
        }
        if(!$form){
            return response()->json([
                'message' => 'Form not found'
            ], 404);
        }
        if($form->creator_id != Auth::user()->id){
            return response()->json([
                'message' => 'Forbidden access'
            ], 403);
        }
        $question = $validator->validate();
        if(isset($question['choices'])){
            $question['choices'] = implode( ",", $question['choices']);
        }
        
        $question['form_id'] = $form->id;
        if(!isset($request->is_required)){
            $question['is_required'] = true; 
        }

        $question = Question::query()->create($question);

        
    return response()->json([
        'message' => 'Add questions success',
        'question' => $question
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestionRequest $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($form_slug, $id)
    {
        $questionId = $id;
        $form = Form::query()->where('slug',$form_slug)->first();
        if(!$form){
            return response()->json([
                'message' => "Form not found",
            ], 404);
        }
        if($form->creator_id != Auth::user()->id){
            return response()->json([
                'message' => "Forbidden access",
            ], 403);
        }
        if(!Question::find($questionId)){
            return response()->json([
                "message" => "Question not found",
            ], 404);
        }
        Question::destroy($questionId);
        return response()->json([
            "message" => "Remove question success"
        ]);
    }
}
