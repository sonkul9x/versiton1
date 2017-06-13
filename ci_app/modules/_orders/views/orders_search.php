<div class="box">
    <h3><?php echo $category; ?></h3>
    <div class="divider"></div>
    <ol class="breadcrumb">
        <li><a href="<?php echo get_base_url(); ?>" title="<?php echo __('IP_default_company'); ?>"><?php echo __('IP_home_page'); ?></a></li>
        <li class="active"><?php echo limit_text($category,60); ?></li>
    </ol>
    <?php if(!empty($faqs)) { ?>
    <div class="list">
        <h4 class="alert alert-warning"><?php echo __('IP_search_total_result'); ?> <strong><?php echo $total_row; ?></strong>. <?php if(isset($keyword) && !empty($keyword)){ ?> <?php echo __('IP_search_keyword'); ?>: <strong><?php echo $keyword; ?></strong><?php } ?></h4>
        <?php foreach($faqs as $key => $faq){
        $uri = get_base_url() . url_title(trim($faq->title), 'dash', TRUE) . '-qs' . $faq->id;
        $image_thumbnail = (isset($faq->thumbnail) && $faq->thumbnail <> '')?base_url().$faq->thumbnail:base_url().'images/no-image.png';
        ?>
        <div class="row list_item">
            <div class="col-sm-3 list_thumb">
                <a href="<?php echo $uri; ?>" title="<?php echo $faq->title; ?>"><img alt="<?php echo $faq->title; ?>" src="<?php echo $image_thumbnail; ?>" title="<?php echo $faq->title; ?>" ></a>
            </div>
            <div class="col-sm-9 list_content">
                <p class="list_title"><a href="<?php echo $uri; ?>" title="<?php echo $faq->title; ?>"><?php echo $faq->title; ?></a><span>(<?php echo get_date_by_lang(get_language(), $faq->created_date); ?>)</span></p>
                <p class="list_summary"><?php echo limit_text($faq->summary, 200); ?></p>
                <a class="viewmore" href="<?php echo $uri; ?>" title="<?php echo $faq->title; ?>"><?php echo __('IP_view_detail'); ?></a>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="paging">
        <?php echo $this->pagination->create_links(); ?>
    </div>
    <div class="clearfix"></div>
    <?php }else { ?>
    <div class="box-content">
        <h4 class="alert alert-warning"><?php echo __('IP_search_no_result'); ?></h4>
    </div>
    <?php } ?>
</div>
