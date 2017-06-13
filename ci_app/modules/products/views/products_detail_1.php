<?php if(!empty($product)) { 



    if(SLUG_ACTIVE==0){



        $uri = get_base_url() . url_title(trim($product->product_name), 'dash', TRUE) . '-ps' . $product->id;    



        $uri2 = get_base_url() . url_title(trim($category), 'dash', TRUE) . '-p' . $product->categories_id;



    }else{



        $uri = get_base_url() . $product->slug;



        $uri2 = get_base_url() . $category_slug;



    }



//    $image = is_null($product->image_name) ? base_url().'images/no-image.png' : base_url().'images/products/'.$product->image_name;



//    $image_thumb = is_null($product->image_name) ? base_url().'images/no-image.png' : base_url().'images/products/thumbnails/'.$product->image_name;



    $price = $product->price != 0 ? get_price_in_vnd($product->price) . ' ₫' : get_price_in_vnd($product->price);



    $price_old = $product->price_old != 0 ? get_price_in_vnd($product->price_old) . ' ₫' : 0;



    $summary = limit_text($product->summary, 1000);



//    $specifications = $product->specifications;



    $description = $product->description;



    $code = $product->code;



    echo form_hidden('id', $product->id);



    echo form_hidden('addUri', get_uri_by_lang(get_language(),'cart'));



    echo form_hidden('lang', get_language());



?>



<div class="box">



    <div class="title">



        <a href="<?php echo $uri2; ?>" title="<?php echo $category; ?>" class="heading"><?php echo $category; ?></a>



    </div>



    <div class="row">



        <div class="col-sm-7 image">



            <?php if(!empty($products_images)){ ?>



            <div class="html5gallery" data-skin="light" data-width="230" data-height="300" data-slideshadow="false" data-resizemode="fill" data-responsive="true">



            <?php foreach($products_images as $key => $value){ 



                $value_image = is_null($value->image_name) ? base_url().'images/no-image.png' : base_url().'images/products/'.$value->image_name;



                $value_image_thumb = is_null($value->image_name) ? base_url().'images/no-image.png' : base_url().'images/products/thumbnails/'.$value->image_name;



            ?>



                <a href="<?php echo $value_image; ?>" title="<?php echo $product->product_name; ?>">



                    <img title="<?php echo $product->product_name; ?>" src="<?php echo $value_image_thumb; ?>" alt="<?php echo $product->product_name; ?>">



                </a>



            <?php } ?>



            </div>



            <?php }else{ ?>



            <img title="<?php echo $product->product_name; ?>" src="/images/no-image.png" />



            <?php } ?>



        </div>



        <div class="col-sm-5 infomation">



            <h1><?php echo $product->product_name; ?></h1>



            <?php // if(!empty($product->code)){ ?>



            <!--<div><label><?php // echo __('IP_code'); ?>: </label><?php // echo $code; ?></div>-->



            <?php // } ?>



            <div>



                <?php if(!empty($price) && $price <> 0){ ?>



                    <span class="price"><?php echo $price; ?></span>



                <?php } ?>



                <?php if(!empty($price_old) && $price_old <> 0){ ?>



                    <span class="price_old"><?php echo $price_old; ?></span>



                <?php } ?>



            </div>



            <?php if(!empty($product->trademark_name)){ ?>



            <div><label><?php echo __('IP_trademark'); ?>: </label><?php echo $product->trademark_name; ?></div>



            <?php } ?>



            <?php if(!empty($product->origin_name)){ ?>



            <div><label><?php echo __('IP_origin'); ?>: </label><?php echo $product->origin_name; ?></div>



            <?php } ?>



            <?php if(!empty($product->state_name)){ ?>



            <div><label><?php echo __('IP_state'); ?>: </label><?php echo $product->state_name; ?></div>



            <?php } ?>



            <?php if(!empty($product->summary)){ ?>



            <div><h2><?php echo $summary; ?></h2></div>



            <?php } ?>



            



            <?php // if(!empty($product->colors)){ ?>



<!--            <div><label><?php // echo __('IP_color'); ?>: </label>



            <?php 



//                $colors_arr = @explode(",", $product->colors);



//                $colors = modules::run('products/get_colors',$colors_arr);



//                foreach($colors as $k => $v){



            ?>



                <span style="width:15px;height:15px;border:1px solid #000;background:#<?php // echo $k; ?>;cursor:pointer;margin-right:5px;display:inline-block;vertical-align:middle;" title="<?php // echo $v; ?>"></span>



            <?php // } ?>



            </div>    -->



            <?php // } ?>



            



            <div>



                <a href="/images/bang_co_giay.jpeg" title="Bảng cỡ giày" class="imagebox tablesize" onclick="imagebox();">Bảng cỡ giày</a>



            </div>



            



            <?php if(!empty($product->size)){ ?>



            <div>



            <?php 



                $size_arr = @explode(",", $product->size);



                $size = modules::run('products/get_size',$size_arr);



                foreach($size as $k => $v){



            ?>



                <span class="sizebox" title="<?php echo $v; ?>"><?php echo $v; ?></span>



            <?php } ?>



            </div>



            <?php } ?>



            



            <?php if($product->price > 0){ ?>



            <div>



                <button type="button" class="addtocart" id="addtocart"><i class="fa fa-shopping-cart"></i>&nbsp;<?php echo __('IP_products_add_cart'); ?></button>



            </div>



            <?php }else{ ?>



            <form method="post" action="<?php echo base_url().CONTACT_HOME_URL; ?>">



                <input type="hidden" name="id" value="<?php echo $product->id; ?>" />



                <input type="submit" class="contact_order_submit" value="<?php echo __('IP_products_contact'); ?>" />



            </form>



            <?php } ?>



            



            <?php $config = get_cache('configurations_' .  get_language()); 



                if(!empty($config)){



            ?>



            <div class="order_tel">



                Hoặc gọi <a href="tel:<?php echo $config['telephone'] ?>"><span><?php echo $config['telephone'] ?></span></a> <br />để mua hàng nhanh nhất



            </div>



            <?php } ?>



            



            <?php echo $this->load->view('quick_support'); ?>



            



        </div>



    </div>



    



    <div role="tabpanel" class="content">







        <!-- Nav tabs -->



        <ul class="nav nav-tabs" role="tablist">



          <!--<li role="presentation" class="active"><a href="#specifications" aria-controls="specifications" role="tab" data-toggle="tab"><?php // echo __('IP_products_specifications'); ?></a></li>-->



          <li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab"><?php echo __('IP_products_infomation'); ?></a></li>



        </ul>







        <!-- Tab panes -->



        <div class="tab-content">



          <!--<div role="tabpanel" class="tab-pane specifications active" id="specifications"><?php // echo $specifications; ?></div>-->



          <div role="tabpanel" class="tab-pane description active" id="description"><?php echo $description; ?></div>



        </div>







    </div>



    



<!--    <div class="description">



        <h5><?php // echo __('IP_detail_infomation'); ?></h5>



        <p><?php // echo $description; ?></p>



    </div>-->







    <?php if(!empty($tags)) { ?>



    <div class="post-tags">



        <?php foreach($tags as $key => $tag){



            $taglink = get_url_by_lang(get_language(),'products_tags').'/'.url_title(trim($tag), 'dash', TRUE);



        ?>



        <a rel="tag" href="<?php echo $taglink; ?>" title="<?php echo $tag; ?>"><?php echo $tag; ?></a>



        <?php } ?>



    </div>



    <?php } ?>



        



    <?php // if(isset($products_same)){echo $products_same;} ?>







    <div class="post-share">



        <span class="share-text">



            Share 



        </span>



        <!--sharing-->



        <div class="sharing">



            <div class="sharing_tab sharing_g">



                <script src="https://apis.google.com/js/platform.js" async defer></script>



                <g:plusone size="medium" data-annotation="bubble"></g:plusone>



            </div>



            <div class="sharing_tab sharing_t">



                <a href="https://twitter.com/share" class="twitter-share-button" data-via="">Tweet</a>



                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>



            </div>



            <div class="sharing_tab sharing_f">



                



                <div class="fb-like" data-href="<?php echo current_url(); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>



            </div>



        </div>



    </div>







    <div id="comments" class="comments-area">



        <div id="respond" class="comment-respond">



            <!--facebook comment-->



            <div class="fb-comments" data-href="<?php echo current_url(); ?>" data-width="100%" data-numposts="10" data-colorscheme="light" style="margin: 20px 0"></div>



        </div>



    </div>







</div>



<?php }else {echo '<h4 class="alert alert-info">' . __('IP_comming_soon') . '</h4>';} ?>