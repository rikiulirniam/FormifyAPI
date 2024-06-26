<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'choice_type' => 'required|in:short answer,paragraph,date,multiple choice,dropdown,checkboxes',
            'choices' => 'required_if:choice_type,multiple choice,dropdown,checkboxes'
        ]);
        if($validator->fails()){
            return response()->json(['message' => 'Invalid fields', 'errors' => $validator->errors()], 422);
        }

        $form = Form::query()->where('slug', $slug)->first();
        if(!$form){
            return response()->json(['message' => 'Form Not found'], 404);
        }

        if($form->creator_id != Auth::user()->id){
            return response()->json([
                'message' => 'Forbidden access',
            ], 403);
        }
        $question = Question::create([
            'name' => $request->name,
            'choice_type' => $request->choice_type,
            'choices' => implode(',', $request->choices) ?? NULL,
            'form_id' => $form->id
        ]);

        return response()->json([
            'message' => 'Add question success',
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
    public function destroy(Request $request, $form_slug, $quest_slug)
    {
        $form = Form::query()->where('slug', $form_slug)->first();
        $question = Question::query()->where('form_id', $form->id ?? NULL)->where('id', $quest_slug)->first();

        if(!$form){
            return response()->json([
                'message' => 'Form not found'
            ], 404);
        }
        if(!$question){
            return response()->json([
                'message' => 'Question not found'
            ], 404);
        }

        if(Auth::user()->id != $form->creator_id){
            return response()->json([
                'message' => 'Forbidden access'
            ], 403);
        }

        $question->destroy($question->id);

        return response()->json([
            'message' => 'Remove question success'
        ]);
    }
}
