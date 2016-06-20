<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Customer;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $customers = Customer::latest()->get();
        return $customers;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

        $attorney = Attorney::lists('name', 'id');
        return view('customers.create', compact('attorney'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $input_values=$request->all();
        Customer::create($input_values);
        return redirect('admin/customers')->with('success', Lang::get('message.success.create'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $Customer = Customer::findOrFail($id);
        return view('customers.show', compact('Customer'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $attorney = Attorney::lists('name', 'id');
        $Customer = Customer::findOrFail($id);
        return view('customers.edit', compact('Customer','attorney'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        //$this->validate($request, ['name' => 'required']); // Uncomment and modify if needed.
        $Customer = Customer::findOrFail($id);
        $input_values=$request->all();

        $Customer->update($input_values);
        return redirect('admin/customers')->with('success', Lang::get('message.success.update'));
    }
    /**
     * Delete confirmation for the given Customer.
     *
     * @param  int      $id
     * @return View
     */
    public function getModalDelete($id = null)
    {
        $error = '';
        $model = '';
        $confirm_route =  route('customers.delete',['id'=>$id]);
        return View('admin/layouts/modal_confirmation', compact('error','model', 'confirm_route'));
    }
    /**
     * Delete the given Customer.
     *
     * @param  int      $id
     * @return Redirect
     */
    public function getDelete($id = null)
    {
        $Customer = Customer::destroy($id);
        // Redirect to the group management page
        return redirect('admin/customers')->with('success', Lang::get('message.success.delete'));
    }
}
