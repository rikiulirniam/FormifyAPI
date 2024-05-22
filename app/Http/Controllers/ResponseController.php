<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Http\Requests\StoreResponseRequest;
use App\Http\Requests\UpdateResponseRequest;
use App\Http\Resources\GetResponse;
use App\Models\AllowedDomain;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\returnSelf;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($form_slug)
    {
        $form = Form::query()->where('slug', $form_slug)->first();
        $response = Response::query()
        ->with('user')
        ->with('answers')
        ->where('form_id', $form->id ?? NULL)
        ->get();
        
        if(!$form){
            return response()->json([
                'message' => 'Form not Found'
            ], 404);
        }
        if($form->creator_id != Auth::user()->id){
            return response()->json(['message' => 'Forbidden access'], 403);
        }
        // return response()->json([
        //     'message' => 'Get responses Success',
        //     'responses' => $response
        // ]);
        return response()->json(new GetResponse($response));
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
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer',
        ]);

        $qId = $request->input('question_id');
        $question = Question::query()->where('form_id')->where('id', $qId)->first();
        if(!$question || !$question->is_required){
            $validator2 = Validator::make($request->all(), [
                'answers.*.value' => 'nullable'
            ]);
        }
        $validator2 = Validator::make($request->all(), [
            'answers.*.value' => 'required'
        ]);


        if($validator->fails()){
            return response()->json(['message' => 'Invalid Fields', 'errors' => $validator->errors()]);
        }

        if($validator2->fails()) return response()->json(['message' => 'Invalid fields', 'errors' => $validator2->errors()]);

        $form = Form::query()->where('slug', $form_slug)->first();


        $user_domain = explode('@', Auth::user()->email)[1];
        $form_domain = AllowedDomain::query()->where('form_id', $form->id)->get();

        foreach($form_domain as $domain){
            if($user_domain != $domain->domain){
                return response()->json(['message' => 'Forbidden access'], 403);
            }
        }

        $isEverSubmit = Response::query()->where('user_id', Auth::user()->id)->where('form_id', $form->id)->first();

        if($form->limit_one_response && $isEverSubmit){
            return response()->json(['message' => 'You cannot submit form twice'], 422);
        }


        $response = Response::create([
            'form_id' => $form->id,
            'user_id' => Auth::user()->id,
            'date' => date(now()),
        ]);

        foreach($request->answers as $answer){
            Answer::create([
                'response_id' => $response->id,
                'question_id' => $answer['question_id'],
                'value' => $answer['value']
            ]);
        }




        return response()->json(['message' => 'Submit form success']);
        // return response()->json($isEverSubmit);
    }

    /**
     * Display the specified resource.
     */
    public function show(Response $response)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Response $response)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResponseRequest $request, Response $response)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Response $response)
    {
        //
    }
}
