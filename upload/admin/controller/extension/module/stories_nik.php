<?php
class ControllerExtensionModuleStoriesNik extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/stories_nik');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('extension/module/stories_nik');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_stories_nik', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $this->getList();
    }

    public function addStory() {
        $this->load->language('extension/module/stories_nik');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/stories_nik');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateStoryForm()) {
            $this->model_extension_module_stories_nik->addStory($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/stories_nik', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getFormStory();
    }

    public function editStory() {
        $this->load->language('extension/module/stories_nik');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/stories_nik');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateStoryForm()) {
            $this->model_extension_module_stories_nik->editStory($this->request->get['story_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/stories_nik', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getFormStory();
    }

    public function deleteMember() {
        $this->load->language('extension/module/stories_nik');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/stories_nik');

        if (isset($this->request->get['story_id']) && $this->validateDelete()) {
            $this->model_extension_module_stories_nik->deleteStory($this->request->get['story_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/stories_nik', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'sd.title';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/stories_nik', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/stories_nik', 'user_token=' . $this->session->data['user_token'], true);
        $data['addStory'] = $this->url->link('extension/module/stories_nik/addStory', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $data['sort_story_title'] = $this->url->link('extension/module/stories_nik', 'user_token=' . $this->session->data['user_token'] . '&sort=sd.title' . $url, true);
        $data['sort_story_sort_order'] = $this->url->link('extension/module/stories_nik', 'user_token=' . $this->session->data['user_token'] . '&sort=s.sort_order' . $url, true);

        if (isset($this->request->post['module_stories_nik_stories_count'])) {
            $data['module_stories_nik_stories_count'] = $this->request->post['module_stories_nik_stories_count'];
        } else if ($this->config->get('module_stories_nik_stories_count')) {
            $data['module_stories_nik_stories_count'] = $this->config->get('module_stories_nik_stories_count');
        } else {
            $data['module_stories_nik_stories_count'] = 10;
        }

        $data['module_stories_nik_module_link'] = HTTPS_CATALOG . 'index.php?route=extension/module/stories_nik/stories';

        if (isset($this->request->post['module_stories_nik_status'])) {
            $data['module_stories_nik_status'] = $this->request->post['module_stories_nik_status'];
        } else {
            $data['module_stories_nik_status'] = $this->config->get('module_stories_nik_status');
        }

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
        );

        $results = $this->model_extension_module_stories_nik->getStories($filter_data);

        foreach ($results as $result) {
            $data['stories'][] = array(
                'story_id'      => $result['story_id'],
                'title'         => $result['title'],
                'sort_order'    => $result['sort_order'],
                'edit'          => $this->url->link('extension/module/stories_nik/editStory', 'user_token=' . $this->session->data['user_token'] . '&story_id=' . $result['story_id'], true),
                'delete'        => $this->url->link('extension/module/stories_nik/deleteStory', 'user_token=' . $this->session->data['user_token'] . '&story_id=' . $result['story_id'], true)
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/stories_nik', $data));
    }

    protected function getFormStory() {
        $data['text_form'] = !isset($this->request->get['story_id']) ? $this->language->get('text_add_story') : $this->language->get('text_edit_story');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['image'])) {
            $data['error_image'] = $this->error['image'];
        } else {
            $data['error_image'] = array();
        }

        if (isset($this->error['text_length'])) {
            $data['error_text_length'] = $this->error['text_length'];
        } else {
            $data['error_text_length'] = array();
        }

        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = array();
        }

        if (isset($this->error['description'])) {
            $data['error_description'] = $this->error['description'];
        } else {
            $data['error_description'] = array();
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = array();
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/stories_nik', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['story_id'])) {
            $data['action'] = $this->url->link('extension/module/stories_nik/addStory', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/stories_nik/editStory', 'user_token=' . $this->session->data['user_token'] . '&story_id=' . $this->request->get['story_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('extension/module/stories_nik', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['story_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $story_info = $this->model_extension_module_stories_nik->getStory($this->request->get['story_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('tool/image');

        if (isset($this->request->post['story_description'])) {
            $data['story_description'] = $this->request->post['story_description'];
        } elseif (isset($this->request->get['story_id'])) {
            $data['story_description'] = $this->model_extension_module_stories_nik->getStoryDescription($this->request->get['story_id']);
        } else {
            $data['story_description'] = array();
        }

        foreach ($data['languages'] as $language) {
            if (isset($data['story_description'][$language['language_id']]) && isset($data['story_description'][$language['language_id']]['image']) && $data['story_description'][$language['language_id']]['image']) {
                $data['story_description'][$language['language_id']]['thumb'] = $this->model_tool_image->resize($data['story_description'][$language['language_id']]['image'], 100, 100);
            } else {
                $data['story_description'][$language['language_id']]['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        if (isset($this->request->post['story_store'])) {
            $data['story_store'] = $this->request->post['story_store'];
        } elseif (isset($this->request->get['story_id'])) {
            $data['story_store'] = $this->model_extension_module_stories_nik->getStoryStores($this->request->get['story_id']);
        } else {
            $data['story_store'] = array(0);
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($story_info)) {
            $data['sort_order'] = $story_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($story_info)) {
            $data['status'] = $story_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['story_seo_url'])) {
            $data['story_seo_url'] = $this->request->post['story_seo_url'];
        } elseif (isset($this->request->get['story_id'])) {
            $data['story_seo_url'] = $this->model_extension_module_stories_nik->getStorySeoUrls($this->request->get['story_id']);
        } else {
            $data['story_seo_url'] = array();
        }

        if (isset($this->request->post['story_layout'])) {
            $data['story_layout'] = $this->request->post['story_layout'];
        } elseif (isset($this->request->get['story_id'])) {
            $data['story_layout'] = $this->model_extension_module_stories_nik->getStoryLayouts($this->request->get['story_id']);
        } else {
            $data['story_layout'] = array();
        }

        $this->load->model('design/layout');

        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['img_placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/stories_form_nik', $data));
    }

    public function install() {
        if ($this->user->hasPermission('modify', 'extension/module/stories_nik')) {
            $this->load->model('extension/module/stories_nik');

            $this->model_extension_module_stories_nik->install();
        }
    }

    public function uninstall() {
        if ($this->user->hasPermission('modify', 'extension/module/stories_nik')) {
            $this->load->model('extension/module/stories_nik');

            $this->model_extension_module_stories_nik->uninstall();
        }
    }

    protected function validateStoryForm() {
        if (!$this->user->hasPermission('modify', 'extension/module/stories_nik')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['story_description'] as $language_id => $value) {
            if ((utf8_strlen($value['image']) < 1)) {
                $this->error['image'][$language_id] = $this->language->get('error_image');
            }

            if ((utf8_strlen($value['text_length']) < 1) || (utf8_strlen($value['text_length']) > 64)) {
                $this->error['text_length'][$language_id] = $this->language->get('error_text_length');
            }

            if ((utf8_strlen($value['title']) < 1) || (utf8_strlen($value['title']) > 64)) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }

            if ((utf8_strlen($value['description']) < 1)) {
                $this->error['description'][$language_id] = $this->language->get('error_description');
            }

            if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
                $this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
            }
        }

        if ($this->request->post['story_seo_url']) {
            $this->load->model('design/seo_url');

            foreach ($this->request->post['story_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        if (count(array_keys($language, $keyword)) > 1) {
                            $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
                        }

                        $seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

                        foreach ($seo_urls as $seo_url) {
                            if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['story_id']) || ($seo_url['query'] != 'story_id=' . $this->request->get['story_id']))) {
                                $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');

                                break;
                            }
                        }
                    }
                }
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/stories_nik')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/stories_nik')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}