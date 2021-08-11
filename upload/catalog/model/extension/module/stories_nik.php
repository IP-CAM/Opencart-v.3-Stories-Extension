<?php
class ModelExtensionModuleStoriesNik extends Model {
    public function getStories($limit) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stories s LEFT JOIN " . DB_PREFIX . "stories_description sd ON (s.story_id = sd.story_id) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND s.status = 1 ORDER BY s.sort_order ASC LIMIT " . (int)$limit);

        return $query->rows;
    }

    public function getStory($story_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stories s LEFT JOIN " . DB_PREFIX . "stories_description sd ON (s.story_id = sd.story_id) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND s.story_id = '" . (int)$story_id . "' AND s.status = 1");

        return $query->row;
    }
}