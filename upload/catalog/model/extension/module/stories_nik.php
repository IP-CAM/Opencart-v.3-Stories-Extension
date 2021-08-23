<?php
class ModelExtensionModuleStoriesNik extends Model {
    public function getStories($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "stories s LEFT JOIN " . DB_PREFIX . "stories_description sd ON (s.story_id = sd.story_id) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND s.status = 1 ORDER BY s.sort_order ASC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getStory($story_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stories s LEFT JOIN " . DB_PREFIX . "stories_description sd ON (s.story_id = sd.story_id) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND s.story_id = '" . (int)$story_id . "' AND s.status = 1");

        return $query->row;
    }

    public function getTotalStories() {
        $sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "stories s LEFT JOIN " . DB_PREFIX . "stories_description sd ON (s.story_id = sd.story_id) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND s.status = 1 ORDER BY s.sort_order ASC";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}