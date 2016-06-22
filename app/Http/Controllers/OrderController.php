<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Order;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $orders = Order::latest()->get();
        return $orders;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
//    public function create()
//    {
//
////        $attorney = Attorney::lists('name', 'id');
//        return view('orders.create');
//    }
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $order=new Order($request->all());
        $order->save();

        return $order;
//        $input_values=$request->all();
//        dd($input_values);
//        Order::create($input_values);
//        return redirect('admin/orders')->with('success', Lang::get('message.success.create'));
    }
//    /**
//     * Display the specified resource.
//     *
//     * @param  int  $id
//     * @return Response
//     */
//    public function show($id)
//    {
//        $Order = Order::findOrFail($id);
//        return view('orders.show', compact('Order'));
//    }
//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param  int  $id
//     * @return Response
//     */
//    public function edit($id)
//    {
//        $attorney = Attorney::lists('name', 'id');
//        $Order = Order::findOrFail($id);
//        return view('orders.edit', compact('Order','attorney'));
//    }
//    /**
//     * Update the specified resource in storage.
//     *
//     * @param  int  $id
//     * @return Response
//     */
//    public function update($id, Request $request)
//    {
//        //$this->validate($request, ['name' => 'required']); // Uncomment and modify if needed.
//        $Order = Order::findOrFail($id);
//        $input_values=$request->all();
//
//        $Order->update($input_values);
//        return redirect('admin/orders')->with('success', Lang::get('message.success.update'));
//    }
//    /**
//     * Delete confirmation for the given Order.
//     *
//     * @param  int      $id
//     * @return View
//     */
//    public function getModalDelete($id = null)
//    {
//        $error = '';
//        $model = '';
//        $confirm_route =  route('orders.delete',['id'=>$id]);
//        return View('admin/layouts/modal_confirmation', compact('error','model', 'confirm_route'));
//    }
//    /**
//     * Delete the given Order.
//     *
//     * @param  int      $id
//     * @return Redirect
//     */
//    public function getDelete($id = null)
//    {
//        $Order = Order::destroy($id);
//        // Redirect to the group management page
//        return redirect('admin/orders')->with('success', Lang::get('message.success.delete'));
//    }
}
