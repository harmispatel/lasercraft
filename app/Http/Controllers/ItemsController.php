<?php

namespace App\Http\Controllers;

use App\Models\{AdditionalLanguage,Category,CategoryProductTags,ItemImages,ItemPrice,ItemReview,Items,ItemsVisit,Languages,Option,Tags};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public function index($id="")
    {
        $data['tags'] = Tags::get();
        $data['options'] = Option::get();
        $data['categories'] = Category::where('parent_id',NULL)->get();

        if(!empty($id) || $id != '')
        {
            $data['cat_id'] = $id;
            $data['category'] = Category::where('id',$id)->first();
            $data['items'] = Items::with(['itemImages'])->where('category_id',$id)->orderBy('order_key')->get();
            $data['cat_tags'] = CategoryProductTags::join('tags','tags.id','category_product_tags.tag_id')->orderBy('tags.order')->where('category_id',$id)->get()->unique('tag_id');
        }
        else
        {
            $data['cat_id'] = '';
            $data['category'] = "All";
            $data['items'] = Items::with(['itemImages'])->orderBy('order_key')->get();
            $data['cat_tags'] = CategoryProductTags::join('tags','tags.id','category_product_tags.tag_id')->orderBy('tags.order')->get()->unique('tag_id');
        }

        return view('client.items.items',$data);
    }



    // Function for Store Newly Create Item
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required',
            'category'   => 'required',
        ]);

        // Language Settings
        $language_settings = clientLanguageSettings();
        $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

        // Language Details
        $language_detail = Languages::where('id',$primary_lang_id)->first();
        $lang_code = isset($language_detail->code) ? $language_detail->code : '';

        $item_name_key = $lang_code."_name";
        $item_description_key = $lang_code."_description";
        $item_price_label_key = $lang_code."_label";

        $max_item_order_key = Items::max('order_key');
        $item_order = (isset($max_item_order_key) && !empty($max_item_order_key)) ? ($max_item_order_key + 1) : 1;

        $category_id = $request->category;
        $type = $request->type;
        $name = $request->name;
        $description = $request->description;
        $discount_type = $request->discount_type;
        $discount = $request->discount;
        $is_new = isset($request->is_new) ? $request->is_new : 0;
        $pers_message = isset($request->pers_message) ? $request->pers_message : 0;
        $published = isset($request->published) ? $request->published : 0;
        $review_rating = isset($request->review_rating) ? $request->review_rating : 0;
        $day_special = isset($request->day_special) ? $request->day_special : 0;
        $options = (isset($request->options) && count($request->options) > 0) ? serialize($request->options) : '';
        $tags = isset($request->tags) ? $request->tags : [];


        $price_array['price'] = isset($request->price['price']) ? array_filter($request->price['price']) : [];
        $price_array['label'] = isset($request->price['label']) ? $request->price['label'] : [];

        if(count($price_array['price']) > 0)
        {
            $price = $price_array;
        }
        else
        {
            $price = [];
        }


        try
        {
            $item = new Items();
            $item->category_id = $category_id;
            $item->type = $type;

            $item->name = $name;
            $item->description = $description;

            $item->$item_name_key = $name;
            $item->$item_description_key = $description;

            $item->discount_type = $discount_type;
            $item->discount = $discount;
            $item->published = $published;
            $item->order_key = $item_order;
            $item->options = $options;
            $item->is_new = $is_new;
            $item->pers_message = $pers_message;
            $item->review = $review_rating;
            $item->day_special = $day_special;

            $item->save();

            // Multiple Images
            $all_images = (isset($request->og_image)) ? $request->og_image : [];
            if(count($all_images) > 0)
            {
                foreach($all_images as $image)
                {
                    $image_token = genratetoken(10);
                    $og_image = $image;
                    $image_arr = explode(";base64,", $og_image);
                    $image_type_ext = explode("image/", $image_arr[0]);
                    $image_base64 = base64_decode($image_arr[1]);

                    $imgname = "item_".$image_token.".".$image_type_ext[1];
                    $img_path = public_path('client_uploads/items/'.$imgname);
                    file_put_contents($img_path,$image_base64);

                    // Insert Image
                    $new_img = new ItemImages();
                    $new_img->item_id = $item->id;
                    $new_img->image = $imgname;
                    $new_img->save();
                }
            }


            // Store Item Price
            if(count($price) > 0)
            {
                $price_arr = $price['price'];
                $label_arr = $price['label'];

                if(count($price_arr) > 0)
                {
                    foreach($price_arr as $key => $price_val)
                    {
                        $label_val = isset($label_arr[$key]) ? $label_arr[$key] : '';
                        $new_price = new ItemPrice();
                        $new_price->item_id = $item->id;
                        $new_price->price = $price_val;
                        $new_price->label = $label_val;
                        $new_price->$item_price_label_key = $label_val;
                        $new_price->save();
                    }
                }
            }


            // Insert & Update Tags
            if(count($tags) > 0)
            {
                foreach($tags as $val)
                {
                    $findTag = Tags::where($item_name_key,$val)->first();
                    $tag_id = (isset($findTag->id) && !empty($findTag->id)) ? $findTag->id : '';

                    if(!empty($tag_id) || $tag_id != '')
                    {
                        $tag = Tags::find($tag_id);
                        $tag->name = $val;
                        $tag->$item_name_key = $val;
                        $tag->update();
                    }
                    else
                    {
                        $max_order = Tags::max('order');
                        $order = (isset($max_order) && !empty($max_order)) ? ($max_order + 1) : 1;

                        $tag = new Tags();
                        $tag->name = $val;
                        $tag->$item_name_key = $val;
                        $tag->order = $order;
                        $tag->save();
                    }

                    if($tag->id)
                    {
                        $cat_pro_tag = new CategoryProductTags();
                        $cat_pro_tag->tag_id = $tag->id;
                        $cat_pro_tag->category_id = $category_id;
                        $cat_pro_tag->item_id = $item->id;
                        $cat_pro_tag->save();
                    }
                }
            }

            return response()->json([
                'success' => 1,
                'message' => "Item has been Inserted SuccessFully....",
            ]);
        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => "Internal Server Error!",
            ]);
        }

    }



    // Function for Delete Item
    public function destroy(Request $request)
    {
        try
        {
            $id = $request->id;

            $item = Items::where('id',$id)->first();
            $cat_id = isset($item->category_id) ? $item->category_id : '';

            // Delete Item Category Tags
            CategoryProductTags::where('item_id',$id)->where('category_id',$cat_id)->delete();

            // Delete Item Visits
            ItemsVisit::where('item_id',$id)->delete();

            // Delete Item Prices
            ItemPrice::where('item_id',$id)->delete();

            // Delete Item Reviews
            ItemReview::where('item_id',$id)->delete();

            // Delete Item
            Items::where('id',$id)->delete();

            // Delete Item Images
            $item_images = ItemImages::where('item_id',$id)->get();

            if(count($item_images) > 0)
            {
                foreach($item_images as $item_img)
                {
                    $image = (isset($item_img['image'])) ? $item_img['image'] : '';

                    if(!empty($image) && file_exists('public/client_uploads/items/'.$image))
                    {
                        unlink('public/client_uploads/items/'.$image);
                    }
                    ItemImages::where('id',$item_img['id'])->delete();
                }
            }

            return response()->json([
                'success' => 1,
                'message' => "Item has been Deleted SuccessFully....",
            ]);
        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => "Internal Server Error!",
            ]);
        }
    }



    // Function for Change Item Status
    public function status(Request $request)
    {
        try
        {
            $id = $request->id;
            $published = $request->status;

            $item = Items::find($id);
            $item->published = $published;
            $item->update();

            return response()->json([
                'success' => 1,
                'message' => "Item Status has been Changed Successfully..",
            ]);

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => "Internal Server Error!",
            ]);
        }
    }



    // Function for Filtered Items
    public function searchItems(Request $request)
    {
        $keyword = $request->keywords;
        $cat_id = $request->id;

        if(session()->has('lang_code'))
        {
            $curr_lang_code = session()->get('lang_code');
        }
        else
        {
            $curr_lang_code = 'en';
        }

        try
        {
            $name_key = $curr_lang_code."_name";
            if(!empty($cat_id))
            {
                $items = Items::where($name_key,'LIKE','%'.$keyword.'%')->where('category_id',$cat_id)->get();
            }
            else
            {
                $items = Items::where($name_key,'LIKE','%'.$keyword.'%')->get();
            }
            $html = '';

            if(count($items) > 0)
            {
                foreach($items as $item)
                {
                    $newStatus = ($item->published == 1) ? 0 : 1;
                    $checked = ($item->published == 1) ? 'checked' : '';

                    if(!empty($item->image) && file_exists('public/client_uploads/items/'.$item->image))
                    {
                        $image = asset('public/client_uploads/items/'.$item->image);
                    }
                    else
                    {
                        $image = asset('public/client_images/not-found/no_image_1.jpg');
                    }

                    $html .= '<div class="col-md-3">';
                        $html .= '<div class="item_box">';
                            $html .= '<div class="item_img">';
                                $html .= '<a><img src="'.$image.'" class="w-100"></a>';
                                $html .= '<div class="edit_item_bt">';
                                    $html .= '<button class="btn edit_category" onclick="editCategory('.$item->id.')">EDIT ITEM.</button>';
                                $html .= '</div>';
                                $html .= '<a class="delet_bt" onclick="deleteItem('.$item->id.')" style="cursor: pointer;"><i class="fa-solid fa-trash"></i></a>';
                                $html .= '<a class="cat_edit_bt" onclick="editItem('.$item->id.')">
                                <i class="fa-solid fa-edit"></i>
                            </a>';
                            $html .= '</div>';
                            $html .= '<div class="item_info">';
                                $html .= '<div class="item_name">';
                                    $html .= '<h3>'.$item->en_name.'</h3>';
                                    $html .= '<div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="status" role="switch" id="status" onclick="changeStatus('.$item->id.','.$newStatus.')" value="1" '.$checked.'></div>';
                                $html .= '</div>';
                                $html .= '<h2>Product</h2>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';


                }
            }

            $html .= '<div class="col-md-3">';
                $html .= '<div class="item_box">';
                    $html .= '<div class="item_img add_category">';
                        $html .= '<a data-bs-toggle="modal" data-bs-target="#addItemModal" class="add_category_bt" id="NewItemBtn"><i class="fa-solid fa-plus"></i></a>';
                    $html .= '</div>';
                    $html .= '<div class="item_info text-center"><h2>Product</h2></div>';
                $html .= '</div>';
            $html .= '</div>';

            return response()->json([
                'success' => 1,
                'message' => "Item has been retrived Successfully...",
                'data'    => $html,
            ]);

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => "Internal Server Error!",
            ]);
        }

    }



    // Function for Edit Item
    public function edit(Request $request)
    {
        $item_id = $request->id;

        try
        {
            // Item Details
            $item = Items::with(['itemImages'])->where('id',$item_id)->first();

            // Get all Parent Categories
            $parent_categories = Category::where('parent_id',NULL)->get();

            // Order Attributes
            $options = Option::get();

            // Tags
            $tags = Tags::get();

            // ModalName
            $modalName = "'editItemModal'";

            // Get Language Settings
            $language_settings = clientLanguageSettings();
            $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

            // Primary Language Details
            $primary_language_detail = Languages::where('id',$primary_lang_id)->first();
            $primary_lang_code = isset($primary_language_detail->code) ? $primary_language_detail->code : '';
            $item_name_key = $primary_lang_code."_name";
            $item_desc_key = $primary_lang_code."_description";
            $item_price_label_key = $primary_lang_code."_label";
            $option_title_key = $primary_lang_code."_title";

            // Item Details
            $item_type = (isset($item['type'])) ? $item['type'] : '';
            $category_id = (isset($item['category_id'])) ? $item['category_id'] : '';
            $item_name = (isset($item[$item_name_key])) ? $item[$item_name_key] : '';
            $item_desc = (isset($item[$item_desc_key])) ? $item[$item_desc_key] : '';
            $price_array = ItemPrice::where('item_id',$item['id'])->get();
            $item_cat_tags = CategoryProductTags::with(['hasOneTag'])->where('item_id',$item['id'])->where('category_id',$item['category_id'])->get();
            $item_options = (isset($item['options']) && !empty($item['options'])) ? unserialize($item['options']) : [];
            $item_published = (isset($item['published']) && $item['published'] == 1) ? 'checked' : '';
            $review_rating = (isset($item['review']) && $item['review'] == 1) ? 'checked' : '';
            $item_is_new = (isset($item['is_new']) && $item['is_new'] == 1) ? 'checked' : '';
            $item_pers_message = (isset($item['pers_message']) && $item['pers_message'] == 1) ? 'checked' : '';
            $item_day_special = (isset($item['day_special']) && $item['day_special'] == 1) ? 'checked' : '';
            $discount = (isset($item['discount']) && !empty($item['discount'])) ? $item['discount'] : 0;

            // Item Category Tags Array
            if(count($item_cat_tags) > 0)
            {
                foreach ($item_cat_tags as $key => $value)
                {
                    $primary_tag_data[] = isset($value->hasOneTag[$primary_lang_code.'_name']) ? $value->hasOneTag[$primary_lang_code.'_name'] : '';
                }
            }
            else
            {
                $primary_tag_data = [];
            }

            // Additional Languages
            $additional_languages = AdditionalLanguage::get();

            if(count($additional_languages) > 0)
            {
                $html = '';
                $html .= '<div class="lang-tab">';
                    // Primary Language
                    $html .= '<a class="active text-uppercase" onclick="updateItemByCode(\''.$primary_lang_code.'\')">'.$primary_lang_code.'</a>';

                    // Additional Language
                    foreach($additional_languages as $value)
                    {
                        // Additional Language Details
                        $add_lang_detail = Languages::where('id',$value->language_id)->first();
                        $add_lang_code = isset($add_lang_detail->code) ? $add_lang_detail->code : '';

                        $html .= '<a class="text-uppercase" onclick="updateItemByCode(\''.$add_lang_code.'\')">'.$add_lang_code.'</a>';
                    }
                $html .= '</div>';

                $html .= '<hr>';

                $html .= '<div class="row">';
                    $html .= '<div class="col-md-12">';
                        $html .= '<form id="edit_item_form" enctype="multipart/form-data">';

                            $html .= csrf_field();
                            $html .= '<input type="hidden" name="active_lang_code" id="active_lang_code" value="'.$primary_lang_code.'">';
                            $html .= '<input type="hidden" name="item_id" id="item_id" value="'.$item['id'].'">';

                            // Item Type
                            $html .= '<div class="row mb-3">';
                                $html .= '<div class="col-md-12">';
                                    $html .= '<label class="form-label" for="type">'.__('Type').'</label>';
                                    $html .= '<select name="type" id="type" class="form-select" onchange="togglePrice('.$modalName.')">';
                                        $html .= '<option value="1"';
                                            if($item_type == 1)
                                            {
                                                $html .= 'selected';
                                            }
                                        $html .='>Product</option>';
                                        $html .= '<option value="2"';
                                            if($item_type == 2)
                                            {
                                                $html .= 'selected';
                                            }
                                        $html .= '>Divider</option>';
                                    $html .= '</select>';
                                $html .= '</div>';
                            $html .= '</div>';

                            // Category
                            $html .= '<div class="row mb-3">';
                                $html .= '<div class="col-md-12">';
                                    $html .= '<label class="form-label" for="category">'. __('Category').'</label>';
                                    $html .= '<select name="category" id="category" class="form-select">';
                                            $html .= '<option value="">Choose Category</option>';
                                            if(count($parent_categories) > 0)
                                            {
                                                foreach ($parent_categories as $parent_cat)
                                                {
                                                    $quote = "";
                                                    $html .= '<option value="'. $parent_cat->id.'" style="font-weight: 900"';

                                                    if($parent_cat->id == $category_id)
                                                    {
                                                        $html .= 'selected';
                                                    }

                                                    $html .='>'.$parent_cat[$primary_lang_code."_name"].'</option>';

                                                    if(count($parent_cat->subcategories) > 0)
                                                    {
                                                        $cat_data['quote'] = $quote;
                                                        $cat_data['par_cat_id'] = $category_id;
                                                        $cat_data['name_key'] = $primary_lang_code."_name";
                                                        $cat_data['subcategories'] = $parent_cat->subcategories;

                                                        $html .= $this->child_cat($cat_data);
                                                    }
                                                }
                                            }
                                    $html .= '</select>';
                                $html .= '</div>';
                            $html .= '</div>';

                            // Item Name
                            $html .= '<div class="row mb-3">';
                                $html .= '<div class="col-md-12">';
                                    $html .= '<label class="form-label" for="item_name">'.__('Name').'</label>';
                                    $html .= '<input type="text" name="item_name" id="item_name" class="form-control" value="'.$item_name.'">';
                                $html .= '</div>';
                            $html .= '</div>';

                            // Price
                            $html .= '<div class="row price_div priceDiv">';
                                $html .= '<div class="col-md-12" id="priceDiv">';
                                    $html .= '<label class="form-label">'.__('Price').'</label>';
                                    if(isset($price_array) && count($price_array) > 0)
                                    {
                                        foreach($price_array as $key => $price_arr)
                                        {
                                            $price_label = isset($price_arr[$item_price_label_key]) ? $price_arr[$item_price_label_key] : '';
                                            $price_count = $key + 1;

                                            $html .= '<div class="row mb-3 align-items-center price price_'.$price_count.'">';
                                                $html .= '<div class="col-md-5 mb-1">';
                                                    $html .= '<input type="text" name="price[price][]" class="form-control" placeholder="Enter Price" value="'.$price_arr['price'].'">';
                                                    $html .= '<input type="hidden" name="price[priceID][]" value="'.$price_arr['id'].'">';
                                                $html .= '</div>';
                                                $html .= '<div class="col-md-6 mb-1">';
                                                    $html .= '<input type="text" name="price[label][]" class="form-control" placeholder="Enter Price Label" value="'.$price_label.'">';
                                                $html .= '</div>';
                                                $html .= '<div class="col-md-1 mb-1">';
                                                    $html .= '<a onclick="deleteItemPrice('.$price_arr['id'].','.$price_count.')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
                                                $html .= '</div>';
                                            $html .= '</div>';
                                        }
                                    }
                                $html .= '</div>';
                            $html .= '</div>';

                            // Price Increment Button
                            $html .= '<div class="row mb-3 price_div priceDiv">';
                                $html .= '<div class="col-md-12">';
                                    $html .= '<a onclick="addPrice(\'editItemModal\')" class="btn addPriceBtn btn-info text-white">'.__('Add Price').'</a>';
                                $html .= '</div>';
                            $html .= '</div>';

                            // Button for Show & Hide More Details
                            $html .= '<div class="row mb-3">';
                                $html .= '<div class="col-md-12 text-center">';
                                    $html .= '<a class="btn btn-sm btn-primary" style="cursor: pointer" onclick="toggleMoreDetails(\'editItemModal\')" id="more_dt_btn">More Details.. <i class="bi bi-eye-slash"></i></a>';
                                $html .= '</div>';
                            $html .= '</div>';

                            $html .= '<div class="row" id="more_details" style="display: none;">';

                                // Discount Type
                                $html .= '<div class="col-md-12 mb-3">';
                                    $html .= '<label class="form-label" for="item_description">'.__('Discount Type').'</label>';
                                    $html .= '<select name="discount_type" id="discount_type" class="form-control">';
                                        $html .= '<option value="percentage" ';
                                            if($item['discount_type'] == 'percentage')
                                            {
                                                $html .= 'selected';
                                            }
                                        $html .= '>'.__('Percentage %').'</option>';
                                        $html .= '<option value="fixed" ';
                                            if($item['discount_type'] == 'fixed')
                                            {
                                                $html .= 'selected';
                                            }
                                        $html .= '>'.__('Fixed Amount').'</option>';
                                    $html .= '</select>';
                                $html .= '</div>';

                                // Discount
                                $html .= '<div class="col-md-12 mb-3">';
                                    $html .= '<label class="form-label" for="item_description">'.__('Discount').'</label>';
                                    $html .= '<input type="number" name="discount" id="discount" class="form-control" value="'.$discount.'">';
                                $html .= '</div>';

                                // Description
                                $html .= '<div class="col-md-12 mb-3">';
                                    $html .= '<label class="form-label" for="item_description">'.__('Desription').'</label>';
                                    $html .= '<textarea name="item_description" id="item_description" class="form-control item_description" rows="3">'.$item_desc.'</textarea>';
                                $html .= '</div>';

                                // Image Section
                                $html .= '<div class="col-md-12 mt-3 mb-2 d-flex flex-wrap" id="edit_images_div">';
                                    if(isset($item->itemImages) && count($item->itemImages) > 0)
                                    {
                                        foreach($item->itemImages as $key => $item_image)
                                        {
                                            $no = $key + 1;
                                            if(!empty($item_image['image']) && file_exists('public/client_uploads/items/'.$item_image['image']))
                                            {
                                                $html .= '<div class="inner-img edit_img_'.$no.'">';
                                                    $html .= '<img src="'.asset('public/client_uploads/items/'.$item_image['image']).'" class="w-100 h-100">';
                                                    $html .= '<a class="btn btn-sm btn-danger del-pre-btn" onclick="deleteItemImages('.$no.','.$item_image->id.')"><i class="fa fa-trash"></i></a>';
                                                $html .= '</div>';
                                            }
                                        }
                                    }
                                $html .= '</div>';
                                $html .= '<div class="col-md-12 mb-2 d-flex flex-wrap" id="images_div">
                                </div>';
                                $html .= '<div class="col-md-12 mul-image" id="img-val"></div>';
                                $html .= '<div class="col-md-12">';
                                    $html .= '<label class="form-label">'.__('Image').'</label>';
                                $html .= '</div>';
                                $html .= '<div class="col-md-12 mt-2 mul-image">';
                                    $html .= '<div class="row">';
                                        $html .= '<div class="col-md-12">';
                                            $html .= '<div class="form-group">';
                                                $html .= '<div id="img-label"><label for="item_image">Upload Images</label></div>';
                                                $html .= '<input type="file" name="item_image" id="item_image" class="form-control" onchange="imageCropper(\'edit_item_form\',this)" style="display: none;">';
                                            $html .= '</div>';
                                        $html .= '</div>';
                                        $html .= '<div class="col-md-12"><code class="img-upload-label">Upload Image in (400*400) Dimensions</code></div>';
                                    $html .= '</div>';
                                $html .= '</div>';
                                $html .= '<div class="col-md-8 img-crop-sec mb-2" style="display: none">';
                                    $html .= '<img src="" alt="" id="resize-image" class="w-100">';
                                    $html .= '<div class="mt-3">';
                                        $html .= '<a class="btn btn-sm btn-success" onclick="saveCropper(\'edit_item_form\')">Save</a>';
                                        $html .= '<a class="btn btn-sm btn-danger" onclick="resetCropper()">Reset</a>';
                                        $html .= '<a class="btn btn-sm btn-secondary" onclick="cancelCropper(\'edit_item_form\')">Cancel</a>';
                                    $html .= '</div>';
                                $html .= '</div>';
                                $html .= '<div class="col-md-4 img-crop-sec" style="display: none;">';
                                    $html .= '<div class="preview" style="width: 200px; height:200px; overflow: hidden;margin: 0 auto;"></div>';
                                $html .= '</div>';

                                // Tags
                                $html .= '<div class="col-md-12 mb-3">';
                                    $html .= '<label class="form-label" for="tags">'.__('Tags').'</label>';
                                    $html .= '<select name="tags[]" id="tags" class="form-select" multiple>';
                                    if(count($tags) > 0)
                                    {
                                        foreach($tags as $tag)
                                        {
                                            $html .= '<option value="'.$tag[$primary_lang_code."_name"].'"';
                                            if(in_array($tag[$primary_lang_code."_name"],$primary_tag_data))
                                            {
                                                $html .= 'selected';
                                            }
                                            $html .='>'.$tag[$primary_lang_code."_name"].'</option>';
                                        }
                                    }
                                    $html .= '</select>';
                                $html .= '</div>';

                                // Order Attributes
                                $html .= '<div class="col-md-12 mb-3">';
                                    $html .= '<label class="form-label" for="options">'.__('Attributes').'</label>';
                                    $html .= '<select name="options[]" id="options" class="form-select" multiple>';
                                        if(count($options) > 0)
                                        {
                                            foreach($options as $opt)
                                            {
                                                $html .= '<option value="'.$opt["id"].'"';
                                                    if(in_array($opt["id"],$item_options))
                                                    {
                                                        $html .= 'selected';
                                                    }
                                                $html .='>'.$opt[$option_title_key].'</option>';
                                            }
                                        }
                                    $html .= '</select>';
                                $html .= '</div>';

                                // Toggle Buttons
                                $html .= '<div class="col-md-12 mb-3 mt-1">';
                                    $html .= '<div class="row">';

                                        $html .= '<div class="col-md-6 mark_new mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="mark_new" name="is_new" value="1" '.$item_is_new.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="mark_new" class="form-label">'.__('New').'</label>';
                                        $html .= '</div>';

                                        $html .= '<div class="col-md-6 pers_message mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="pers_message" name="pers_message" value="1" '.$item_pers_message.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="pers_message" class="form-label">'.__('Personalized Message').'</label>';
                                        $html .= '</div>';

                                        $html .= '<div class="col-md-6 day_special mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="day_special" name="day_special" value="1" '.$item_day_special.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="day_special" class="form-label">'.__('Day Special').'</label>';
                                        $html .= '</div>';

                                        $html .= '<div class="col-md-6 mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="publish" name="published" value="1" '.$item_published.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="publish" class="form-label">'.__('Published').'</label>';
                                        $html .= '</div>';

                                        $html .= '<div class="col-md-6 review_rating mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="review_rating" name="review_rating" value="1" '.$review_rating.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="review_rating" class="form-label">'.__('Review & Rating').'</label>';
                                        $html .= '</div>';

                                    $html .= '</div>';
                                $html .= '</div>';

                            $html .= '</div>';

                        $html .= '</form>';
                    $html .= '</div>';
                $html .= '</div>';

            }
            else
            {
                $html = '';
                $html .= '<div class="lang-tab">';
                    // Primary Language
                    $html .= '<a class="active text-uppercase" onclick="updateItemByCode(\''.$primary_lang_code.'\')">'.$primary_lang_code.'</a>';
                $html .= '</div>';

                $html .= '<hr>';

                $html .= '<div class="row">';
                    $html .= '<div class="col-md-12">';
                        $html .= '<form id="edit_item_form" enctype="multipart/form-data">';

                            $html .= csrf_field();
                            $html .= '<input type="hidden" name="active_lang_code" id="active_lang_code" value="'.$primary_lang_code.'">';
                            $html .= '<input type="hidden" name="item_id" id="item_id" value="'.$item['id'].'">';

                            // Item Type
                            $html .= '<div class="row mb-3">';
                                $html .= '<div class="col-md-12">';
                                    $html .= '<label class="form-label" for="type">'.__('Type').'</label>';
                                    $html .= '<select name="type" id="type" class="form-select" onchange="togglePrice('.$modalName.')">';
                                        $html .= '<option value="1"';
                                            if($item_type == 1)
                                            {
                                                $html .= 'selected';
                                            }
                                        $html .='>Product</option>';
                                        $html .= '<option value="2"';
                                            if($item_type == 2)
                                            {
                                                $html .= 'selected';
                                            }
                                        $html .= '>Divider</option>';
                                    $html .= '</select>';
                                $html .= '</div>';
                            $html .= '</div>';

                            // Category
                            $html .= '<div class="row mb-3">';
                                $html .= '<div class="col-md-12">';
                                    $html .= '<label class="form-label" for="category">'. __('Category').'</label>';
                                    $html .= '<select name="category" id="category" class="form-select">';
                                            $html .= '<option value="">Choose Category</option>';
                                            if(count($parent_categories) > 0)
                                            {
                                                foreach ($parent_categories as $parent_cat)
                                                {
                                                    $quote = "";
                                                    $html .= '<option value="'. $parent_cat->id.'" style="font-weight: 900"';

                                                    if($parent_cat->id == $category_id)
                                                    {
                                                        $html .= 'selected';
                                                    }

                                                    $html .='>'.$parent_cat[$primary_lang_code."_name"].'</option>';

                                                    if(count($parent_cat->subcategories) > 0)
                                                    {
                                                        $cat_data['quote'] = $quote;
                                                        $cat_data['par_cat_id'] = $category_id;
                                                        $cat_data['name_key'] = $primary_lang_code."_name";
                                                        $cat_data['subcategories'] = $parent_cat->subcategories;

                                                        $html .= $this->child_cat($cat_data);
                                                    }
                                                }
                                            }
                                    $html .= '</select>';
                                $html .= '</div>';
                            $html .= '</div>';

                            // Item Name
                            $html .= '<div class="row mb-3">';
                                $html .= '<div class="col-md-12">';
                                    $html .= '<label class="form-label" for="item_name">'.__('Name').'</label>';
                                    $html .= '<input type="text" name="item_name" id="item_name" class="form-control" value="'.$item_name.'">';
                                $html .= '</div>';
                            $html .= '</div>';

                            // Price
                            $html .= '<div class="row price_div priceDiv">';
                                $html .= '<div class="col-md-12" id="priceDiv">';
                                    $html .= '<label class="form-label">'.__('Price').'</label>';
                                    if(isset($price_array) && count($price_array) > 0)
                                    {
                                        foreach($price_array as $key => $price_arr)
                                        {
                                            $price_label = isset($price_arr[$item_price_label_key]) ? $price_arr[$item_price_label_key] : '';
                                            $price_count = $key + 1;

                                            $html .= '<div class="row mb-3 align-items-center price price_'.$price_count.'">';
                                                $html .= '<div class="col-md-5 mb-1">';
                                                    $html .= '<input type="text" name="price[price][]" class="form-control" placeholder="Enter Price" value="'.$price_arr['price'].'">';
                                                    $html .= '<input type="hidden" name="price[priceID][]" value="'.$price_arr['id'].'">';
                                                $html .= '</div>';
                                                $html .= '<div class="col-md-6 mb-1">';
                                                    $html .= '<input type="text" name="price[label][]" class="form-control" placeholder="Enter Price Label" value="'.$price_label.'">';
                                                $html .= '</div>';
                                                $html .= '<div class="col-md-1 mb-1">';
                                                    $html .= '<a onclick="deleteItemPrice('.$price_arr['id'].','.$price_count.')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
                                                $html .= '</div>';
                                            $html .= '</div>';
                                        }
                                    }
                                $html .= '</div>';
                            $html .= '</div>';

                            // Price Increment Button
                            $html .= '<div class="row mb-3 price_div priceDiv">';
                                $html .= '<div class="col-md-12">';
                                    $html .= '<a onclick="addPrice(\'editItemModal\')" class="btn addPriceBtn btn-info text-white">'.__('Add Price').'</a>';
                                $html .= '</div>';
                            $html .= '</div>';

                            // Button for Show & Hide More Details
                            $html .= '<div class="row mb-3">';
                                $html .= '<div class="col-md-12 text-center">';
                                    $html .= '<a class="btn btn-sm btn-primary" style="cursor: pointer" onclick="toggleMoreDetails(\'editItemModal\')" id="more_dt_btn">More Details.. <i class="bi bi-eye-slash"></i></a>';
                                $html .= '</div>';
                            $html .= '</div>';

                            $html .= '<div class="row" id="more_details" style="display: none;">';

                                // Discount Type
                                $html .= '<div class="col-md-12 mb-3">';
                                    $html .= '<label class="form-label" for="item_description">'.__('Discount Type').'</label>';
                                    $html .= '<select name="discount_type" id="discount_type" class="form-control">';
                                        $html .= '<option value="percentage" ';
                                            if($item['discount_type'] == 'percentage')
                                            {
                                                $html .= 'selected';
                                            }
                                        $html .= '>'.__('Percentage %').'</option>';
                                        $html .= '<option value="fixed" ';
                                            if($item['discount_type'] == 'fixed')
                                            {
                                                $html .= 'selected';
                                            }
                                        $html .= '>'.__('Fixed Amount').'</option>';
                                    $html .= '</select>';
                                $html .= '</div>';

                                // Discount
                                $html .= '<div class="col-md-12 mb-3">';
                                    $html .= '<label class="form-label" for="item_description">'.__('Discount').'</label>';
                                    $html .= '<input type="number" name="discount" id="discount" class="form-control" value="'.$discount.'">';
                                $html .= '</div>';

                                // Description
                                $html .= '<div class="col-md-12 mb-3">';
                                    $html .= '<label class="form-label" for="item_description">'.__('Desription').'</label>';
                                    $html .= '<textarea name="item_description" id="item_description" class="form-control item_description" rows="3">'.$item_desc.'</textarea>';
                                $html .= '</div>';

                                // Image Section
                                $html .= '<div class="col-md-12 mt-3 mb-2 d-flex flex-wrap" id="edit_images_div">';
                                    if(isset($item->itemImages) && count($item->itemImages) > 0)
                                    {
                                        foreach($item->itemImages as $key => $item_image)
                                        {
                                            $no = $key + 1;
                                            if(!empty($item_image['image']) && file_exists('public/client_uploads/items/'.$item_image['image']))
                                            {
                                                $html .= '<div class="inner-img edit_img_'.$no.'">';
                                                    $html .= '<img src="'.asset('public/client_uploads/items/'.$item_image['image']).'" class="w-100 h-100">';
                                                    $html .= '<a class="btn btn-sm btn-danger del-pre-btn" onclick="deleteItemImages('.$no.','.$item_image->id.')"><i class="fa fa-trash"></i></a>';
                                                $html .= '</div>';
                                            }
                                        }
                                    }
                                $html .= '</div>';

                                $html .= '<div class="col-md-12 mb-2 d-flex flex-wrap" id="images_div">
                                </div>';

                                $html .= '<div class="col-md-12 mul-image" id="img-val"></div>';

                                $html .= '<div class="col-md-12">';
                                    $html .= '<label class="form-label">'.__('Image').'</label>';
                                $html .= '</div>';

                                $html .= '<div class="col-md-12 mt-2 mul-image">';
                                    $html .= '<div class="row">';
                                        $html .= '<div class="col-md-12">';
                                            $html .= '<div class="form-group">';
                                                $html .= '<div id="img-label"><label for="item_image">Upload Images</label></div>';
                                                $html .= '<input type="file" name="item_image" id="item_image" class="form-control" onchange="imageCropper(\'edit_item_form\',this)" style="display: none;">';
                                            $html .= '</div>';
                                        $html .= '</div>';
                                        $html .= '<div class="col-md-12"><code class="img-upload-label">Upload Image in (400*400) Dimensions</code></div>';
                                    $html .= '</div>';
                                $html .= '</div>';

                                $html .= '<div class="col-md-8 img-crop-sec mb-2" style="display: none">';
                                    $html .= '<img src="" alt="" id="resize-image" class="w-100">';
                                    $html .= '<div class="mt-3">';
                                        $html .= '<a class="btn btn-sm btn-success" onclick="saveCropper(\'edit_item_form\')">Save</a>';
                                        $html .= '<a class="btn btn-sm btn-danger" onclick="resetCropper()">Reset</a>';
                                        $html .= '<a class="btn btn-sm btn-secondary" onclick="cancelCropper(\'edit_item_form\')">Cancel</a>';
                                    $html .= '</div>';
                                $html .= '</div>';
                                $html .= '<div class="col-md-4 img-crop-sec" style="display: none;">';
                                    $html .= '<div class="preview" style="width: 200px; height:200px; overflow: hidden;margin: 0 auto;"></div>';
                                $html .= '</div>';

                                // Tags
                                $html .= '<div class="col-md-12 mt-2 mb-3">';
                                    $html .= '<label class="form-label" for="tags">'.__('Tags').'</label>';
                                    $html .= '<select name="tags[]" id="tags" class="form-select" multiple>';
                                    if(count($tags) > 0)
                                    {
                                        foreach($tags as $tag)
                                        {
                                            $html .= '<option value="'.$tag[$primary_lang_code."_name"].'"';
                                            if(in_array($tag[$primary_lang_code."_name"],$primary_tag_data))
                                            {
                                                $html .= 'selected';
                                            }
                                            $html .='>'.$tag[$primary_lang_code."_name"].'</option>';
                                        }
                                    }
                                    $html .= '</select>';
                                $html .= '</div>';

                                // Order Attributes
                                $html .= '<div class="col-md-12 mb-3">';
                                    $html .= '<label class="form-label" for="options">'.__('Attributes').'</label>';
                                    $html .= '<select name="options[]" id="options" class="form-select" multiple>';
                                        if(count($options) > 0)
                                        {
                                            foreach($options as $opt)
                                            {
                                                $html .= '<option value="'.$opt["id"].'"';
                                                    if(in_array($opt["id"],$item_options))
                                                    {
                                                        $html .= 'selected';
                                                    }
                                                $html .='>'.$opt[$option_title_key].'</option>';
                                            }
                                        }
                                    $html .= '</select>';
                                $html .= '</div>';

                                // Toggle Buttons
                                $html .= '<div class="col-md-12 mb-3 mt-1">';
                                    $html .= '<div class="row">';

                                        $html .= '<div class="col-md-6 mark_new mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="mark_new" name="is_new" value="1" '.$item_is_new.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="mark_new" class="form-label">'.__('New').'</label>';
                                        $html .= '</div>';

                                        $html .= '<div class="col-md-6 pers_message mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="pers_message" name="pers_message" value="1" '.$item_pers_message.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="pers_message" class="form-label">'.__('Personalized Message').'</label>';
                                        $html .= '</div>';

                                        $html .= '<div class="col-md-6 day_special mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="day_special" name="day_special" value="1" '.$item_day_special.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="day_special" class="form-label">'.__('Day Special').'</label>';
                                        $html .= '</div>';

                                        $html .= '<div class="col-md-6 mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="publish" name="published" value="1" '.$item_published.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="publish" class="form-label">'.__('Published').'</label>';
                                        $html .= '</div>';

                                        $html .= '<div class="col-md-6 review_rating mb-3">';
                                            $html .= '<label class="switch me-2">';
                                                $html .= '<input type="checkbox" id="review_rating" name="review_rating" value="1" '.$review_rating.'>';
                                                $html .= '<span class="slider round">';
                                                    $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                    $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                                $html .= '</span>';
                                            $html .= '</label>';
                                            $html .= '<label for="review_rating" class="form-label">'.__('Review & Rating').'</label>';
                                        $html .= '</div>';

                                    $html .= '</div>';
                                $html .= '</div>';

                            $html .= '</div>';

                        $html .= '</form>';
                    $html .= '</div>';
                $html .= '</div>';
            }

            return response()->json([
                'success' => 1,
                'message' => "Item Details has been Retrived Successfully..",
                'data'=> $html,
                'item_type'=> $item_type,
            ]);
        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => "Internal Server Error!",
            ]);
        }
    }



    // Get Child Categories
    function child_cat($data)
    {
        $quote = $data['quote'];
        $quote .= '-';
        $quote_array = strlen($quote);
        $fw = ($quote_array == 1) ? 600 : 400;

        $html = '';

        foreach($data['subcategories'] as $subcategory)
        {
            $html .= '<option value="'.$subcategory->id.'" style="font-weight: '.$fw.'"';

            if($data['par_cat_id'] == $subcategory->id)
            {
                $html .= 'selected';
            }

            $html .= '> &nbsp;'.$quote.' '.$subcategory[$data['name_key']].'</option>';

            if(count($subcategory->subcategories) > 0)
            {
                $new_data['quote'] = $quote;
                $new_data['par_cat_id'] = $data['par_cat_id'];
                $new_data['name_key'] = $data['name_key'];
                $new_data['subcategories'] = $subcategory->subcategories;

                $html .= $this->child_cat($new_data);
            }
        }

        return $html;
    }


    // Function for Update Existing Item
    public function update(Request $request)
    {
        $request->validate([
            'item_name'   => 'required',
            'category'   => 'required',
        ]);

        $item_id = $request->item_id;
        $item_type = $request->type;
        $category = $request->category;
        $item_name = $request->item_name;
        $discount_type = $request->discount_type;
        $discount = $request->discount;
        $item_description = $request->item_description;
        $is_new = isset($request->is_new) ? $request->is_new : 0;
        $pers_message = isset($request->pers_message) ? $request->pers_message : 0;
        $day_special = isset($request->day_special) ? $request->day_special : 0;
        $published = isset($request->published) ? $request->published : 0;
        $review_rating = isset($request->review_rating) ? $request->review_rating : 0;

        $active_lang_code = $request->active_lang_code;

        $price_array['price'] = isset($request->price['price']) ? array_filter($request->price['price']) : [];
        $price_array['label'] = isset($request->price['label']) ? $request->price['label'] : [];
        $price_array['priceID'] = isset($request->price['priceID']) ? $request->price['priceID'] : [];

        $options = (isset($request->options) && count($request->options) > 0) ? serialize($request->options) : '';
        $tags = isset($request->tags) ? $request->tags : [];

        if(count($price_array['price']) > 0)
        {
            $item_price = $price_array;
        }
        else
        {
            $item_price = [];
        }


        try
        {
            $name_key = $active_lang_code."_name";
            $description_key = $active_lang_code."_description";
            $price_label_key = $active_lang_code."_label";

            $item = Items::find($item_id);

            if($item)
            {
                $item->category_id = $category;
                $item->published = $published;
                $item->is_new = $is_new;
                $item->pers_message = $pers_message;
                $item->day_special = $day_special;
                $item->review = $review_rating;
                $item->options = $options;
                $item->type = $item_type;
                $item->discount_type = $discount_type;
                $item->discount = $discount;

                $item->name = $item_name;
                $item->description = $item_description;

                $item->$name_key = $item_name;
                $item->$description_key = $item_description;

                // Multiple Images
                $all_images = (isset($request->og_image)) ? $request->og_image : [];
                if(count($all_images) > 0)
                {
                    foreach($all_images as $image)
                    {
                        $image_token = genratetoken(10);
                        $og_image = $image;
                        $image_arr = explode(";base64,", $og_image);
                        $image_type_ext = explode("image/", $image_arr[0]);
                        $image_base64 = base64_decode($image_arr[1]);

                        $imgname = "item_".$image_token.".".$image_type_ext[1];
                        $img_path = public_path('client_uploads/items/'.$imgname);
                        file_put_contents($img_path,$image_base64);

                        // Insert Image
                        $new_img = new ItemImages();
                        $new_img->item_id = $item_id;
                        $new_img->image = $imgname;
                        $new_img->save();
                    }
                }

                $item->update();

                // Update & Insert New Price
                if(count($item_price) > 0)
                {
                    $price_arr = $item_price['price'];
                    $label_arr = $item_price['label'];
                    $ids_arr = $item_price['priceID'];

                    if(count($price_arr) > 0)
                    {
                        foreach($price_arr as $key => $price_val)
                        {
                            $label_val = isset($label_arr[$key]) ? $label_arr[$key] : '';
                            $price_id = isset($ids_arr[$key]) ? $ids_arr[$key] : '';

                            if(!empty($price_id) || $price_id != '') // Update Price
                            {
                                $upd_price = ItemPrice::find($price_id);
                                $upd_price->price = $price_val;
                                $upd_price->label = $label_val;
                                $upd_price->$price_label_key = $label_val;
                                $upd_price->update();
                            }
                            else // Insert New Price
                            {
                                $new_price = new ItemPrice();
                                $new_price->item_id = $item_id;
                                $new_price->price = $price_val;
                                $new_price->label = $label_val;
                                $new_price->$price_label_key = $label_val;
                                $new_price->save();
                            }
                        }
                    }

                }

                CategoryProductTags::where('category_id',$item->category_id)->where('item_id',$item->id)->delete();

                // Insert & Update Tags
                if(count($tags) > 0)
                {
                    foreach($tags as $val)
                    {
                        $findTag = Tags::where($name_key,$val)->first();
                        $tag_id = (isset($findTag->id) && !empty($findTag->id)) ? $findTag->id : '';

                        if(!empty($tag_id) || $tag_id != '')
                        {
                            $tag = Tags::find($tag_id);
                            $tag->name = $val;
                            $tag->$name_key = $val;
                            $tag->update();
                        }
                        else
                        {
                            $max_order = Tags::max('order');
                            $order = (isset($max_order) && !empty($max_order)) ? ($max_order + 1) : 1;

                            $tag = new Tags();
                            $tag->name = $val;
                            $tag->$name_key = $val;
                            $tag->order = $order;
                            $tag->save();
                        }

                        if($tag->id)
                        {
                            $cat_pro_tag = new CategoryProductTags();
                            $cat_pro_tag->tag_id = $tag->id;
                            $cat_pro_tag->category_id = $item->category_id;
                            $cat_pro_tag->item_id = $item->id;
                            $cat_pro_tag->save();
                        }
                    }
                }

            }

            return response()->json([
                'success' => 1,
                'message' => "Item has been Updated SuccessFully....",
            ]);

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => "Internal Server Error!",
            ]);
        }

    }



    // Function for Update Category By Language Code
    public function updateByLangCode(Request $request)
    {
        $item_id = $request->item_id;
        $item_type = $request->type;
        $category = $request->category;
        $item_name = $request->item_name;
        $item_description = $request->item_description;
        $discount_type = $request->discount_type;
        $discount = $request->discount;
        $is_new = isset($request->is_new) ? $request->is_new : 0;
        $pers_message = isset($request->pers_message) ? $request->pers_message : 0;
        $day_special = isset($request->day_special) ? $request->day_special : 0;
        $published = isset($request->published) ? $request->published : 0;
        $review_rating = isset($request->review_rating) ? $request->review_rating : 0;

        $price_array['price'] = isset($request->price['price']) ? array_filter($request->price['price']) : [];
        $price_array['label'] = isset($request->price['label']) ? $request->price['label'] : [];
        $price_array['priceID'] = isset($request->price['priceID']) ? $request->price['priceID'] : [];

        $options = (isset($request->options) && count($request->options) > 0) ? serialize($request->options) : '';
        $tags = isset($request->tags) ? $request->tags : [];

        if(count($price_array['price']) > 0)
        {
            $item_price = $price_array;
        }
        else
        {
            $item_price = [];
        }

        $active_lang_code = $request->active_lang_code;
        $next_lang_code = $request->next_lang_code;
        $act_lang_name_key = $active_lang_code."_name";
        $act_lang_description_key = $active_lang_code."_description";
        $act_lang_price_key = $active_lang_code."_label";

        $request->validate([
            'item_name'   => 'required',
            'category'   => 'required',
        ]);

        try
        {
            // Update Item
            $item = Items::find($item_id);

            if($item)
            {
                $item->category_id = $category;
                $item->published = $published;
                $item->is_new = $is_new;
                $item->pers_message = $pers_message;
                $item->day_special = $day_special;
                $item->review = $review_rating;
                $item->options = $options;
                $item->type = $item_type;
                $item->discount_type = $discount_type;
                $item->discount = $discount;

                $item->name = $item_name;
                $item->description = $item_description;

                $item->$act_lang_name_key = $item_name;
                $item->$act_lang_description_key = $item_description;

                // Multiple Images
                $all_images = (isset($request->og_image)) ? $request->og_image : [];
                if(count($all_images) > 0)
                {
                    foreach($all_images as $image)
                    {
                        $image_token = genratetoken(10);
                        $og_image = $image;
                        $image_arr = explode(";base64,", $og_image);
                        $image_type_ext = explode("image/", $image_arr[0]);
                        $image_base64 = base64_decode($image_arr[1]);

                        $imgname = "item_".$image_token.".".$image_type_ext[1];
                        $img_path = public_path('client_uploads/items/'.$imgname);
                        file_put_contents($img_path,$image_base64);

                        // Insert Image
                        $new_img = new ItemImages();
                        $new_img->item_id = $item_id;
                        $new_img->image = $imgname;
                        $new_img->save();
                    }
                }

                $item->update();

                // Update & Insert New Price
                if(count($item_price) > 0)
                {
                    $price_arr = $item_price['price'];
                    $label_arr = $item_price['label'];
                    $ids_arr = $item_price['priceID'];

                    if(count($price_arr) > 0)
                    {
                        foreach($price_arr as $key => $price_val)
                        {
                            $label_val = isset($label_arr[$key]) ? $label_arr[$key] : '';
                            $price_id = isset($ids_arr[$key]) ? $ids_arr[$key] : '';

                            if(!empty($price_id) || $price_id != '') // Update Price
                            {
                                $upd_price = ItemPrice::find($price_id);
                                $upd_price->price = $price_val;
                                $upd_price->label = $label_val;
                                $upd_price->$act_lang_price_key = $label_val;
                                $upd_price->update();
                            }
                            else // Insert New Price
                            {
                                $new_price = new ItemPrice();
                                $new_price->item_id = $item_id;
                                $new_price->price = $price_val;
                                $new_price->label = $label_val;
                                $new_price->$act_lang_price_key = $label_val;
                                $new_price->save();
                            }
                        }
                    }

                }

                CategoryProductTags::where('category_id',$item->category_id)->where('item_id',$item->id)->delete();

                // Insert & Update Tags
                if(count($tags) > 0)
                {
                    foreach($tags as $val)
                    {
                        $findTag = Tags::where($act_lang_name_key,$val)->first();
                        $tag_id = (isset($findTag->id) && !empty($findTag->id)) ? $findTag->id : '';

                        if(!empty($tag_id) || $tag_id != '')
                        {
                            $tag = Tags::find($tag_id);
                            $tag->name = $val;
                            $tag->$act_lang_name_key = $val;
                            $tag->update();
                        }
                        else
                        {
                            $max_order = Tags::max('order');
                            $order = (isset($max_order) && !empty($max_order)) ? ($max_order + 1) : 1;

                            $tag = new Tags();
                            $tag->name = $val;
                            $tag->$act_lang_name_key = $val;
                            $tag->order = $order;
                            $tag->save();
                        }

                        if($tag->id)
                        {
                            $cat_pro_tag = new CategoryProductTags();
                            $cat_pro_tag->tag_id = $tag->id;
                            $cat_pro_tag->category_id = $item->category_id;
                            $cat_pro_tag->item_id = $item->id;
                            $cat_pro_tag->save();
                        }
                    }
                }
            }

            // Get HTML Data
            $html_data = $this->getEditItemData($next_lang_code,$item_id);

            return response()->json([
                'success' => 1,
                'message' => "Item Details has been Retrived Successfully..",
                'data' => $html_data,
                'item_type'=> $item_type,
            ]);
        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => "Internal Server Error!",
            ]);
        }

    }



    // Function for Get Item Data
    public function getEditItemData($current_lang_code,$item_id)
    {
        // Get Language Settings
        $language_settings = clientLanguageSettings();
        $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

        // Primary Language Details
        $primary_language_detail = Languages::where('id',$primary_lang_id)->first();
        $primary_lang_code = isset($primary_language_detail->code) ? $primary_language_detail->code : '';

        // Additional Languages
        $additional_languages = AdditionalLanguage::get();
        if(count($additional_languages) > 0)
        {
            $name_key = $current_lang_code."_name";
            $description_key = $current_lang_code."_description";
            $price_label_key = $current_lang_code."_label";
            $option_title_key = $current_lang_code."_title";
        }
        else
        {
            $name_key = $primary_lang_code."_name";
            $description_key = $primary_lang_code."_description";
            $price_label_key = $primary_lang_code."_label";
            $option_title_key = $primary_lang_code."_title";
        }

        // Item Details
        $item = Items::with(['itemImages'])->where('id',$item_id)->first();

        // Get all Parent Categories
        $parent_categories = Category::where('parent_id',NULL)->get();

        // Order Attributes
        $options = Option::get();

        // Tags
        $tags = Tags::get();

        // ModalName
        $modalName = "'editItemModal'";

        // Item Details
        $item_type = (isset($item['type'])) ? $item['type'] : '';
        $category_id = (isset($item['category_id'])) ? $item['category_id'] : '';
        $item_name = (isset($item[$name_key])) ? $item[$name_key] : '';
        $item_desc = (isset($item[$description_key])) ? $item[$description_key] : '';
        $price_array = ItemPrice::where('item_id',$item['id'])->get();
        $item_cat_tags = CategoryProductTags::with(['hasOneTag'])->where('item_id',$item['id'])->where('category_id',$item['category_id'])->get();
        $item_options = (isset($item['options']) && !empty($item['options'])) ? unserialize($item['options']) : [];
        $item_published = (isset($item['published']) && $item['published'] == 1) ? 'checked' : '';
        $review_rating = (isset($item['review']) && $item['review'] == 1) ? 'checked' : '';
        $item_is_new = (isset($item['is_new']) && $item['is_new'] == 1) ? 'checked' : '';
        $item_pers_message = (isset($item['pers_message']) && $item['pers_message'] == 1) ? 'checked' : '';
        $item_day_special = (isset($item['day_special']) && $item['day_special'] == 1) ? 'checked' : '';
        $discount = (isset($item['discount']) && !empty($item['discount'])) ? $item['discount'] : 0;

        // Item Category Tags Array
        if(count($item_cat_tags) > 0)
        {
            foreach ($item_cat_tags as $key => $value)
            {
                $lang_tag_data[] = isset($value->hasOneTag[$name_key]) ? $value->hasOneTag[$name_key] : '';
            }
        }
        else
        {
            $lang_tag_data = [];
        }

        // Primary Active Tab
        $primary_active_tab = ($primary_lang_code == $current_lang_code) ? 'active' : '';

        if(count($additional_languages) > 0)
        {
            $html = '';
            $html .= '<div class="lang-tab">';
                // Primary Language
                $html .= '<a class="'.$primary_active_tab.' text-uppercase" onclick="updateItemByCode(\''.$primary_lang_code.'\')">'.$primary_lang_code.'</a>';

                // Additional Language
                foreach($additional_languages as $value)
                {
                    // Additional Language Details
                    $add_lang_detail = Languages::where('id',$value->language_id)->first();
                    $add_lang_code = isset($add_lang_detail->code) ? $add_lang_detail->code : '';

                    // Additional Active Tab
                    $additional_active_tab = ($add_lang_code == $current_lang_code) ? 'active' : '';

                    $html .= '<a class="'.$additional_active_tab.' text-uppercase" onclick="updateItemByCode(\''.$add_lang_code.'\')">'.$add_lang_code.'</a>';
                }
            $html .= '</div>';

            $html .= '<hr>';

            $html .= '<div class="row">';
                $html .= '<div class="col-md-12">';
                    $html .= '<form id="edit_item_form" enctype="multipart/form-data">';

                        $html .= csrf_field();
                        $html .= '<input type="hidden" name="active_lang_code" id="active_lang_code" value="'.$current_lang_code.'">';
                        $html .= '<input type="hidden" name="item_id" id="item_id" value="'.$item['id'].'">';

                        // Item Type
                        $html .= '<div class="row mb-3">';
                            $html .= '<div class="col-md-12">';
                                $html .= '<label class="form-label" for="type">'.__('Type').'</label>';
                                $html .= '<select name="type" id="type" class="form-select" onchange="togglePrice('.$modalName.')">';
                                    $html .= '<option value="1"';
                                        if($item_type == 1)
                                        {
                                            $html .= 'selected';
                                        }
                                    $html .='>Product</option>';
                                    $html .= '<option value="2"';
                                        if($item_type == 2)
                                        {
                                            $html .= 'selected';
                                        }
                                    $html .= '>Divider</option>';
                                $html .= '</select>';
                            $html .= '</div>';
                        $html .= '</div>';


                        // Category
                        $html .= '<div class="row mb-3">';
                            $html .= '<div class="col-md-12">';
                                $html .= '<label class="form-label" for="category">'. __('Category').'</label>';
                                $html .= '<select name="category" id="category" class="form-select">';
                                        $html .= '<option value="">Choose Category</option>';
                                        if(count($parent_categories) > 0)
                                        {
                                            foreach ($parent_categories as $parent_cat)
                                            {
                                                $quote = "";
                                                $html .= '<option value="'. $parent_cat->id.'" style="font-weight: 900"';

                                                if($parent_cat->id == $category_id)
                                                {
                                                    $html .= 'selected';
                                                }

                                                $html .='>'.$parent_cat[$name_key].'</option>';

                                                if(count($parent_cat->subcategories) > 0)
                                                {
                                                    $cat_data['quote'] = $quote;
                                                    $cat_data['par_cat_id'] = $category_id;
                                                    $cat_data['name_key'] = $name_key;
                                                    $cat_data['subcategories'] = $parent_cat->subcategories;

                                                    $html .= $this->child_cat($cat_data);
                                                }
                                            }
                                        }
                                $html .= '</select>';
                            $html .= '</div>';
                        $html .= '</div>';

                        // Item Name
                        $html .= '<div class="row mb-3">';
                            $html .= '<div class="col-md-12">';
                                $html .= '<label class="form-label" for="item_name">'.__('Name').'</label>';
                                $html .= '<input type="text" name="item_name" id="item_name" class="form-control" value="'.$item_name.'">';
                            $html .= '</div>';
                        $html .= '</div>';

                        // Price
                        $html .= '<div class="row price_div priceDiv">';
                            $html .= '<div class="col-md-12" id="priceDiv">';
                                $html .= '<label class="form-label">'.__('Price').'</label>';
                                if(isset($price_array) && count($price_array) > 0)
                                {
                                    foreach($price_array as $key => $price_arr)
                                    {
                                        $price_label = isset($price_arr[$price_label_key]) ? $price_arr[$price_label_key] : '';
                                        $price_count = $key + 1;

                                        $html .= '<div class="row mb-3 align-items-center price price_'.$price_count.'">';
                                            $html .= '<div class="col-md-5 mb-1">';
                                                $html .= '<input type="text" name="price[price][]" class="form-control" placeholder="Enter Price" value="'.$price_arr['price'].'">';
                                                $html .= '<input type="hidden" name="price[priceID][]" value="'.$price_arr['id'].'">';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-6 mb-1">';
                                                $html .= '<input type="text" name="price[label][]" class="form-control" placeholder="Enter Price Label" value="'.$price_label.'">';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-1 mb-1">';
                                                $html .= '<a onclick="deleteItemPrice('.$price_arr['id'].','.$price_count.')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
                                            $html .= '</div>';
                                        $html .= '</div>';
                                    }
                                }
                            $html .= '</div>';
                        $html .= '</div>';

                        // Price Increment Button
                        $html .= '<div class="row mb-3 price_div priceDiv">';
                            $html .= '<div class="col-md-12">';
                                $html .= '<a onclick="addPrice(\'editItemModal\')" class="btn addPriceBtn btn-info text-white">'.__('Add Price').'</a>';
                            $html .= '</div>';
                        $html .= '</div>';

                        // Button for Show & Hide More Details
                        $html .= '<div class="row mb-3">';
                            $html .= '<div class="col-md-12 text-center">';
                                $html .= '<a class="btn btn-sm btn-primary" style="cursor: pointer" onclick="toggleMoreDetails(\'editItemModal\')" id="more_dt_btn">More Details.. <i class="bi bi-eye-slash"></i></a>';
                            $html .= '</div>';
                        $html .= '</div>';

                        $html .= '<div class="row" id="more_details" style="display: none;">';

                            // Discount Type
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="item_description">'.__('Discount Type').'</label>';
                                $html .= '<select name="discount_type" id="discount_type" class="form-control">';
                                    $html .= '<option value="percentage" ';
                                        if($item['discount_type'] == 'percentage')
                                        {
                                            $html .= 'selected';
                                        }
                                    $html .= '>'.__('Percentage %').'</option>';
                                    $html .= '<option value="fixed" ';
                                        if($item['discount_type'] == 'fixed')
                                        {
                                            $html .= 'selected';
                                        }
                                    $html .= '>'.__('Fixed Amount').'</option>';
                                $html .= '</select>';
                            $html .= '</div>';

                            // Discount
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="item_description">'.__('Discount').'</label>';
                                $html .= '<input type="number" name="discount" id="discount" class="form-control" value="'.$discount.'">';
                            $html .= '</div>';

                            // Description
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="item_description">'.__('Desription').'</label>';
                                $html .= '<textarea name="item_description" id="item_description" class="form-control item_description" rows="3">'.$item_desc.'</textarea>';
                            $html .= '</div>';

                            // Image Section
                            $html .= '<div class="col-md-12 mt-3 mb-2 d-flex flex-wrap" id="edit_images_div">';
                                if(isset($item->itemImages) && count($item->itemImages) > 0)
                                {
                                    foreach($item->itemImages as $key => $item_image)
                                    {
                                        $no = $key + 1;
                                        if(!empty($item_image['image']) && file_exists('public/client_uploads/items/'.$item_image['image']))
                                        {
                                            $html .= '<div class="inner-img edit_img_'.$no.'">';
                                                $html .= '<img src="'.asset('public/client_uploads/items/'.$item_image['image']).'" class="w-100 h-100">';
                                                $html .= '<a class="btn btn-sm btn-danger del-pre-btn" onclick="deleteItemImages('.$no.','.$item_image->id.')"><i class="fa fa-trash"></i></a>';
                                            $html .= '</div>';
                                        }
                                    }
                                }
                            $html .= '</div>';
                            $html .= '<div class="col-md-12 mb-2 d-flex flex-wrap" id="images_div">
                            </div>';
                            $html .= '<div class="col-md-12 mul-image" id="img-val"></div>';
                            $html .= '<div class="col-md-12">';
                                $html .= '<label class="form-label">'.__('Image').'</label>';
                            $html .= '</div>';
                            $html .= '<div class="col-md-12 mt-2 mul-image">';
                                $html .= '<div class="row">';
                                    $html .= '<div class="col-md-12">';
                                        $html .= '<div class="form-group">';
                                            $html .= '<div id="img-label"><label for="item_image">Upload Images</label></div>';
                                            $html .= '<input type="file" name="item_image" id="item_image" class="form-control" onchange="imageCropper(\'edit_item_form\',this)" style="display: none;">';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-md-12"><code class="img-upload-label">Upload Image in (400*400) Dimensions</code></div>';
                                $html .= '</div>';
                            $html .= '</div>';
                            $html .= '<div class="col-md-8 img-crop-sec mb-2" style="display: none">';
                                $html .= '<img src="" alt="" id="resize-image" class="w-100">';
                                $html .= '<div class="mt-3">';
                                    $html .= '<a class="btn btn-sm btn-success" onclick="saveCropper(\'edit_item_form\')">Save</a>';
                                    $html .= '<a class="btn btn-sm btn-danger" onclick="resetCropper()">Reset</a>';
                                    $html .= '<a class="btn btn-sm btn-secondary" onclick="cancelCropper(\'edit_item_form\')">Cancel</a>';
                                $html .= '</div>';
                            $html .= '</div>';
                            $html .= '<div class="col-md-4 img-crop-sec" style="display: none;">';
                                $html .= '<div class="preview" style="width: 200px; height:200px; overflow: hidden;margin: 0 auto;"></div>';
                            $html .= '</div>';

                            // Tags
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="tags">'.__('Tags').'</label>';
                                $html .= '<select name="tags[]" id="tags" class="form-select" multiple>';
                                if(count($tags) > 0)
                                {
                                    foreach($tags as $tag)
                                    {
                                        $html .= '<option value="'.$tag[$name_key].'"';
                                        if(in_array($tag[$name_key],$lang_tag_data))
                                        {
                                            $html .= 'selected';
                                        }
                                        $html .='>'.$tag[$name_key].'</option>';
                                    }
                                }
                                $html .= '</select>';
                            $html .= '</div>';

                            // Order Attributes
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="options">'.__('Attributes').'</label>';
                                $html .= '<select name="options[]" id="options" class="form-select" multiple>';
                                    if(count($options) > 0)
                                    {
                                        foreach($options as $opt)
                                        {
                                            $html .= '<option value="'.$opt["id"].'"';
                                                if(in_array($opt["id"],$item_options))
                                                {
                                                    $html .= 'selected';
                                                }
                                            $html .='>'.$opt[$option_title_key].'</option>';
                                        }
                                    }
                                $html .= '</select>';
                            $html .= '</div>';

                            // Toggle Buttons
                            $html .= '<div class="col-md-12 mb-3 mt-1">';
                                $html .= '<div class="row">';

                                    $html .= '<div class="col-md-6 mark_new mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="mark_new" name="is_new" value="1" '.$item_is_new.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="mark_new" class="form-label">'.__('New').'</label>';
                                    $html .= '</div>';

                                    $html .= '<div class="col-md-6 pers_message mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="pers_message" name="pers_message" value="1" '.$item_pers_message.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="pers_message" class="form-label">'.__('Personalized Message').'</label>';
                                    $html .= '</div>';

                                    $html .= '<div class="col-md-6 day_special mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="day_special" name="day_special" value="1" '.$item_day_special.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="day_special" class="form-label">'.__('Day Special').'</label>';
                                    $html .= '</div>';

                                    $html .= '<div class="col-md-6 mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="publish" name="published" value="1" '.$item_published.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="publish" class="form-label">'.__('Published').'</label>';
                                    $html .= '</div>';

                                    $html .= '<div class="col-md-6 review_rating mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="review_rating" name="review_rating" value="1" '.$review_rating.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="review_rating" class="form-label">'.__('Review & Rating').'</label>';
                                    $html .= '</div>';

                                $html .= '</div>';
                            $html .= '</div>';

                        $html .= '</div>';

                    $html .= '</form>';
                $html .= '</div>';
            $html .= '</div>';
        }
        else
        {
            $html = '';
            $html .= '<div class="lang-tab">';
                // Primary Language
                $html .= '<a class="active text-uppercase" onclick="updateItemByCode(\''.$primary_lang_code.'\')">'.$primary_lang_code.'</a>';
            $html .= '</div>';

            $html .= '<hr>';

            $html .= '<div class="row">';
                $html .= '<div class="col-md-12">';
                    $html .= '<form id="edit_item_form" enctype="multipart/form-data">';

                        $html .= csrf_field();
                        $html .= '<input type="hidden" name="active_lang_code" id="active_lang_code" value="'.$primary_lang_code.'">';
                        $html .= '<input type="hidden" name="item_id" id="item_id" value="'.$item['id'].'">';

                        // Item Type
                        $html .= '<div class="row mb-3">';
                            $html .= '<div class="col-md-12">';
                                $html .= '<label class="form-label" for="type">'.__('Type').'</label>';
                                $html .= '<select name="type" id="type" class="form-select" onchange="togglePrice('.$modalName.')">';
                                    $html .= '<option value="1"';
                                        if($item_type == 1)
                                        {
                                            $html .= 'selected';
                                        }
                                    $html .='>Product</option>';
                                    $html .= '<option value="2"';
                                        if($item_type == 2)
                                        {
                                            $html .= 'selected';
                                        }
                                    $html .= '>Divider</option>';
                                $html .= '</select>';
                            $html .= '</div>';
                        $html .= '</div>';

                        // Category
                        $html .= '<div class="row mb-3">';
                            $html .= '<div class="col-md-12">';
                                $html .= '<label class="form-label" for="category">'. __('Category').'</label>';
                                $html .= '<select name="category" id="category" class="form-select">';
                                        $html .= '<option value="">Choose Category</option>';
                                        if(count($parent_categories) > 0)
                                        {
                                            foreach ($parent_categories as $parent_cat)
                                            {
                                                $quote = "";
                                                $html .= '<option value="'. $parent_cat->id.'" style="font-weight: 900"';

                                                if($parent_cat->id == $category_id)
                                                {
                                                    $html .= 'selected';
                                                }

                                                $html .='>'.$parent_cat[$name_key].'</option>';

                                                if(count($parent_cat->subcategories) > 0)
                                                {
                                                    $cat_data['quote'] = $quote;
                                                    $cat_data['par_cat_id'] = $category_id;
                                                    $cat_data['name_key'] = $name_key;
                                                    $cat_data['subcategories'] = $parent_cat->subcategories;

                                                    $html .= $this->child_cat($cat_data);
                                                }
                                            }
                                        }
                                $html .= '</select>';
                            $html .= '</div>';
                        $html .= '</div>';

                        // Item Name
                        $html .= '<div class="row mb-3">';
                            $html .= '<div class="col-md-12">';
                                $html .= '<label class="form-label" for="item_name">'.__('Name').'</label>';
                                $html .= '<input type="text" name="item_name" id="item_name" class="form-control" value="'.$item_name.'">';
                            $html .= '</div>';
                        $html .= '</div>';

                        // Price
                        $html .= '<div class="row price_div priceDiv">';
                            $html .= '<div class="col-md-12" id="priceDiv">';
                                $html .= '<label class="form-label">'.__('Price').'</label>';
                                if(isset($price_array) && count($price_array) > 0)
                                {
                                    foreach($price_array as $key => $price_arr)
                                    {
                                        $price_label = isset($price_arr[$price_label_key]) ? $price_arr[$price_label_key] : '';
                                        $price_count = $key + 1;

                                        $html .= '<div class="row mb-3 align-items-center price price_'.$price_count.'">';
                                            $html .= '<div class="col-md-5 mb-1">';
                                                $html .= '<input type="text" name="price[price][]" class="form-control" placeholder="Enter Price" value="'.$price_arr['price'].'">';
                                                $html .= '<input type="hidden" name="price[priceID][]" value="'.$price_arr['id'].'">';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-6 mb-1">';
                                                $html .= '<input type="text" name="price[label][]" class="form-control" placeholder="Enter Price Label" value="'.$price_label.'">';
                                            $html .= '</div>';
                                            $html .= '<div class="col-md-1 mb-1">';
                                                $html .= '<a onclick="deleteItemPrice('.$price_arr['id'].','.$price_count.')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
                                            $html .= '</div>';
                                        $html .= '</div>';
                                    }
                                }
                            $html .= '</div>';
                        $html .= '</div>';

                        // Price Increment Button
                        $html .= '<div class="row mb-3 price_div priceDiv">';
                            $html .= '<div class="col-md-12">';
                                $html .= '<a onclick="addPrice(\'editItemModal\')" class="btn addPriceBtn btn-info text-white">'.__('Add Price').'</a>';
                            $html .= '</div>';
                        $html .= '</div>';

                        // Button for Show & Hide More Details
                        $html .= '<div class="row mb-3">';
                            $html .= '<div class="col-md-12 text-center">';
                                $html .= '<a class="btn btn-sm btn-primary" style="cursor: pointer" onclick="toggleMoreDetails(\'editItemModal\')" id="more_dt_btn">More Details.. <i class="bi bi-eye-slash"></i></a>';
                            $html .= '</div>';
                        $html .= '</div>';

                        $html .= '<div class="row" id="more_details" style="display: none;">';

                            // Discount Type
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="item_description">'.__('Discount Type').'</label>';
                                $html .= '<select name="discount_type" id="discount_type" class="form-control">';
                                    $html .= '<option value="percentage" ';
                                        if($item['discount_type'] == 'percentage')
                                        {
                                            $html .= 'selected';
                                        }
                                    $html .= '>'.__('Percentage %').'</option>';
                                    $html .= '<option value="fixed" ';
                                        if($item['discount_type'] == 'fixed')
                                        {
                                            $html .= 'selected';
                                        }
                                    $html .= '>'.__('Fixed Amount').'</option>';
                                $html .= '</select>';
                            $html .= '</div>';

                            // Discount
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="item_description">'.__('Discount').'</label>';
                                $html .= '<input type="number" name="discount" id="discount" class="form-control" value="'.$discount.'">';
                            $html .= '</div>';

                            // Description
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="item_description">'.__('Desription').'</label>';
                                $html .= '<textarea name="item_description" id="item_description" class="form-control item_description" rows="3">'.$item_desc.'</textarea>';
                            $html .= '</div>';

                            // Image Section
                            $html .= '<div class="col-md-12 mt-3 mb-2 d-flex flex-wrap" id="edit_images_div">';
                                if(isset($item->itemImages) && count($item->itemImages) > 0)
                                {
                                    foreach($item->itemImages as $key => $item_image)
                                    {
                                        $no = $key + 1;
                                        if(!empty($item_image['image']) && file_exists('public/client_uploads/items/'.$item_image['image']))
                                        {
                                            $html .= '<div class="inner-img edit_img_'.$no.'">';
                                                $html .= '<img src="'.asset('public/client_uploads/items/'.$item_image['image']).'" class="w-100 h-100">';
                                                $html .= '<a class="btn btn-sm btn-danger del-pre-btn" onclick="deleteItemImages('.$no.','.$item_image->id.')"><i class="fa fa-trash"></i></a>';
                                            $html .= '</div>';
                                        }
                                    }
                                }
                            $html .= '</div>';
                            $html .= '<div class="col-md-12 mb-2 d-flex flex-wrap" id="images_div">
                            </div>';
                            $html .= '<div class="col-md-12 mul-image" id="img-val"></div>';
                            $html .= '<div class="col-md-12">';
                                $html .= '<label class="form-label">'.__('Image').'</label>';
                            $html .= '</div>';
                            $html .= '<div class="col-md-12 mt-2 mul-image">';
                                $html .= '<div class="row">';
                                    $html .= '<div class="col-md-12">';
                                        $html .= '<div class="form-group">';
                                            $html .= '<div id="img-label"><label for="item_image">Upload Images</label></div>';
                                            $html .= '<input type="file" name="item_image" id="item_image" class="form-control" onchange="imageCropper(\'edit_item_form\',this)" style="display: none;">';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-md-12"><code class="img-upload-label">Upload Image in (400*400) Dimensions</code></div>';
                                $html .= '</div>';
                            $html .= '</div>';
                            $html .= '<div class="col-md-8 img-crop-sec mb-2" style="display: none">';
                                $html .= '<img src="" alt="" id="resize-image" class="w-100">';
                                $html .= '<div class="mt-3">';
                                    $html .= '<a class="btn btn-sm btn-success" onclick="saveCropper(\'edit_item_form\')">Save</a>';
                                    $html .= '<a class="btn btn-sm btn-danger" onclick="resetCropper()">Reset</a>';
                                    $html .= '<a class="btn btn-sm btn-secondary" onclick="cancelCropper(\'edit_item_form\')">Cancel</a>';
                                $html .= '</div>';
                            $html .= '</div>';
                            $html .= '<div class="col-md-4 img-crop-sec" style="display: none;">';
                                $html .= '<div class="preview" style="width: 200px; height:200px; overflow: hidden;margin: 0 auto;"></div>';
                            $html .= '</div>';

                            // Tags
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="tags">'.__('Tags').'</label>';
                                $html .= '<select name="tags[]" id="tags" class="form-select" multiple>';
                                if(count($tags) > 0)
                                {
                                    foreach($tags as $tag)
                                    {
                                        $html .= '<option value="'.$tag[$name_key].'"';
                                        if(in_array($tag[$name_key],$lang_tag_data))
                                        {
                                            $html .= 'selected';
                                        }
                                        $html .='>'.$tag[$name_key].'</option>';
                                    }
                                }
                                $html .= '</select>';
                            $html .= '</div>';

                            // Order Attributes
                            $html .= '<div class="col-md-12 mb-3">';
                                $html .= '<label class="form-label" for="options">'.__('Attributes').'</label>';
                                $html .= '<select name="options[]" id="options" class="form-select" multiple>';
                                    if(count($options) > 0)
                                    {
                                        foreach($options as $opt)
                                        {
                                            $html .= '<option value="'.$opt["id"].'"';
                                                if(in_array($opt["id"],$item_options))
                                                {
                                                    $html .= 'selected';
                                                }
                                            $html .='>'.$opt[$option_title_key].'</option>';
                                        }
                                    }
                                $html .= '</select>';
                            $html .= '</div>';

                            // Toggle Buttons
                            $html .= '<div class="col-md-12 mb-3 mt-1">';
                                $html .= '<div class="row">';

                                    $html .= '<div class="col-md-6 mark_new mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="mark_new" name="is_new" value="1" '.$item_is_new.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="mark_new" class="form-label">'.__('New').'</label>';
                                    $html .= '</div>';

                                    $html .= '<div class="col-md-6 pers_message mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="pers_message" name="pers_message" value="1" '.$item_pers_message.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="pers_message" class="form-label">'.__('Personalized Message').'</label>';
                                    $html .= '</div>';

                                    $html .= '<div class="col-md-6 day_special mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="day_special" name="day_special" value="1" '.$item_day_special.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="day_special" class="form-label">'.__('Day Special').'</label>';
                                    $html .= '</div>';

                                    $html .= '<div class="col-md-6 mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="publish" name="published" value="1" '.$item_published.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="publish" class="form-label">'.__('Published').'</label>';
                                    $html .= '</div>';

                                    $html .= '<div class="col-md-6 review_rating mb-3">';
                                        $html .= '<label class="switch me-2">';
                                            $html .= '<input type="checkbox" id="review_rating" name="review_rating" value="1" '.$review_rating.'>';
                                            $html .= '<span class="slider round">';
                                                $html .= '<i class="fa-solid fa-circle-check check_icon"></i>';
                                                $html .= '<i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>';
                                            $html .= '</span>';
                                        $html .= '</label>';
                                        $html .= '<label for="review_rating" class="form-label">'.__('Review & Rating').'</label>';
                                    $html .= '</div>';

                                $html .= '</div>';
                            $html .= '</div>';

                        $html .= '</div>';

                    $html .= '</form>';
                $html .= '</div>';
            $html .= '</div>';

        }

        return $html;

    }



    // Function Delete Item Image
    public function deleteItemImage(Request $request)
    {
        $image_id = $request->image_id;

        try
        {
            $item_image = ItemImages::find($image_id);

            $image = isset($item_image['image']) ? $item_image['image'] : '';

            if(!empty($image) && file_exists('public/client_uploads/items/'.$image))
            {
                unlink('public/client_uploads/items/'.$image);
            }

            ItemImages::where('id',$image_id)->delete();

            return response()->json([
                'success' => 1,
                'message' => 'Item Image has been Removed SuccessFully..',
            ]);

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error!',
            ]);
        }
    }



    // Function for Sorting Items.
    public function sorting(Request $request)
    {
        $sort_array = $request->sortArr;

        foreach ($sort_array as $key => $value)
        {
    		$key = $key+1;
    		Items::where('id',$value)->update(['order_key'=>$key]);
    	}

        return response()->json([
            'success' => 1,
            'message' => "Item has been Sorted SuccessFully....",
        ]);

    }


    // Functon for Delete Item Price
    public function deleteItemPrice(Request $request)
    {
        $price_id = $request->price_id;

        ItemPrice::where('id',$price_id)->delete();

        return response()->json([
            'success' => 1,
            'message' => 'Item Price has been Removed..',
        ]);
    }

}
