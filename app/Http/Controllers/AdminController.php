<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
// use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    //
    public function index()
    {
        return view('admin.index');
    }

    // ============================ brand =================================
    public function brands()
    {
        $brands = Brand::OrderBy('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand()
    {
        return view('admin.brand_add');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => "required",
            'slug' => "required|unique:brands,slug",
            'image' => "mimes:png,jpg,jpeg|max:2048",
        ]);
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateBrandThumbailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully');
    }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand_edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => "required",
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => "mimes:png,jpg,jpeg|max:2048",
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBrandThumbailsImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully');
    }

    public function GenerateBrandThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspecRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function brand_delete($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully');
    }

    // ============================ Category =================================
    public function categories()
    {
        $categories = Category::OrderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    public function category_add()
    {
        return view('admin.category_add');
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name' => "required",
            'slug' => "required|unique:categories,slug",
            'image' => "mimes:png,jpg,jpeg|max:2048",
        ]);
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbailsImage($image, $file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been added successfully');
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category_edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => "required",
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => "mimes:png,jpg,jpeg|max:2048",
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
                File::delete(public_path('uploads/categories') . '/' . $category->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumbailsImage($image, $file_name);
            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully');
    }

    public function GenerateCategoryThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspecRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function category_delete($id)
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully');
    }

    // ============================ Product =================================
    public function products()
    {
        $products = Product::OrderBy('id', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function product_add()
    {
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        return view('admin.product_add', compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = [];
        $counter = 1;

        if ($request->hasFile('images')) {
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            $gallery_arr = []; // Initialize array to hold filenames
            $counter = 1; // Initialize counter

            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();

                if (in_array($gextension, $allowedfileExtion)) {
                    $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;

                    // Generate and save the thumbnail image
                    $this->GenerateProductThumbnailImage($file, $gfileName);

                    // Add the filename to the array
                    $gallery_arr[] = $gfileName;

                    // Increment the counter
                    $counter++;
                }
            }

            // Convert array of filenames to a comma-separated string
            $gallery_images = implode(',', $gallery_arr);

            // Add the log here to see the contents of $gallery_images
            Log::info('Gallery Images: ' . $gallery_images);

            // Save the filenames string to the database
            $product->images = $gallery_images;
            $product->save();
        }


        $product->images = $gallery_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully');
    }

    public function GenerateProductThumbnailImage($image, $imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnail');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());
        $img->cover(540, 689, "top");
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspecRatio();
        })->save($destinationPath . '/' . $imageName);

        $img->resize(540, 689, function ($constraint) {
            $constraint->aspecRatio();
        })->save($destinationPathThumbnail . '/' . $imageName);
    }

    public function product_edit($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        return view('admin.product_edit', compact('product', 'categories', 'brands'));
    }

    // public function product_update(Request $request)
    // {
    //     $request->validate([
    //         'name'=>'required',
    //         'slug'=>'required|unique:products,slug,' . $request->id,
    //         'short_description'=>'required',
    //         'description'=>'required',
    //         'regular_price'=>'required',
    //         'sale_price'=>'required',
    //         'SKU'=>'required',
    //         'stock_status'=>'required',
    //         'featured'=>'required',
    //         'quantity'=>'required',
    //         'image'=>'required|mimes:png,jpg,jpeg|max:2048',
    //         'category_id'=>'required',
    //         'brand_id'=>'required'
    //     ]);

    //     $product = Product::find($request->id);
    //     $product->name = $request->name;
    //     $product->slug = Str::slug($request->name);
    //     $product->short_description = $request->short_description;
    //     $product->description = $request->description;
    //     $product->regular_price = $request->regular_price;
    //     $product->sale_price = $request->sale_price;
    //     $product->SKU = $request->SKU;
    //     $product->stock_status = $request->stock_status;
    //     $product->featured = $request->featured;
    //     $product->quantity = $request->quantity;
    //     $product->category_id = $request->category_id;
    //     $product->brand_id = $request->brand_id;

    //     $current_timestamp = Carbon::now()->timestamp;

    //     if($request->hasFile('image'))
    //     {
    //         if(File::exists(public_path('uploads/product').'/'.$product->image))
    //         {
    //             File::delete(public_path('uploads/product').'/'.$product->image);
    //         }
    //         if(File::exists(public_path('uploads/product/thumbnail').'/'.$product->image))
    //         {
    //             File::delete(public_path('uploads/product/thumbnail').'/'.$product->image);
    //         }
    //         $image = $request->file('image');
    //         $imageName = $current_timestamp . '.' . $image->extension();
    //         $this->GenerateProductThumbnailImage($image, $imageName);
    //         $product->image = $imageName;
    //     }

    //     $gallery_arr = array();
    //     $gallery_images = [];
    //     $counter = 1;

    //     if ($request->hasFile('images')) {

    //         foreach(explode(',',$product->images) as $ofile)
    //         {
    //             if(File::exists(public_path('uploads/product').'/'.$ofile))
    //             {
    //                 File::delete(public_path('uploads/product').'/'.$ofile);
    //             }
    //             if(File::exists(public_path('uploads/product/thumbnail').'/'.$ofile))
    //             {
    //                 File::delete(public_path('uploads/product/thumbnail').'/'.$ofile);
    //             }
    //         }

    //         $allowedfileExtion = ['jpg', 'png', 'jpeg'];
    //         $files = $request->file('images');
    //         $gallery_arr = []; // Initialize array to hold filenames
    //         $counter = 1; // Initialize counter

    //         foreach ($files as $file) {
    //             $gextension = $file->getClientOriginalExtension();

    //             if (in_array($gextension, $allowedfileExtion)) {
    //                 $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;

    //                 $this->GenerateProductThumbnailImage($file, $gfileName);

    //                 $gallery_arr[] = $gfileName;

    //                 $counter++;
    //             }
    //         }

    //         $gallery_images = implode(',', $gallery_arr);
    //         Log::info('Gallery Images: ' . $gallery_images);

    //         $product->images = $gallery_images;
    //         $product->save();
    //     }

    //     $product->images = $gallery_images;
    //     $product->save();
    //     return redirect()->route('admin.products')->with('status', 'Product has been updated successfully');
    // }

    public function product_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        $product = Product::findOrFail($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                $oldImagePath = public_path('uploads/products/') . $product->image;
                $oldThumbnailPath = public_path('uploads/products/thumbnail/') . $product->image;

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                if (file_exists($oldThumbnailPath)) {
                    unlink($oldThumbnailPath);
                }
            }

            // Save new image
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = [];
        $gallery_images = [];
        $counter = 1;

        if ($request->hasFile('images')) {
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            $gallery_arr = []; // Initialize array to hold filenames
            $counter = 1; // Initialize counter

            // Delete old gallery images
            if ($product->images) {
                $oldImages = explode(',', $product->images);
                foreach ($oldImages as $oldImage) {
                    $oldImagePath = public_path('uploads/products/') . $oldImage;
                    $oldThumbnailPath = public_path('uploads/products/thumbnail/') . $oldImage;

                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    if (file_exists($oldThumbnailPath)) {
                        unlink($oldThumbnailPath);
                    }
                }
            }

            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();

                if (in_array($gextension, $allowedfileExtion)) {
                    $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;

                    // Generate and save the thumbnail image
                    $this->GenerateProductThumbnailImage($file, $gfileName);

                    // Add the filename to the array
                    $gallery_arr[] = $gfileName;

                    // Increment the counter
                    $counter++;
                }
            }

            // Convert array of filenames to a comma-separated string
            $gallery_images = implode(',', $gallery_arr);

            // Add the log here to see the contents of $gallery_images
            Log::info('Gallery Images: ' . $gallery_images);

            // Save the filenames string to the database
            $product->images = $gallery_images;
        }

        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully');
    }

    public function product_delete($id)
    {
        $product = Product::find($id);
        if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
            File::delete(public_path('uploads/products') . '/' . $product->image);
        }
        if (File::exists(public_path('uploads/product/thumbnail') . '/' . $product->image)) {
            File::delete(public_path('uploads/product/thumbnail') . '/' . $product->image);
        }

        foreach (explode(',', $product->images) as $ofile) {
            if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                File::delete(public_path('uploads/products') . '/' . $ofile);
            }
            if (File::exists(public_path('uploads/products/thumbnail') . '/' . $ofile)) {
                File::delete(public_path('uploads/products/thumbnail') . '/' . $ofile);
            }
        }

        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully');
    }

    // coupons
    public function coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }

    public function coupon_add()
    {
        return view('admin.coupon_add');
    }

    public function coupon_store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully!');
    }

    public function coupon_edit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon_edit', compact('coupon'));
    }

    public function coupon_update(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully!');
    }

    public function coupon_delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted successfully!');
    }

    public function orders()
    {
        $orders = Order::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::find($order_id);
        $orderitems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(10);
        $transaction = Transaction::where('order_id', $order_id)->first();

        return view("admin.order_details", compact('order', 'orderitems', 'transaction'));
    }

    public function update_order_status(Request $request){        
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;
        if($request->order_status=='delivered')
        {
            $order->delivered_date = Carbon::now();
        }
        else if($request->order_status=='canceled')
        {
            $order->canceled_date = Carbon::now();
        }        
        $order->save();
        if($request->order_status=='delivered')
        {
            $transaction = Transaction::where('order_id',$request->order_id)->first();
            $transaction->status = "approved";
            $transaction->save();
        }
        return back()->with("status", "Status changed successfully!");
    }
}
