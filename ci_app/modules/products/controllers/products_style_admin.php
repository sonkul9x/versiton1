<?php
class Products_Style_Admin extends MY_Controller {
    function __construct() {
        parent::__construct();
        modules::run('auth/auth/validate_login');
        $this->_layout = 'admin_ui/layout/main';
    }

    function browse() {
        $options = array();

        $options['products_style'] = $this->products_style_model->get_products_style();

        if(isset($options['error']) || isset($options['succeed']) || isset($options['warning'])) 
            $options['options'] = $options;

        // Chuan bi du lieu chinh de hien thi
        $this->_view_data['main_content'] = $this->load->view('admin/products_style/list', $options, TRUE);
        // Chuan bi cac the META
        $this->_view_data['title'] = 'Quản lý Kiểu dáng' . DEFAULT_TITLE_SUFFIX;
        // Tra lai view du lieu cho nguoi su dung
        $this->load->view($this->_layout, $this->_view_data);
    }
 
    function add() {
        $options = array();
        // Xu ly viec them vao db
        if ($this->is_postback()) {
            if (!$this->_do_add()) {
                $options['error'] = validation_errors();
                $options['options'] = $options;
            }
        }
        $this->_get_add_form_data($options);

        // Chuan bi du lieu chinh de hien thi
        $this->_view_data['main_content'] = $this->load->view('admin/products_style/form', $options, TRUE);
        // Chuan bi cac the META
        $this->_view_data['title'] = 'Thêm Kiểu dáng' . DEFAULT_TITLE_SUFFIX;

        // Tra lai view du lieu cho nguoi su dung
        $this->load->view($this->_layout, $this->_view_data);
    }

    /**
     *
     * @return type 
     */
    private function _get_add_form_data(&$options = array()) {
        
        $options['name']        = $this->input->post('name');
        $options['header']      = 'Thêm Kiểu dáng';
        $options['button_name'] = 'Lưu dữ liệu';
        $options['submit_uri']  = PRODUCTS_STYLE_ADMIN_ADD_URL;
    }

    private function _do_add() {
        $this->form_validation->set_rules('name', 'Kiểu dáng', 'trim|required|xss_clean|max_length[255]');
        if ($this->form_validation->run()) {
            $data = array(
                'name' => my_trim($this->input->post('name')),
                'status' => STATUS_ACTIVE,
                'creator' => $this->phpsession->get('user_id'),
                'editor' => $this->phpsession->get('user_id'),
            );
            $this->products_style_model->insert($data);

            redirect(PRODUCTS_STYLE_ADMIN_BASE_URL);
        }
        return FALSE;
    }

    function edit() {
        $options = array();
        if ($this->is_postback()) {
            if (!$this->input->post('from_list')) {
                if (!$this->_do_edit()) {
                    $options['error'] = validation_errors();
                    $options['options'] = $options;
                }
            }
        }
        $this->_get_edit_form_data($options);
        // Chuan bi du lieu chinh de hien thi
        $this->_view_data['main_content']   = $this->load->view('admin/products_style/form', $options, TRUE);
        // Chuan bi cac the META
        $this->_view_data['title']          = 'Sửa Kiểu dáng' . DEFAULT_TITLE_SUFFIX;
        // Tra lai view du lieu cho nguoi su dung
        $this->load->view($this->_layout, $this->_view_data);
    }

    private function _get_edit_form_data(&$options = array()) {
        $id = $this->input->post('id');
        if ($this->input->post('from_list')) {
            $products_style = $this->products_style_model->get_products_style(array('id' => $id));
            $options['name'] = $products_style->name;
        } else {
            $options['name'] = $this->input->post('name');
        }
        $options['id']          = $id;
        $options['header']      = 'Sửa Kiểu dáng';
        $options['button_name'] = 'Lưu dữ liệu';
        $options['submit_uri']  = PRODUCTS_STYLE_ADMIN_EDIT_URL;
    }

    private function _do_edit() {
        $this->form_validation->set_rules('name', 'Kiểu dáng', 'trim|required|xss_clean|max_length[255]');
        if ($this->form_validation->run()) {
            $data = array(
                'id' => $this->input->post('id'),
                'name' => my_trim($this->input->post('name', TRUE)),
                'editor' => $this->phpsession->get('user_id'),
            );
            $this->products_style_model->update($data);
            redirect(PRODUCTS_STYLE_ADMIN_BASE_URL);
        }
        return FALSE;
    }

    function delete() {
        if ($this->is_postback() && $this->input->post('from_list')) {
            $id = $this->input->post('id');
            $check1 = $this->products_model->get_products_count(array('style_id'=>$id));
            if(empty($check1)){
                $this->products_style_model->delete($id);
            }
            redirect(PRODUCTS_STYLE_ADMIN_BASE_URL);
        }
    }

}