<script>
    $(document).ready(function(){

    $("a.switcher").bind("click", function(e){
        e.preventDefault();
        
        var theid = $(this).attr("id");
        var theproducts = $("ul#products");
        var classNames = $(this).attr('class').split(' ');
        
        var gridthumb = "<?php echo base_url('frontend/images'); ?>/products/grid-default-thumb.png";
        var listthumb = "<?php echo base_url('frontend/images'); ?>/products/list-default-thumb.png";
        
        if($(this).hasClass("active")) {
            // if currently clicked button has the active class
            // then we do nothing!
            return false;
        } else {
            // otherwise we are clicking on the inactive button
            // and in the process of switching views!

            if(theid == "gridview") {
                $(this).addClass("active");
                $("#listview").removeClass("active");
            
                $("#listview").children("img").attr("src","<?php echo base_url('frontend/images'); ?>/list-view.png");
            
                var theimg = $(this).children("img");
                theimg.attr("src","<?php echo base_url('frontend/images'); ?>/grid-view-active.png");
            
                // remove the list class and change to grid
                theproducts.removeClass("list");
                theproducts.addClass("grid");
            
                // update all thumbnails to larger size
                $("img.thumb").attr("src",gridthumb);
            }
            
            else if(theid == "listview") {
                $(this).addClass("active");
                $("#gridview").removeClass("active");
                    
                $("#gridview").children("img").attr("src","<?php echo base_url('frontend/images'); ?>/grid-view.png");
                    
                var theimg = $(this).children("img");
                theimg.attr("src","<?php echo base_url('frontend/images'); ?>/list-view-active.png");
                    
                // remove the grid view and change to list
                theproducts.removeClass("grid")
                theproducts.addClass("list");
                // update all thumbnails to smaller size
                $("img.thumb").attr("src",listthumb);
            } 
        }

    });
});
</script>
<div class="container-2">
                    <section class="content">
                       <?php if(!empty($products)) { ?>
                        <div class="ctrl">
                            
                            <span class="list-style-buttons">
                                <a href="#" id="gridview" class="switcher active"><img src="<?php echo base_url('frontend/images'); ?>/grid-view-active.png" alt="Grid"></a>
                                <a href="#" id="listview" class="switcher"><img src="<?php echo base_url('frontend/images'); ?>/list-view.png" alt="List"></a>
                            </span>
                        </div>
                        <ul id="products" class="clearfix grid">
                     
                      
    <?php foreach($products as $key => $value){
        if(SLUG_ACTIVE==0){
            $uri = get_base_url() . url_title(trim($value->product_name), 'dash', TRUE) . '-ps' . $value->id;
        }else{
            $uri = get_base_url() . $value->slug;
        }
         $image = is_null($value->image_name) ? base_url().'images/no-image.png' : base_url().'images/products/thumbnails/'.$value->image_name;

              //      $imgbig = base_url().'images/products/'.$value->image_name;

                    $value_name = limit_text($value->product_name, 100);

                    $value_category = limit_text($value->category, 100);

                    $price = $value->price > 0 ? get_price_in_vnd($value->price) . ' ₫' : get_price_in_vnd($value->price);        

                    $price_old = $value->price_old > 0 ? get_price_in_vnd($value->price_old) . ' ₫' : 0;

                    $price_count = $value->price_old - $value->price;

                    $price_discount = $price_count > 0 ? get_price_in_vnd($price_count) . ' ₫' : 0;

                    $saleoff = $value->price_old > 0 ? ($price_count/$value->price_old)*100 : 0;

                    $saleoff = round($saleoff,0);
     

    ?>
                
                            <li class="da-thumbs">
                                <div class="product-thumb-hover">
                                    <section class="left">
                                        <img src="<?php echo base_url('frontend/images'); ?>/products/product-1.jpg" alt="">
                                        <p class="sale">Sale</p>
                                        <article class="da-animate da-slideFromTop" style="display: block;">
                                            <h3><?php echo $value->product_name; ?></h3>
                                            <p>
                                            <a href="product-detail.html" class="link tip" original-title="View Detail"></a>&nbsp;
                                            <a href="cart.html" class="cart tip" original-title="Add to cart"></a>&nbsp;&nbsp;
                                            <a href="<?php echo base_url('frontend/images'); ?>/preview/work_1_l.jpg" rel="prettyPhoto[gallery1]" class="zoom tip" original-title="Zoom"></a></p>
                                        </article>
                                    </section>
                                </div>
                                <section class="center">
                                    <h3><?php echo $value->product_name; ?></h3>
                                    <em>Danh Mục: <a href="#"><?php echo $value->category; ?></a></em>
                                </section>
                                <section class="right">
                                    <span class="price"><small>$320.00</small>&nbsp;&nbsp; $95.00</span>
                                    <ul class="menu-button">
                                        <li><a href="cart.html" class="cart tip" original-title="Add to Cart"></a></li>
                                        <li><a href="<?php echo base_url('frontend/images'); ?>/preview/work_1_l.jpg" rel="prettyPhoto[gallery1]" class="zoom tip" original-title="Zoom"></a></li>
                                        <li><a href="wishlist.html" class="wishlist tip" original-title="Add to Wishlist"></a></li>
                                        <li><a href="compare.html" class="compare tip" original-title="Compare"></a></li>
                                        <li><a href="product-detail.html" class="link tip" original-title="View Detail"></a></li>
                                    </ul>
                                </section>
                            </li>
                            
<?php } ?>
                          
                        </ul><!--end:products-->
                          <div id="pagination" class="paging">
                            <?php echo $this->pagination->create_links(); ?>
                            </div>
                            <?php }else{echo '<h4 class="alert alert-info">' . __('IP_comming_soon') . '</h4>';} ?>
                    </section>

                    <aside class="sidebar">
                        <?php //  echo $this->load->view('common/side-filter'); ?>
                        <?php   echo $this->load->view('common/side-products'); ?>
                        <?php   echo $this->load->view('common/side-news'); ?>
                        <?php   echo $this->load->view('common/side-customer'); ?>
                    </aside> 
                </div>