<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Http\Requests\UpdateFormRequest;
use App\Models\AllowedDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;
use function PHPUnit\Framework\isEmpty;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $forms = Form::query()->with('creator')->where('creator_id', Auth::user()->id)->get();
        $forms = Form::query()->where('creator_id', Auth::user()->id)->get();

        return response()->json([
            'message' => 'Get all forms success',
            'forms' => $forms
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
    public function store(Request $request)
    {
        $requestData = $request->all();
        $user = Auth::user();
        $validator = Validator::make($requestData, [
            'name' => 'required',
            'slug' => 'required|unique:forms|regex:/^[0-9a-zA-Z.-]+$/',
            'allowed_domains' => 'array',
        ]);
        
        if($validator->fails()){
            return response()->json(['message'=> 'Invalid field', 'errors' => $validator->errors()], 422);
        }

        $form = $request->all();
        $form['creator_id'] = $user->id;
        unset($form['allowed_domains']);

        $domain = explode('@', $user->email)[1];
        $sendForm = Form::create($form);
        AllowedDomain::create([
            'form_id' => $sendForm['id'],
            'domain'=> $domain
        ]);

        return response()->json([
            'message' => 'Create form success',
            'form' => $sendForm
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($form_slug)
    {
            $form = Form::query()->with('allowed_domains')->with('question')->with('creator')->where('slug', $form_slug)->first();

            $formDomain = $form->allowed_domains->domain ?? NULL;
            $userDomain = explode( "@", Auth::user()->email)[1];

            $allowedDomains = AllowedDomain::query()->where('domain', $formDomain)->first();

            if(!$form){
                return response()->json([
                    'message'=> 'Form not found',
                ], 404);
            }
            if($userDomain != $allowedDomains->domain){
                return response()->json([
                'message' => "Forbidden access",
                ], 403);
            }
                return response()->json([
                    'form' => $form,
                ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Form $form)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormRequest $request, Form $form)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form)
    {
        //
    }
}
