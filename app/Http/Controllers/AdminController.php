<?php

namespace App\Http\Controllers;

use PDF;

use Notification;

use App\Models\Order;

use App\Models\Product;

use App\Models\Cartegory;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Notifications\MyFirstNotification;




class AdminController extends Controller
{
    public function view_cartegory() {

        if(Auth::id()) {
            $data = Cartegory::all();
            return view('admin.cartegory', compact('data'));
        }else{
            return redirect('login');
        }

        
    }

    public function add_cartegory(Request $request) {

        if(Auth::id()) {
        $data = new cartegory;

        $data->cartegories_name = $request->cartegory;

        $data->save();

        return redirect()->back()->with('message', 'Cartegory Added Successfully');
        }else{
            return redirect('login');
        }
    }

    public function delete_cartegory($id) {

        if(Auth::id()) {
        $data = Cartegory::find($id);

        $data->delete();

        return redirect()->back()->with('message', 'Cartegory Deleted Successfully');
        }else{
        return redirect('login');
        }
    }

    public function view_product() {

        if(Auth::id()) {
        $cartegory = cartegory::all();
        return view('admin.product', compact('cartegory'));
        }else{
            return redirect('login');
        }
    }

    public function add_product(Request $request) {

        if(Auth::id()) {

        $product = new product;

        $product->title = $request->title;
        $product->description = $request->description;
        $product->cartegory = $request->cartegory;
        $product->quantity = $request->quantity;
        $product->price = $request->price;
        $product->discount_price = $request->dis_price;

        $image = $request->image;
        $imagename = time().'.'.$image->getClientOriginalExtension();
        $request->image->move('product', $imagename);
        $product->image = $imagename;

        $product->save();

        return redirect()->back()->with('message', 'Product Added Successfully');

        }else{
            return redirect('login');
        }
    }

    public function show_product() {

        if(Auth::id()) {

        $product = product::all();

        return view('admin.show_product', compact('product'));
        }else{
            return redirect('login');
        }
    }

    public function delete_product($id) {

        if(Auth::id()) {
        $product = product::find($id);
        $product->delete();
        return redirect()->back()->with('message','Product Deleted Successfully');
        }else{
            return redirect('login');
        }
    }

    public function update_product($id) {

        if(Auth::id()) {
        $product = product::find($id);

        $cartegory = cartegory::all();

        return view('admin.update_product', compact('product','cartegory'));
        }else{
            return redirect('login');
        }
    }

    public function update_product_confirm(Request $request, $id) {

        if(Auth::id()) {
        $product = product::find($id);

        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->discount_price = $request->dis_price;
        $product->cartegory = $request->cartegory;
        $product->quantity = $request->quantity;

        $image = $request->image;

        if($image) {
            $imagename = time().'.'.$image->getClientOriginalExtension();
            $request->image->move('product', $imagename);
            $product->image = $imagename;
        }

        $product->save();

        return redirect()->back()->with('message','Product Updated Successfully');
        }else{
            return redirect('login');
        }

    }

    public function order() {

        if(Auth::id()) {

        $order = order::all();

        return view('admin.order', compact('order'));
        }else{
            return redirect('login');
        }
    }

    public function delivered($id) {

        if(Auth::id()) {
        $order = order::find($id);

        $order->delivery_status = "Delivered";

        $order->payment_status = "Paid";

        $order->save();

        return redirect()->back();
        }else{
            return redirect('login');
        }

    }

    public function print_pdf($id) {

        if(Auth::id()) {

        $order = order::find($id);

        $pdf = PDF::loadview('admin.pdf', compact('order'));

        return $pdf->download('order_details.pdf');
        }else{
            return redirect('login');
        }

    }

    public function send_email($id) {
        if(Auth::id()) {
        $order = order::find($id);

        return view('admin.email_info', compact('order'));
        }else{
            return redirect('login');
        }
    }

    public function send_user_email(Request $request, $id) {

        if(Auth::id()) {
        $order = order::find($id);

        $details = [
            'greeting' => $request->greeting,
            'firstline' => $request->firstline,
            'body' => $request->body,
            'button' => $request->button,
            'url' => $request->url,
            'lastline' => $request->lastline
        ];

        Notification::send($order, new MyFirstNotification($details));

        return redirect()->back()->with('message','Email Sent Successfully');
        }else{
            return redirect('login');
        }
    }

    public function searchdata(Request $request) {
        if(Auth::id()) {
        $searchText = $request->search;

        $order = order::where('name', 'LIKE', "%$searchText%")->orWhere('phone', 'LIKE', "%$searchText%")
        ->orWhere('product_title', 'LIKE', "%$searchText%")
        ->get();

        return view('admin.order', compact('order'));
        }else{
            return redirect('login');
        }
    }
}
