<?php
class ControllerExtensionModuleStoriesNik extends Controller {
    public function index() {
        $this->load->language('extension/module/stories_nik');
        $this->load->model('extension/module/stories_nik');
        $this->load->model('setting/setting');
        $this->load->model('tool/image');

        $settings = $this->model_setting_setting->getSetting('module_stories_nik', $this->config->get('config_store_id'));

        if (isset($settings['module_stories_nik_stories_count']) && utf8_strlen($settings['module_stories_nik_stories_count']) < 1) {
            $settings['module_stories_nik_stories_count'] = 10;
        }

        if (isset($this->request->get['story_id'])) {
            return $this->getStoryView($this->request->get['story_id']);
        }

        $data['stories'] = $this->model_extension_module_stories_nik->getStories($settings['module_stories_nik_stories_count']);

        foreach ($data['stories'] as $key => $story) {
            if ($story['description']) {
                $data['stories'][$key]['description'] = html_entity_decode($story['description']);
                $data['stories'][$key]['description'] = strip_tags($data['stories'][$key]['description']);
                $data['stories'][$key]['description'] = trim($data['stories'][$key]['description']);
                $data['stories'][$key]['description'] = substr($data['stories'][$key]['description'], 0, (int)$story['text_length']);
                $data['stories'][$key]['description'] = rtrim($data['stories'][$key]['description'], "!,.-");
                $data['stories'][$key]['description'] = substr($data['stories'][$key]['description'], 0, strrpos($data['stories'][$key]['description'], ' '));
                $data['stories'][$key]['description'] .= '...';
            }

            $data['stories'][$key]['link'] = $this->url->link('extension/module/stories_nik', 'story_id=' . $story['story_id']);

            if ($story['image']) {
                $data['stories'][$key]['thumb'] = $this->model_tool_image->resize($story['image'], 183, 277);
            } else {
                $data['stories'][$key]['thumb'] = '';
            }

        }

        return $this->load->view('extension/module/stories_nik', $data);
    }

    public function stories() {
        $this->load->language('extension/module/stories_nik');
        $this->load->model('extension/module/stories_nik');
        $this->load->model('setting/setting');
        $this->load->model('tool/image');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $settings = $this->model_setting_setting->getSetting('module_stories_nik', $this->config->get('config_store_id'));

        if (isset($settings['module_stories_nik_stories_count']) && utf8_strlen($settings['module_stories_nik_stories_count']) < 1) {
            $limit = 8;
        } else {
            $limit = (int)$settings['module_stories_nik_stories_count'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $filter_data = array(
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );


        $data['stories'] = array();

        $stories_total = $this->model_extension_module_stories_nik->getTotalStories();

        $stories = $this->model_extension_module_stories_nik->getStories($filter_data);

        foreach ($stories as $key => $story) {
            if ($story['description']) {
                $stories[$key]['description'] = html_entity_decode($story['description']);
                $stories[$key]['description'] = strip_tags($stories[$key]['description']);
                $stories[$key]['description'] = trim($stories[$key]['description']);
                $stories[$key]['description'] = substr($stories[$key]['description'], 0, (int)$story['text_length']);
                $stories[$key]['description'] = rtrim($stories[$key]['description'], "!,.-");
                $stories[$key]['description'] = substr($stories[$key]['description'], 0, strrpos($stories[$key]['description'], ' '));
                $stories[$key]['description'] .= '...';
            }

            $stories[$key]['link'] = $this->url->link('extension/module/stories_nik', 'story_id=' . $story['story_id']);

            if ($story['image']) {
                $stories[$key]['thumb'] = $this->model_tool_image->resize($story['image'], 183, 277);
            } else {
                $stories[$key]['thumb'] = '';
            }

        }

        $data['stories'] = $stories;

        $pagination = new Pagination();
        $pagination->total = $stories_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('extension/module/stories_nik/stories', '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($stories_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($stories_total - $limit)) ? $stories_total : ((($page - 1) * $limit) + $limit), $stories_total, ceil($stories_total / $limit));

        $data['limit'] = $limit;
        $data['count_stories'] = $stories_total;

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/module/stories_list_nik', $data));
    }

    protected function getStoryView($story_id) {
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/stories_nik/stories')
        );

        $story = $this->model_extension_module_stories_nik->getStory($story_id);

        if ($story) {
            $this->document->setTitle($story['meta_title']);
            $this->document->setDescription($story['meta_description']);
            $this->document->setKeywords($story['meta_keyword']);

            if ($story['description']) {
                $story['description'] = html_entity_decode($story['description']);
            }

            if ($story['image']) {
                $story['thumb'] = $this->model_tool_image->resize($story['image'], 183, 277);
            } else {
                $story['thumb'] = '';
            }

            $data['story'] = $story;
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/module/stories_info_nik', $data));
    }
}