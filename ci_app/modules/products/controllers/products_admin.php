<?phpclass Products_Admin extends MY_Controller{    function __construct()    {        parent::__construct();        modules::run('auth/auth/validate_login', $this->router->fetch_module());        $this->_layout = 'admin_ui/layout/main';        $this->_view_data['url'] = PRODUCTS_ADMIN_BASE_URL;    }    /**     * Liệt kê danh sách các sản phẩm của chủ gian hàng hoặc danh sách tất cả     * sản phẩm nếu người đăng nhập là quản trị     *     * @return type     */    function browse($para1 = DEFAULT_LANGUAGE, $para2 = 1)    {        $options = array('lang' => switch_language($para1), 'page' => $para2);        $options = array_merge($options, $this->_prepare_search($options));        $options['products_sort_type'] = $this->phpsession->get('products_sort_type');        $this->phpsession->save('product_lang', $options['lang']);        $options['is_admin'] = TRUE;        if ($options['cat_id'] != DEFAULT_COMBO_VALUE) {            $cat_array = $this->products_model->sql_get_by_cat($options['cat_id']);            $cat_array .= $options['cat_id'];            $options['cat_array'] = @explode(',', $cat_array);        }        $total_row = $this->products_model->get_products_count($options);        $total_pages = (int)($total_row / PRODUCTS_ADMIN_PRODUCT_PER_PAGE);        if ($total_pages * PRODUCTS_ADMIN_PRODUCT_PER_PAGE < $total_row) $total_pages++;        if ((int)$options['page'] > $total_pages) $options['page'] = $total_pages;        $options['offset'] = $options['page'] <= DEFAULT_PAGE ? DEFAULT_OFFSET : ((int)$options['page'] - 1) * PRODUCTS_ADMIN_PRODUCT_PER_PAGE;        $options['limit'] = PRODUCTS_ADMIN_PRODUCT_PER_PAGE;        $config = prepare_pagination(            array(                'total_rows' => $total_row,                'per_page' => $options['limit'],                'offset' => $options['offset'],                'js_function' => 'change_page_admin'            )        );        $this->pagination->initialize($config);        $options['page_links'] = $this->pagination->create_ajax_links();        $options['products'] = $this->products_model->get_products($options);        $options['total_rows'] = $total_row;        $options['total_pages'] = $total_pages;        $options['page'] = $options['page'];        $options['product_name'] = isset($options['product_name']) ? $options['product_name'] : FALSE;// REM 2014//        $options['categories_array'] = $this->products_categories_model->get_categories_array();        $options['filter'] = $options['filter'];        if ($options['lang'] <> DEFAULT_LANGUAGE) {            $options['uri'] = PRODUCTS_ADMIN_BASE_URL . '/' . $options['lang'];        } else {            $options['uri'] = PRODUCTS_ADMIN_BASE_URL;        }        if (isset($options['error']) || isset($options['succeed']) || isset($options['warning']))            $options['options'] = $options;        // Chuan bi du lieu chinh de hien thi        $this->_view_data['main_content'] = $this->load->view('admin/products/product_list', $options, TRUE);        // Chuan bi cac the META        $this->_view_data['title'] = 'Quản lý sản phẩm' . DEFAULT_TITLE_SUFFIX;        // Tra lai view du lieu cho nguoi su dung        $this->load->view($this->_layout, $this->_view_data);    }    private function _prepare_search($options = array())    {        $view_data = array();        // nếu submit        if ($this->is_postback()) {            $this->phpsession->save('product_name_search', $this->db->escape_str($this->input->post('product_name')));            $view_data['search'] = $this->phpsession->get('product_name_search');            $this->phpsession->save('categories_search_id', $this->input->post('categories_id'));            $view_data['categories_combo'] = $this->products_categories_model->get_categories_combo(array('combo_name' => 'categories_id', 'categories_id' => $this->input->post('categories_id'), 'lang' => $options['lang'], 'extra' => 'class="btn" style="max-width: 25%;"'));            //search with lang            $options['lang'] = $this->input->post('lang');        } else {            $view_data['search'] = $this->phpsession->get('product_name_search');            if (!($this->phpsession->get('categories_search_id'))) $this->phpsession->save('categories_search_id', DEFAULT_COMBO_VALUE);            $view_data['categories_combo'] = $this->products_categories_model->get_categories_combo(array('combo_name' => 'categories_id', 'categories_id' => $this->phpsession->get('categories_search_id'), 'lang' => $options['lang'], 'extra' => 'class="btn" style="max-width: 25%;"'));        }        $view_data['lang_combobox'] = $this->utility_model->get_lang_combo(array('lang' => $options['lang'], 'extra' => 'onchange="javascript:change_lang();" class="btn"'));        $view_data['sort_combobox'] = $this->products_model->get_products_sort_combobox(array('sort_combobox' => $this->phpsession->get('products_sort_type'), 'extra' => 'onchange="javascript:change_sort();" class="btn"'));        $options['keyword'] = $this->phpsession->get('product_name_search');        $options['cat_id'] = $this->phpsession->get('categories_search_id');        $options['filter'] = $this->load->view('admin/products/search_product_form', $view_data, TRUE);        return $options;    }    public function do_sort_products_list()    {        if ($this->is_postback()) {            $this->phpsession->save('products_sort_type', $this->input->post('sort_combobox'));        } else {            $this->phpsession->save('products_sort_type', '');        }    }    /*     * Thêm mới sản phẩm     */    function add()    {        $options = array();        $this->form_validation->set_rules('product_name', 'Tên sản phẩm', 'trim|required|xss_clean|max_length[255]');        if ($this->form_validation->run()) {            $data = array(                'product_name' => $this->input->post('product_name'),                'status' => STATUS_ACTIVE,                'created_date' => date('Y-m-d H:i:s'),                'updated_date' => date('Y-m-d H:i:s'),                'lang' => $this->phpsession->get("product_lang"),                'creator' => $this->phpsession->get('user_id'),                'editor' => $this->phpsession->get('user_id'),            );            $position_add = $this->products_model->position_to_add_products(array('lang' => $data['lang']));            $data['position'] = $position_add;            $product_id = $this->products_model->insert($data);            //redirect(PRODUCTS_ADMIN_EDIT_URL .'/'. $product_id);            return $this->edit(array('id' => $product_id));        } else {            $options['error'] = validation_errors();            $options['header'] = 'Thêm sản phẩm';            if (isset($options['error']) || isset($options['succeed']) || isset($options['warning']))                $options['options'] = $options;            // Chuan bi du lieu chinh de hien thi            $this->_view_data['main_content'] = $this->load->view('admin/products/add_product_form', $options, TRUE);            // Chuan bi cac the META            $this->_view_data['title'] = 'Thêm sản phẩm' . DEFAULT_TITLE_SUFFIX;            // Tra lai view du lieu cho nguoi su dung            $this->load->view($this->_layout, $this->_view_data);        }        return FALSE;    }    /**     * Thực hiện việc sửa nội dung sản phẩm.     *     */    function edit($options = array())    {        $this->output->link_js('/powercms/scripts/uploadify/jquery.uploadify-3.1.min.js');        // $this->output->link_js('/plugins/jquery/ui.sortable.js');        $this->output->javascripts('uploadify();');        $this->output->javascripts('setup_moveable();');        $this->output->javascripts('set_hover_img();');        if (!$this->is_postback()) redirect(PRODUCTS_ADMIN_BASE_URL);        if ($this->is_postback() && !$this->input->post('from_list')) {            if (!$this->_do_edit_product())                $options['error'] = validation_errors();            if (isset($options['error'])) $options['options'] = $options;        }        if (!isset($options['id'])) {            $options['id'] = NULL;        }        $options += $this->_get_edit_product_form_data(array('id' => $options['id']));        // Chuan bi du lieu chinh de hien thi        $this->_view_data['main_content'] = $this->load->view('admin/products/edit_product_form', $options, TRUE);        // Chuan bi cac the META        $this->_view_data['title'] = 'Sửa sản phẩm' . DEFAULT_TITLE_SUFFIX;        // Tra lai view du lieu cho nguoi su dung        $this->load->view($this->_layout, $this->_view_data);    }    private function _get_edit_product_form_data($options = array())    {        if (isset($options['id']))            $product_id = $options['id'];        else $product_id = $this->input->post('id');        $view_data = array();        // Get from DB        if ($this->input->post('from_list')) {            $products = $this->products_model->get_products(array('id' => $product_id, 'is_admin' => TRUE));            if (!is_object($products)) show_404();            $view_data['product_name'] = $products->product_name;            if (SLUG_ACTIVE > 0) {                $view_data['slug'] = slug_character_remove($products->slug);            }            $view_data['summary'] = $products->summary;            $view_data['code'] = $products->code;            $price_unit = get_unit_from_price($products->price);            $view_data['price'] = str_replace('.00', '', $price_unit['price']);            $price_unit_old = get_unit_from_price($products->price_old);            $view_data['price_old'] = str_replace('.00', '', $price_unit_old['price']);            $view_data['unit'] = $this->utility_model->get_product_units_combo(array('combo_name' => 'unit', 'unit' => $price_unit['unit'], 'extra' => 'class="btn"'));            $view_data['product_unit'] = $this->products_units_model->get_units_combo(array('units' => $products->unit_id, 'extra' => 'class="btn"'));            $view_data['unit_old'] = $this->utility_model->get_product_units_combo(array('combo_name' => 'unit_old', 'unit_old' => $price_unit_old['unit'], 'extra' => 'class="btn"'));            $view_data['product_unit_old'] = $this->products_units_model->get_units_combo(array('units_old' => $products->unit_id_old, 'extra' => 'class="btn"'));            $view_data['description'] = $products->description;            $view_data['manufacturer'] = $products->manufacturer;            $view_data['specifications'] = $products->specifications;            $view_data['typical_product'] = $this->utility_model->get_special_product_combo(array('combo_name' => 'typical', 'typical' => $products->typical, 'extra' => 'class="btn"'));            $view_data['new_product'] = $this->utility_model->get_special_product_combo(array('combo_name' => 'new', 'new' => $products->new, 'extra' => 'class="btn"'));            $view_data['top_sell_product'] = $this->utility_model->get_special_product_combo(array('combo_name' => 'top_seller', 'top_seller' => $products->top_seller, 'extra' => 'class="btn"'));            $view_data['categories'] = $this->products_categories_model->get_categories_combo(array('categories_combobox' => $products->categories_id, 'lang' => $products->lang, 'extra' => 'class="btn"'));            $view_data['products_origin'] = $this->products_origin_model->get_products_origin_combo(array('products_origin' => $products->origin_id, 'extra' => 'class="btn"'));            $view_data['products_trademark'] = $this->products_trademark_model->get_products_trademark_combo(array('products_trademark' => $products->trademark_id, 'extra' => 'class="btn"'));            $view_data['tags'] = $products->tags;            $view_data['meta_title'] = $products->meta_title;            $view_data['meta_keywords'] = $products->meta_keywords;            $view_data['meta_description'] = $products->meta_description;            $view_data['link_demo'] = $products->link_demo;            $view_data['lang_combobox'] = $this->utility_model->get_lang_combo(array('lang' => $products->lang, 'extra' => 'onchange="javascript:get_categories_by_lang();" class="btn"'));            $colors = explode(',', $products->colors);            $view_data['products_color'] = $this->products_color_model->get_products_color_combo(array('products_color[]' => $colors, 'extra' => 'multiple="multiple" class="btn" size="3" style="height: 120px; width: 300px; padding-left: 5px;"'));            $view_data['products_state'] = $this->products_state_model->get_products_state_combo(array('products_state' => $products->state_id, 'extra' => 'class="btn"'));            $size = explode(',', $products->size);            $view_data['products_size'] = $this->products_size_model->get_products_size_combo(array('products_size[]' => $size, 'extra' => 'multiple="multiple" class="btn" size="3" style="height: 120px; width: 300px; padding-left: 5px;"'));            $view_data['products_style'] = $this->products_style_model->get_products_style_combo(array('products_style' => $products->style_id, 'extra' => 'class="btn"'));            $view_data['products_material'] = $this->products_material_model->get_products_material_combo(array('products_material' => $products->material_id, 'extra' => 'class="btn"'));        } // Get from submit        else {            $view_data['product_name'] = $this->input->post('product_name', TRUE);            if (SLUG_ACTIVE > 0) {                $view_data['slug'] = my_trim($this->input->post('slug', TRUE));            }            $view_data['summary'] = '';            $view_data['code'] = $this->input->post('code', TRUE);            $view_data['description'] = '';            $view_data['manufacturer'] = '';            $view_data['specifications'] = '';            $view_data['price'] = $this->input->post('price', TRUE);            $view_data['price_old'] = $this->input->post('price_old', TRUE);            $view_data['unit'] = $this->utility_model->get_product_units_combo(array('combo_name' => 'unit', 'unit' => $this->input->post('unit'), 'extra' => 'class="btn"'));            $view_data['product_unit'] = $this->products_units_model->get_units_combo(array('units' => $this->input->post('units', TRUE), 'extra' => 'class="btn"'));            $view_data['unit_old'] = $this->utility_model->get_product_units_combo(array('combo_name' => 'unit_old', 'unit_old' => $this->input->post('unit_old'), 'extra' => 'class="btn"'));            $view_data['product_unit_old'] = $this->products_units_model->get_units_combo(array('units_old' => $this->input->post('units_old', TRUE), 'extra' => 'class="btn"'));            $view_data['typical_product'] = $this->utility_model->get_special_product_combo(array('combo_name' => 'typical', 'typical' => $this->input->post('typical'), 'extra' => 'class="btn"'));            $view_data['new_product'] = $this->utility_model->get_special_product_combo(array('combo_name' => 'new', 'new' => $this->input->post('new'), 'extra' => 'class="btn"'));            $view_data['top_sell_product'] = $this->utility_model->get_special_product_combo(array('combo_name' => 'top_seller', 'top_seller' => $this->input->post('top_seller'), 'extra' => 'class="btn"'));            $view_data['categories'] = $this->products_categories_model->get_categories_combo(array('categories_combobox' => $this->input->post('categories_combobox'), 'lang' => $this->input->post('lang'), 'extra' => 'class="btn"'));            $view_data['products_origin'] = $this->products_origin_model->get_products_origin_combo(array('products_origin' => $this->input->post('products_origin'), 'extra' => 'class="btn"'));            $view_data['products_trademark'] = $this->products_trademark_model->get_products_trademark_combo(array('products_trademark' => $this->input->post('products_trademark'), 'extra' => 'class="btn"'));            $view_data['tags'] = $this->input->post('tags', TRUE);            $view_data['meta_title'] = $this->input->post('meta_title', TRUE);            $view_data['meta_keywords'] = $this->input->post('meta_keywords', TRUE);            $view_data['meta_description'] = $this->input->post('meta_description', TRUE);            $view_data['link_demo'] = $this->input->post('link_demo', TRUE);            $view_data['lang_combobox'] = $this->utility_model->get_lang_combo(array('lang' => $this->phpsession->get("product_lang"), 'extra' => 'onchange="javascript:get_categories_by_lang();" class="btn"'));            $colors = $this->input->post('products_color', TRUE);            $view_data['products_color'] = $this->products_color_model->get_products_color_combo(array('products_color[]' => $colors, 'extra' => 'multiple="multiple" class="btn" size="3" style="height: 120px; width: 300px; padding-left: 5px;"'));            $view_data['products_state'] = $this->products_state_model->get_products_state_combo(array('products_state' => $this->input->post('products_state'), 'extra' => 'class="btn"'));            $size = $this->input->post('products_size', TRUE);            $view_data['products_size'] = $this->products_size_model->get_products_size_combo(array('products_size[]' => $size, 'extra' => 'multiple="multiple" class="btn" size="3" style="height: 120px; width: 300px; padding-left: 5px;"'));            $view_data['products_style'] = $this->products_style_model->get_products_style_combo(array('products_style' => $this->input->post('products_style'), 'extra' => 'class="btn"'));            $view_data['products_material'] = $this->products_material_model->get_products_material_combo(array('products_material' => $this->input->post('products_material'), 'extra' => 'class="btn"'));        }        $view_data['product_id'] = $product_id;        $this->phpsession->save('product_id', $product_id);        //$view_data['uri']           = PRODUCTS_ADMIN_EDIT_URL;        $view_data['submit_uri'] = PRODUCTS_ADMIN_EDIT_URL;        $view_data['header'] = 'Sửa thông tin sản phẩm';        $view_data['button_name'] = 'Lưu dữ liệu';        $view_data['images'] = $this->get_products_images();        $view_data['scripts'] = $this->_get_scripts();        return $view_data;    }    /**     * Thực hiện việc thêm sản phẩm vào trong DB     * @return type     */    private function _do_edit_product()    {        if ($this->input->post('btnSubmit') === 'Lưu dữ liệu') {            $this->form_validation->set_rules('product_name', 'Tên sản phẩm', 'trim|required|max_length[255]|xss_clean');            if (SLUG_ACTIVE > 0) {                $this->form_validation->set_rules('slug', 'Slug', 'trim|required|xss_clean|max_length[1000]');            }            $this->form_validation->set_rules('categories_combobox', 'Phân loại sản phẩm', 'required|is_not_default_combo');            $this->form_validation->set_rules('price', 'Giá tiền', 'trim|max_length[11]|xss_clean|is_numeric');            $this->form_validation->set_rules('price_old', 'Giá cũ', 'trim|max_length[11]|xss_clean|is_numeric');            $this->form_validation->set_rules('summary', 'Mô tả ngắn', 'trim|xss_clean');            $this->form_validation->set_rules('code', 'Mã sản phẩm', 'trim|xss_clean|max_length[255]');            $this->form_validation->set_rules('description', 'Thông tin sản phẩm', 'trim|min_length[10]');            $this->form_validation->set_rules('manufacturer', 'Thông tin nhà sản xuất', 'trim|min_length[10]');            $this->form_validation->set_rules('specifications', 'Thông số kỹ thuật', 'trim|min_length[10]');            $this->form_validation->set_rules('products_origin', 'Xuất xứ', 'is_not_default_combo');            $this->form_validation->set_rules('products_trademark', 'Thương hiệu', 'is_not_default_combo');            $this->form_validation->set_rules('meta_title', 'Meta title', 'trim|xss_clean|max_length[255]');            $this->form_validation->set_rules('meta_keywords', 'Meta keywords', 'trim|xss_clean|max_length[255]');            $this->form_validation->set_rules('meta_description', 'Meta description', 'trim|xss_clean');            $this->form_validation->set_rules('link_demo', 'Link demo', 'trim|xss_clean|max_length[500]');            $this->form_validation->set_rules('tags', 'Tags', 'trim|xss_clean');            $this->form_validation->set_rules('products_colors[]', 'Màu sắc', 'is_not_default_combo');            $this->form_validation->set_rules('products_size[]', 'Kích cỡ', 'is_not_default_combo');            $this->form_validation->set_rules('products_state', 'Tình trạng sản phẩm', 'is_not_default_combo');            $this->form_validation->set_rules('products_style', 'Kiểu dáng', 'is_not_default_combo');            $this->form_validation->set_rules('products_material', 'Chất liệu', 'is_not_default_combo');            if ($this->form_validation->run($this)) {                $content = str_replace('&lt;', '<', $this->input->post('description'));                $content = str_replace('&gt;', '>', $content);                $manufacturer = str_replace('&lt;', '<', $this->input->post('manufacturer'));                $manufacturer = str_replace('&gt;', '>', $manufacturer);                $specifications = str_replace('&lt;', '<', $this->input->post('specifications'));                $specifications = str_replace('&gt;', '>', $specifications);                $colors = ',' . @implode(",", $this->input->post('products_color', TRUE)) . ',';                $size = ',' . @implode(",", $this->input->post('products_size', TRUE)) . ',';                $product_data = array(                    'id' => $this->input->post('id'),                    'product_name' => strip_tags($this->input->post('product_name', TRUE)),                    'updated_date' => date('Y-m-d H:i:s'),                    'description' => $content,                    'manufacturer' => $manufacturer,                    'specifications' => $specifications,                    'price' => $this->input->post('price') * $this->input->post('unit'),                    'price_old' => $this->input->post('price_old') * $this->input->post('unit_old'),                    'summary' => my_trim($this->input->post('summary', TRUE)),                    'code' => my_trim($this->input->post('code', TRUE)),                    'typical' => $this->input->post('typical', TRUE),                    'new' => $this->input->post('new', TRUE),                    'top_seller' => $this->input->post('top_seller', TRUE),                    'categories_id' => $this->input->post('categories_combobox', TRUE),                    'origin_id' => $this->input->post('products_origin', TRUE),                    'trademark_id' => $this->input->post('products_trademark', TRUE),                    'meta_title' => $this->input->post('meta_title', TRUE),                    'meta_keywords' => $this->input->post('meta_keywords', TRUE),                    'meta_description' => $this->input->post('meta_description', TRUE),                    'tags' => $this->input->post('tags', TRUE),                    'lang' => $this->input->post('lang', TRUE),                    'unit_id' => $this->input->post('units', TRUE),                    'unit_id_old' => $this->input->post('units_old', TRUE),                    'link_demo' => $this->input->post('link_demo', TRUE),                    'editor' => $this->phpsession->get('user_id'),                    'colors' => $colors,                    'size' => $size,                    'state_id' => $this->input->post('products_state', TRUE),                    'style_id' => $this->input->post('products_style', TRUE),                    'material_id' => $this->input->post('products_material', TRUE),                );                $position_edit = $this->products_model->position_to_edit_products(array('id' => $product_data['id'], 'lang' => $product_data['lang']));                $product_data['position'] = $position_edit;                $this->products_model->update($product_data);                if (SLUG_ACTIVE > 0) {                    $this->slug_model->update_slug(array('slug' => my_trim($this->input->post('slug', TRUE)) . SLUG_CHARACTER_URL, 'type' => SLUG_TYPE_PRODUCTS, 'type_id' => $product_data['id']));                }//                if($this->input->get('back_url') != '')//                    redirect($this->input->get('back_url'));//                else                redirect(PRODUCTS_ADMIN_BASE_URL . '/' . $product_data['lang']);            }            $this->_last_message = validation_errors();            return FALSE;        } else if ($this->input->post('btnSubmit') == 'Upload') {            $this->upload_images();            $this->_last_message = $this->products_images_model->get_last_message();        }    }    /**     * Lấy ảnh tương ứng với từng sản phẩm     */    function get_products_images()    {        $options['product_id'] = $this->phpsession->get('product_id');        $images = $this->products_images_model->get_images($options);        $view_data = array();        $view_data['images'] = $images;        if ($this->input->post('is-ajax'))            echo $this->load->view('admin/products/products_images', $view_data, TRUE);        else            return $this->load->view('admin/products/products_images', $view_data, TRUE);    }    /*     * Thực hiện upload ảnh khách sạn = uploadify     */    public function ajax_upload_products_image()    {        if (!empty($_FILES)) {            $image_path = './images/products/';            $product_id = $this->phpsession->get('product_id');            $products = $this->products_model->get_products(array('id' => $product_id, 'is_admin' => TRUE));            $product_name = url_title($products->product_name, 'dash', TRUE);            $this->products_images_model->upload_image_1($product_id, $product_name, $image_path);        }    }    function upload_images()    {        $image_path = './images/products/';        $product_id = $this->phpsession->get('product_id');        $products = $this->products_model->get_products(array('id' => $product_id, 'is_admin' => TRUE));        $product_name = url_title($products->product_name, 'dash', TRUE);        $this->products_images_model->upload_image_1($product_id, $product_name, $image_path);    }    public function sort_products_image()    {        $arr = $this->input->post('id');        $i = 1;        foreach ($arr as $recordidval) {            $array = array('position' => $i);            $this->db->where('id', $recordidval);            $this->db->where('products_id', $this->phpsession->get('product_id'));            $this->db->update('product_images', $array);            $i = $i + 1;        }    }    public function delete_products_image()    {        $image_id = $this->input->post('id');        $image_path = './images/products/';        $this->products_images_model->delete_image($image_id, $image_path);        echo $this->get_products_images();    }    /**     * Xóa sản phẩm     */    function delete()    {        $options = array();        if ($this->is_postback()) {            $product_id = $this->input->post('id');            if (SLUG_ACTIVE > 0) {                $check_slug = $this->slug_model->get_slug(array('type_id' => $product_id, 'type' => SLUG_TYPE_PRODUCTS, 'onehit' => TRUE));                if (!empty($check_slug)) {                    $this->slug_model->delete($check_slug->id);                }            }            $this->products_images_model->delete_all_images($product_id, './images/products/');            $this->products_model->delete($product_id);            $options['warning'] = 'Đã xóa thành công';        }        $lang = $this->phpsession->get("product_lang");        //return $this->browse($options);        redirect(PRODUCTS_ADMIN_BASE_URL . '/' . $lang);    }//    public function up()//    {//        $product_id = $this->input->post('id');////        // Chỉ thực hiện nếu lời gọi này được gọi từ trang list//        //$product_id = $options['id'];//        //$this->products_model->up_product($product_id);//        $this->products_model->update(array('id'=>$product_id,'updated_date'=>date('Y-m-d H:i:s')));//        $lang = $this->phpsession->get("product_lang");//        redirect(PRODUCTS_ADMIN_BASE_URL . '/' . $lang);//    }    public function up()    {        $id = $this->input->post('id');        $this->products_model->item_to_sort_products(array('id' => $id));        $lang = $this->phpsession->get("product_lang");        redirect(PRODUCTS_ADMIN_BASE_URL . '/' . $lang);    }    function change_status()    {        $id = $this->input->post('id');        $product = $this->products_model->get_products(array('id' => $id, 'is_admin' => TRUE));        $status = $product->status == STATUS_ACTIVE ? STATUS_INACTIVE : STATUS_ACTIVE;        $this->products_model->update(array('id' => $id, 'status' => $status));    }    function change_home()    {        $id = $this->input->post('id');        $product = $this->products_model->get_products(array('id' => $id));        $home = $product->home == STATUS_ACTIVE ? STATUS_INACTIVE : STATUS_ACTIVE;        $this->products_model->update(array('id' => $id, 'home' => $home));    }    function import($options = array())    {        if (!empty($_FILES)) {            //upload products excel file            $config = array();            $config['upload_path'] = './images/products-data/';            $config['allowed_types'] = 'xls|xlsx|csv';            $config['max_size'] = '5048';            $this->load->library('upload', $config);            if (!$this->upload->do_upload()) {                $options['error'] = $this->upload->display_errors();            } else {                $data = $this->upload->data();                //read excel file                $this->load->library('excel');                $inputFileName = './images/products-data/' . $data['file_name'];                //  Read your Excel workbook                try {                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);                    $objPHPExcel = $objReader->load($inputFileName);                } catch (Exception $e) {                    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());                }                //  Get worksheet dimensions                $sheet = $objPHPExcel->getSheet(0);                $highestRow = $sheet->getHighestRow();                $highestColumn = $sheet->getHighestColumn();                //  Loop through each row of the worksheet in turn                for ($row = 1; $row <= $highestRow; $row++) {                    //  Read a row of data into an array                    $rowData = $sheet->ToArray('A' . $row . ':' . $highestColumn . $row,                        NULL,                        TRUE,                        FALSE);                    //  Insert row data array into your database of choice here                }                foreach ($rowData as $index => $data) {                    if ($index != 0) {                        $stt = $data[0];                        $product_name = $data[1];                        $cat = $data[2];                        $price_old = $data[3];                        $price = $data[4];                        $home = $data[5];                        $status = $data[6];                        $description = $data[7];                        $specifications = $data[8];                        $meta_title = $data[9];                        $meta_des = $data[10];                        $meta_key = $data[11];                        $options['product_name'] = $product_name;                        $products = $this->products_model->get_products($options);                        //if productsname doesn't exist in system                        if (count($products) == 0) {                            //insert to products table                            $products_data = array(                                'product_name' => $product_name,                                'categories_id' => $cat,                                'price' => $price,                                'price_old' => $price_old,                                'home' => $home,                                'status' => $status,                                'description' => $description,                                'specifications' => $specifications,                                'meta_title' => $meta_title,                                'meta_description' => $meta_des,                                'meta_keywords' => $meta_key,                                //'lang'                  => $this->phpsession->get("products_lang"),                            );                            $products_id = $this->products_model->insert($products_data);                        } else {                            $products_id = $products[0]->id;                            foreach ($products as $value) {                                $this->products_model->update(array('id' => $value->id));                            }                        }                    }                }                $options['succeed'] = 'Bạn đã nhập dữ liệu thành công';            }            $this->browse();        } else {            $options['error'] = validation_errors();            $options['header'] = 'Nhập sản phẩm bằng excel';            if (isset($options['error']) || isset($options['succeed']) || isset($options['warning']))                $options['options'] = $options;            // Chuan bi du lieu chinh de hien thi            $this->_view_data['main_content'] = $this->load->view('admin/products/add_product_form_excel', $options, TRUE);            // Chuan bi cac the META            $this->_view_data['title'] = 'Nhập sản phẩm bằng excel' . DEFAULT_TITLE_SUFFIX;            // Tra lai view du lieu cho nguoi su dung            $this->load->view($this->_layout, $this->_view_data);        }        return FALSE;//        else//        {//            $options['error'] = 'Bạn vui lòng chọn file để upload';//        }//        $options['page'] = 1;//        return $this->browse($options);    }    private function _get_scripts()    {        $scripts = '<script type="text/javascript" src="/plugins/tinymce/tinymce.min.js?v=4.1.7"></script>';        $scripts .= '<link rel="stylesheet" type="text/css" href="/plugins/fancybox/source/jquery.fancybox.css" media="screen" />';        $scripts .= '<script type="text/javascript" src="/plugins/fancybox/source/jquery.fancybox.pack.js"></script>';        $scripts .= '<script type="text/javascript">$(".iframe-btn").fancybox({"width":900,"height":500,"type":"iframe","autoScale":false});</script>';        $scripts .= '<style type=text/css>.fancybox-inner {height:500px !important;}</style>';        $scripts .= '<script type="text/javascript">enable_tiny_mce();</script>';        return $scripts;    }}?>