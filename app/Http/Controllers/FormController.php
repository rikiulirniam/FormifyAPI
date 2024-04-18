<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Models\AllowedDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:forms|regex:/^[a-zA-Z0-9.-]+$/',
            'allowed_domains' => 'array'
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Invalid token',
                'errors' => $validator->errors()
            ], 422);
        }

        $form = Form::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description ?? NULL,
            'limit_one_response' => $request->limit_one_response,
            'creator_id' => Auth::user()->id
        ]);

        foreach($request->allowed_domains as $domain){
            AllowedDomain::create([
                'form_id' => $form->id,
                'domain' => $domain
            ]);
        }

        return response()->json($form);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $form = Form::query()->with('questions')->where('slug', $slug)->first();
        if(!$form){
            return response()->json([
                'message' => 'Form not found'
            ], 404);
        }
        $user_domain = explode('@', Auth::user()->email)[1];
        $form_domain = AllowedDomain::query()->where('form_id', $form->id)->get();
        foreach($form_domain as $domain){
            // return response()->json($domain->domain);
            if($user_domain != $domain->domain){
                return response()->json(['message' => 'Forbidden access'], 403);
            };
        }
        // if($form->)
        return response()->json($form);
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
