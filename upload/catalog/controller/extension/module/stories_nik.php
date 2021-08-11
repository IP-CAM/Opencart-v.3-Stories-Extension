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

    protected function getStoryView($story_id) {
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $story = $this->model_extension_module_stories_nik->getStory($story_id);

        if ($story['description']) {
            $story['description'] = html_entity_decode($story['description']);
        }

        if ($story['image']) {
            $story['thumb'] = $this->model_tool_image->resize($story['image'], 183, 277);
        } else {
            $story['thumb'] = '';
        }

        $data['story'] = $story;

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/module/stories_info_nik', $data));
    }
}