<?php
class ModelExtensionModuleStoriesNik extends Model {
    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stories` (
			`story_id` INT(11) NOT NULL AUTO_INCREMENT,
			`sort_order` INT(3) NOT NULL DEFAULT 0,
			`status` TINYINT(1) NOT NULL DEFAULT 1,
			PRIMARY KEY (`story_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stories_description` (
            `story_id` INT(11) NOT NULL,
            `language_id` INT(11) NOT NULL,
            `image` VARCHAR(255) NOT NULL,
            `text_length` INT(11) NOT NULL,
            `title` VARCHAR(64) NOT NULL,
            `description` mediumtext NOT NULL,
            `meta_title` VARCHAR(255) NOT NULL,
            `meta_description` VARCHAR(255) NOT NULL,
            `meta_keyword` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`story_id`, `language_id`)
		) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stories_to_layout` (
            `story_id` INT(11) NOT NULL AUTO_INCREMENT,
            `store_id` INT(11) NOT NULL,
            `layout_id` INT(11) NOT NULL,
            PRIMARY KEY (`story_id`, `store_id`)
		) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stories_to_store` (
            `story_id` INT(11) NOT NULL AUTO_INCREMENT,
            `store_id` INT(11) NOT NULL,
            PRIMARY KEY (`story_id`, `store_id`)
		) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "stories`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "stories_description`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "stories_to_layout`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "stories_to_store`");
    }

    public function addStory($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "stories SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "'");

        $story_id = $this->db->getLastId();

        foreach ($data['story_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "stories_description SET story_id = '" . (int)$story_id . "', language_id = '" . (int)$language_id . "', `image` = '" . $this->db->escape($value['image']) . "', text_length = '" . (int)$value['text_length'] . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }

        if (isset($data['story_store'])) {
            foreach ($data['story_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "stories_to_store SET story_id = '" . (int)$story_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        // SEO URL
        if (isset($data['story_seo_url'])) {
            foreach ($data['story_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'story_id=" . (int)$story_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                }
            }
        }

        if (isset($data['story_layout'])) {
            foreach ($data['story_layout'] as $store_id => $layout_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "stories_to_layout SET story_id = '" . (int)$story_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
            }
        }

        $this->cache->delete('stories');

        return $story_id;
    }

    public function editStory($story_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "stories SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE story_id = '" . (int)$story_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "stories_description WHERE story_id = '" . (int)$story_id . "'");

        foreach ($data['story_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "stories_description SET story_id = '" . (int)$story_id . "', language_id = '" . (int)$language_id . "', `image` = '" . $this->db->escape($value['image']) . "', text_length = '" . (int)$value['text_length'] . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "stories_to_store WHERE story_id = '" . (int)$story_id . "'");

        if (isset($data['story_store'])) {
            foreach ($data['story_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "stories_to_store SET story_id = '" . (int)$story_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'story_id=" . (int)$story_id . "'");

        if (isset($data['story_seo_url'])) {
            foreach ($data['story_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (trim($keyword)) {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'story_id=" . (int)$story_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                }
            }
        }

        $this->db->query("DELETE FROM `" . DB_PREFIX . "stories_to_layout` WHERE story_id = '" . (int)$story_id . "'");

        if (isset($data['story_layout'])) {
            foreach ($data['story_layout'] as $store_id => $layout_id) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "stories_to_layout` SET story_id = '" . (int)$story_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
            }
        }

        $this->cache->delete('stories');
    }

    public function deleteStory($story_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "stories` WHERE story_id = '" . (int)$story_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "stories_description` WHERE story_id = '" . (int)$story_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "stories_to_store` WHERE story_id = '" . (int)$story_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "stories_to_layout` WHERE story_id = '" . (int)$story_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'story_id=" . (int)$story_id . "'");

        $this->cache->delete('stories');
    }

    public function getStoryInfo($story_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stories s LEFT JOIN " . DB_PREFIX . "stories_description sd ON (s.story_id = sd.story_id) LEFT JOIN " . DB_PREFIX . "stories_to_store s2s ON (s.story_id = s2s.story_id) WHERE s.story_id = '" . (int)$story_id . "' AND sd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getStory($story_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "stories WHERE story_id = '" . (int)$story_id . "'");

        return $query->row;
    }

    public function getStories($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "stories s LEFT JOIN " . DB_PREFIX . "stories_description sd ON (s.story_id = sd.story_id) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

            $sort_data = array(
                'sd.title',
                's.sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY sd.title";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }


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
        } else {
            $stories_data = $this->cache->get('stories.' . (int)$this->config->get('config_language_id'));

            if (!$stories_data) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stories s LEFT JOIN " . DB_PREFIX . "stories_description sd ON (s.story_id = sd.story_id) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY sd.title");

                $stories_data = $query->rows;

                $this->cache->set('stories.' . (int)$this->config->get('config_language_id'), $stories_data);
            }

            return $stories_data;
        }
    }

    public function getStoryDescription($story_id) {
        $story_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stories_description WHERE story_id = '" . (int)$story_id . "'");

        foreach ($query->rows as $result) {
            $story_description_data[$result['language_id']] = array(
                'image'            => $result['image'],
                'text_length'      => $result['text_length'],
                'title'            => $result['title'],
                'description'      => $result['description'],
                'meta_title'       => $result['meta_title'],
                'meta_description' => $result['meta_description'],
                'meta_keyword'     => $result['meta_keyword']
            );
        }

        return $story_description_data;
    }

    public function getStoryStores($story_id) {
        $story_store_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stories_to_store WHERE story_id = '" . (int)$story_id . "'");

        foreach ($query->rows as $result) {
            $story_store_data[] = $result['store_id'];
        }

        return $story_store_data;
    }

    public function getStorySeoUrls($story_id) {
        $story_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'story_id=" . (int)$story_id . "'");

        foreach ($query->rows as $result) {
            $story_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
        }

        return $story_seo_url_data;
    }

    public function getStoryLayouts($story_id) {
        $story_layout_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stories_to_layout WHERE story_id = '" . (int)$story_id . "'");

        foreach ($query->rows as $result) {
            $story_layout_data[$result['store_id']] = $result['layout_id'];
        }

        return $story_layout_data;
    }

    public function getTotalStories() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "stories");

        return $query->row['total'];
    }

    public function getTotalStoriesByLayoutId($layout_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "stories_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

        return $query->row['total'];
    }
}