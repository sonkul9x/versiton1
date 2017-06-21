<?php  echo $this->load->view('common/header'); ?>
    <div id="page_wrap">
        <?php // echo $this->load->view('common/sk-top'); ?>
        <div id="container">
             <?php  echo $this->load->view('common/sk-top_nav'); ?>
            <div class="content-wrap">
                <?php  echo $this->load->view('common/sk-top_slide'); ?>
                <?php  echo $this->load->view('common/sk-intro'); ?>
                <div class="container-2">
                    <section class="content">
                        <?php   echo $this->load->view('common/sk-product-featured'); ?>
                        <?php   echo $this->load->view('common/sk-product-news'); ?>
                         <?php echo $this->load->view('common/sk-ads'); ?>
                        <?php   echo $this->load->view('common/sk-product-sale'); ?>
                    </section>
                    <aside class="sidebar">

                        <?php //  echo $this->load->view('common/side-filter'); ?>
                        <?php   echo $this->load->view('common/side-products'); ?>
                        <?php   echo $this->load->view('common/side-news'); ?>
                        <?php   echo $this->load->view('common/side-customer'); ?>
                    </aside> 
                </div><!--end:container-2-->
                <?php   echo $this->load->view('common/sk-footer-top'); ?>
            </div><!--end:content-wrap-->
<?php echo $this->load->view('common/footer'); ?>

