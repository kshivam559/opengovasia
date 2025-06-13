<?php

/**
 * WordPress Hybrid Meta Storage System
 * 
 * Features:
 * - Standard WordPress function patterns (get_meta, add_meta, update_meta, delete_meta)
 * - Custom tables for better performance (no _meta suffix)
 * - Relationship tables for many-to-many (no _map suffix)
 * - Simple, clean, production-ready code
 * - Full repeatable data support
 * - Enhanced query capabilities
 */

// Register Custom Post Types
function register_custom_post_types()
{
    $post_types = [
        'events' => ['name' => 'Events', 'icon' => 'dashicons-calendar'],
        'awards' => ['name' => 'Awards', 'icon' => 'dashicons-awards'],
        'company' => ['name' => 'Company', 'icon' => 'dashicons-businessman'],
        'testimonials' => ['name' => 'Testimonials', 'icon' => 'dashicons-format-quote'],
        'ogtv' => ['name' => 'OGTV', 'icon' => 'dashicons-video-alt3'],
    ];

    foreach ($post_types as $slug => $data) {

        // Default value
        $has_archive = true;
        $rewrite_slug = $slug;

        if ($slug === 'awards') {
            $has_archive = 'past-winners'; // change archive slug for awards
            $rewrite_slug = 'awards'; // Custom slug for awards
        }

        if ($slug === 'company') {
            $has_archive = false; // No archive for company
            
        }

        register_post_type($slug, [
            'labels' => [
                'name' => $data['name'],
                'singular_name' => rtrim($data['name'], 's'),
                'add_new' => 'Add New',
                'edit_item' => 'Edit ' . rtrim($data['name'], 's'),
                'view_item' => 'View ' . rtrim($data['name'], 's'),
                'all_items' => 'All ' . $data['name'],
            ],
            'public' => true,
            'has_archive' => $has_archive,
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_icon' => $data['icon'],
            'show_in_rest' => true,
            'rewrite' => [
                'slug' => $rewrite_slug,
                'with_front' => false
            ],
            'menu_position' => 6,
        ]);

        HybridMeta::create_tables($slug);
    }
}
add_action('init', 'register_custom_post_types');

class HybridMeta
{
    private static $cache = [];
    private static $current_hybrid_meta_query = null;
    private static $schema_version = '0.0.0'; // Increment this for schema updates
    private static $update_lock_timeout = 30; // seconds
    private static $current_query_post_type = null;

    // Field definitions per post type
    private static $schemas = [
        'events' => [
            'event_date' => 'DATE',
            'event_start_time' => 'TIME',
            'event_end_time' => 'TIME',
            'event_timezone' => 'VARCHAR(50)',
            'event_address' => 'VARCHAR(255)',
            'theme_color' => 'VARCHAR(7)',
            'event_description' => 'TEXT',
            'event_link' => 'VARCHAR(500)',
            'speakers_heading' => 'VARCHAR(255)',
        ],
        'awards' => [

        ],
        'company' => [

        ],
        'testimonials' => [

        ],
        'ogtv' => [
            'video_url' => 'VARCHAR(500)',
        ]
    ];

    // Relationship definitions
    private static $relations = [
        'events' => [
            'speakers',
            'who_should_attend', // Add this
            'testimonials',
            'topics_covered',
            'special_events',
            'companies', // Add this
        ],
        'awards' => ['companies'],
        'company' => ['socials'],
    ];

    /**
     * Create/Update tables with production safety
     */
    public static function create_tables($post_type)
    {
        try {
            if (!isset(self::$schemas[$post_type])) {
                return true;
            }

            // Check if update is needed
            if (!self::needs_schema_update($post_type)) {
                return true;
            }

            // Acquire update lock
            if (!self::acquire_update_lock($post_type)) {
                error_log("HybridMeta: Could not acquire update lock for {$post_type}");
                return false;
            }

            // Double-check after acquiring lock
            if (!self::needs_schema_update($post_type)) {
                self::release_update_lock($post_type);
                return true;
            }

            $success = self::perform_schema_update($post_type);

            if ($success) {
                update_option("hybrid_meta_version_{$post_type}", self::$schema_version);
                self::log_schema_update($post_type, 'success');
            } else {
                self::log_schema_update($post_type, 'failed');
            }

            self::release_update_lock($post_type);
            return $success;

        } catch (Exception $e) {
            error_log("HybridMeta: Schema update failed for {$post_type}: " . $e->getMessage());
            self::release_update_lock($post_type);
            return false;
        }
    }

    /**
     * Check if schema update is needed
     */
    private static function needs_schema_update($post_type)
    {
        global $wpdb;

        // First check if table exists at all
        $table = $wpdb->prefix . $post_type;
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table;

        // If table doesn't exist, we definitely need to create it
        if (!$table_exists) {
            error_log("HybridMeta: Table {$table} does not exist, needs creation");
            return true;
        }

        // If table exists, check version
        $current_version = get_option("hybrid_meta_version_{$post_type}", '0.0.0');
        $needs_update = version_compare($current_version, self::$schema_version, '<');

        if ($needs_update) {
            error_log("HybridMeta: Schema version mismatch for {$post_type}. Current: {$current_version}, Required: " . self::$schema_version);
        }

        return $needs_update;
    }

    /**
     * Force schema update
     */
    public static function force_update_schema($post_type)
    {
        try {
            // Rate limiting check
            $last_update = get_option("hybrid_meta_last_update_{$post_type}", 0);
            if (time() - $last_update < 300) { // 5 minute cooldown
                error_log("HybridMeta: Rate limit hit for {$post_type}");
                return false;
            }

            update_option("hybrid_meta_last_update_{$post_type}", time());

            // Force version mismatch by setting old version
            update_option("hybrid_meta_version_{$post_type}", '0.0.0');

            // Acquire update lock
            if (!self::acquire_update_lock($post_type)) {
                error_log("HybridMeta: Could not acquire update lock for {$post_type}");
                return false;
            }

            // Directly perform schema update without version check
            $success = self::perform_schema_update($post_type);

            if ($success) {
                update_option("hybrid_meta_version_{$post_type}", self::$schema_version);
                self::log_schema_update($post_type, 'force_success');
                error_log("HybridMeta: Force update successful for {$post_type}");
            } else {
                self::log_schema_update($post_type, 'force_failed');
                error_log("HybridMeta: Force update failed for {$post_type}");
            }

            self::release_update_lock($post_type);
            return $success;

        } catch (Exception $e) {
            error_log("HybridMeta: Force update exception for {$post_type}: " . $e->getMessage());
            self::release_update_lock($post_type);
            return false;
        }
    }


    /**
     * Acquire update lock to prevent concurrent updates
     */
    private static function acquire_update_lock($post_type)
    {
        $lock_key = "hybrid_meta_update_lock_{$post_type}";
        $lock_value = time() + self::$update_lock_timeout;

        // Try to acquire lock
        $existing_lock = get_option($lock_key, 0);

        // Check if existing lock is expired
        if ($existing_lock && $existing_lock > time()) {
            return false; // Lock is still active
        }

        // Acquire or refresh lock
        return update_option($lock_key, $lock_value);
    }

    /**
     * Release update lock
     */
    private static function release_update_lock($post_type)
    {
        $lock_key = "hybrid_meta_update_lock_{$post_type}";
        delete_option($lock_key);
    }

    /**
     * Perform the actual schema update with error handling
     */
    private static function perform_schema_update($post_type)
    {
        global $wpdb;

        $table = $wpdb->prefix . $post_type;

        // Start transaction if possible
        $wpdb->query('START TRANSACTION');

        try {
            // Check if table exists
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table;

            if (!$table_exists) {
                $success = self::create_new_table($post_type);
            } else {
                $success = self::update_existing_table($post_type);
            }

            if (!$success) {
                throw new Exception("Failed to update main table");
            }

            // Update relationship tables
            $success = self::create_relationship_tables($post_type);
            if (!$success) {
                throw new Exception("Failed to update relationship tables");
            }

            // Commit transaction
            $wpdb->query('COMMIT');
            return true;

        } catch (Exception $e) {
            // Rollback transaction
            $wpdb->query('ROLLBACK');
            error_log("HybridMeta: Schema update failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new table with better error handling
     */
    private static function create_new_table($post_type)
    {
        global $wpdb;

        $charset = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . $post_type;
        $columns = ['post_id BIGINT UNSIGNED NOT NULL PRIMARY KEY'];

        foreach (self::$schemas[$post_type] as $field => $type) {
            // Validate field name
            if (!self::is_valid_field_name($field)) {
                throw new Exception("Invalid field name: {$field}");
            }
            $columns[] = "`{$field}` {$type}";
        }

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            " . implode(",\n            ", $columns) . ",
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX post_id (post_id)
        ) {$charset};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Capture any dbDelta errors
        ob_start();
        $result = dbDelta($sql);
        $output = ob_get_clean();

        if ($wpdb->last_error) {
            throw new Exception("Database error: " . $wpdb->last_error);
        }

        return true;
    }

    /**
     * Update existing table with validation
     */
    private static function update_existing_table($post_type)
    {
        global $wpdb;

        $table = $wpdb->prefix . $post_type;

        // Get existing columns with error handling
        $existing_columns = $wpdb->get_col("DESCRIBE {$table}", 0);

        if ($wpdb->last_error) {
            throw new Exception("Could not describe table: " . $wpdb->last_error);
        }

        // Add missing columns
        foreach (self::$schemas[$post_type] as $field => $type) {
            if (!in_array($field, $existing_columns)) {

                // Validate field name and type
                if (!self::is_valid_field_name($field)) {
                    throw new Exception("Invalid field name: {$field}");
                }

                if (!self::is_valid_field_type($type)) {
                    throw new Exception("Invalid field type: {$type}");
                }

                $sql = "ALTER TABLE {$table} ADD COLUMN `{$field}` {$type}";
                $result = $wpdb->query($sql);

                if ($result === false || $wpdb->last_error) {
                    throw new Exception("Failed to add column {$field}: " . $wpdb->last_error);
                }

                error_log("HybridMeta: Added column '{$field}' to table '{$table}'");
            }
        }

        return true;
    }

    /**
     * Create relationship tables with error handling
     */
    private static function create_relationship_tables($post_type)
    {
        global $wpdb;

        if (!isset(self::$relations[$post_type])) {
            return true;
        }

        $charset = $wpdb->get_charset_collate();

        foreach (self::$relations[$post_type] as $relation) {
            // Validate relation name
            if (!self::is_valid_field_name($relation)) {
                throw new Exception("Invalid relation name: {$relation}");
            }

            $rel_table = $wpdb->prefix . $post_type . '_' . $relation;

            $rel_sql = "CREATE TABLE IF NOT EXISTS {$rel_table} (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                post_id BIGINT UNSIGNED NOT NULL,
                value TEXT NOT NULL,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX post_id (post_id),
                INDEX sort_order (sort_order)
            ) {$charset};";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            ob_start();
            dbDelta($rel_sql);
            ob_get_clean();

            if ($wpdb->last_error) {
                throw new Exception("Failed to create relation table {$rel_table}: " . $wpdb->last_error);
            }
        }

        return true;
    }

    /**
     * Validate field names to prevent SQL injection
     */
    private static function is_valid_field_name($name)
    {
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name) && strlen($name) <= 64;
    }

    /**
     * Validate field types
     */
    private static function is_valid_field_type($type)
    {
        $allowed_types = [
            'VARCHAR',
            'CHAR',
            'TEXT',
            'MEDIUMTEXT',
            'LONGTEXT',
            'INT',
            'BIGINT',
            'SMALLINT',
            'TINYINT',
            'DECIMAL',
            'FLOAT',
            'DOUBLE',
            'DATE',
            'DATETIME',
            'TIMESTAMP',
            'TIME',
            'BOOLEAN',
            'TINYINT(1)'
        ];

        $type_upper = strtoupper($type);
        foreach ($allowed_types as $allowed) {
            if (strpos($type_upper, $allowed) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log schema updates for debugging
     */
    private static function log_schema_update($post_type, $status)
    {
        $log_entry = [
            'post_type' => $post_type,
            'version' => self::$schema_version,
            'status' => $status,
            'timestamp' => current_time('mysql')
        ];

        $log = get_option('hybrid_meta_update_log', []);
        $log[] = $log_entry;

        // Keep only last 50 entries
        if (count($log) > 50) {
            $log = array_slice($log, -50);
        }

        update_option('hybrid_meta_update_log', $log);
    }

    /**
     * Safe mode - disable schema updates in production
     */
    public static function is_safe_mode()
    {
        return defined('HYBRID_META_SAFE_MODE') && HYBRID_META_SAFE_MODE;
    }

    /**
     * Get update log for debugging
     */
    public static function get_update_log()
    {
        return get_option('hybrid_meta_update_log', []);
    }

    /**
     * Clear update log
     */
    public static function clear_update_log()
    {
        delete_option('hybrid_meta_update_log');
    }

    /**
     * Check system health
     */
    public static function health_check()
    {
        $health = [
            'status' => 'healthy',
            'issues' => []
        ];

        foreach (array_keys(self::$schemas) as $post_type) {
            $version = get_option("hybrid_meta_version_{$post_type}", '0.0');

            if (version_compare($version, self::$schema_version, '<')) {
                $health['issues'][] = "Schema outdated for {$post_type}";
                $health['status'] = 'warning';
            }

            // Check if tables exist
            global $wpdb;
            $table = $wpdb->prefix . $post_type;
            $exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table;

            if (!$exists) {
                $health['issues'][] = "Missing table for {$post_type}";
                $health['status'] = 'error';
            }
        }

        return $health;
    }

    /**
     * Get meta value - WordPress standard pattern
     */
    public static function get($post_id, $key = '', $single = true)
    {
        if (!$post_id)
            return $single ? '' : [];

        $post_type = get_post_type($post_id);
        if (!$post_type || !isset(self::$schemas[$post_type])) {
            return $single ? '' : [];
        }

        // Check if it's a relationship and get it directly
        if (
            $key && isset(self::$relations[$post_type]) &&
            in_array($key, self::$relations[$post_type])
        ) {
            return self::get_relation($post_id, $key);
        }

        // Get from cache or load (this now includes relationships)
        $cache_key = $post_id . '_' . $post_type;
        if (!isset(self::$cache[$cache_key])) {
            self::$cache[$cache_key] = self::load_data($post_id, $post_type);
        }

        $data = self::$cache[$cache_key];

        if (empty($key)) {
            return $data; // This now includes both meta fields and relationships
        }

        $value = isset($data[$key]) ? $data[$key] : '';
        return $single ? $value : [$value];
    }

    /**
     * Add meta value - WordPress standard pattern
     */
    public static function add($post_id, $key, $value)
    {
        return self::update($post_id, $key, $value);
    }

    /**
     * Update meta value - WordPress standard pattern
     */
    public static function update($post_id, $key, $value = null)
    {
        if (!$post_id)
            return false;

        $post_type = get_post_type($post_id);
        if (!$post_type || !isset(self::$schemas[$post_type]))
            return false;

        // Handle array of data
        if (is_array($key)) {
            return self::save_data($post_id, $post_type, $key);
        }

        // Handle single key-value
        if ($value !== null) {
            return self::save_data($post_id, $post_type, [$key => $value]);
        }

        return false;
    }

    /**
     * Delete meta value - WordPress standard pattern
     */
    public static function delete($post_id, $key = '')
    {
        if (!$post_id)
            return false;

        global $wpdb;
        $post_type = get_post_type($post_id);
        if (!$post_type || !isset(self::$schemas[$post_type]))
            return false;

        if (empty($key)) {
            // Delete all meta
            $table = $wpdb->prefix . $post_type;
            $result = $wpdb->delete($table, ['post_id' => $post_id], ['%d']);

            // Delete all relationships
            if (isset(self::$relations[$post_type])) {
                foreach (self::$relations[$post_type] as $relation) {
                    $rel_table = $wpdb->prefix . $post_type . '_' . $relation;
                    $wpdb->delete($rel_table, ['post_id' => $post_id], ['%d']);
                }
            }

            unset(self::$cache[$post_id . '_' . $post_type]);
            return $result !== false;
        }

        // Delete specific relationship
        if (isset(self::$relations[$post_type]) && in_array($key, self::$relations[$post_type])) {
            $rel_table = $wpdb->prefix . $post_type . '_' . $key;
            return $wpdb->delete($rel_table, ['post_id' => $post_id], ['%d']) !== false;
        }

        // Set field to NULL
        $table = $wpdb->prefix . $post_type;
        return $wpdb->update($table, [$key => null], ['post_id' => $post_id]) !== false;
    }

    /**
     * Enhanced query with meta support
     */
    public static function query($args)
    {
        $post_type = $args['post_type'] ?? null;

        $hybrid_conditions = [];
        $wp_query_args = $args;

        // Process meta_query to separate hybrid vs standard conditions
        if ($post_type && isset(self::$schemas[$post_type]) && isset($args['meta_query']) && is_array($args['meta_query'])) {
            $original_meta_query = $args['meta_query'];
            $processed_wp_meta_query = [];

            if (isset($original_meta_query['relation'])) {
                $processed_wp_meta_query['relation'] = $original_meta_query['relation'];
            }

            foreach ($original_meta_query as $key_or_index => $condition) {
                if ($key_or_index === 'relation')
                    continue;

                if (is_array($condition) && isset($condition['key']) && isset(self::$schemas[$post_type][$condition['key']])) {
                    $hybrid_conditions[] = $condition;
                } elseif (is_array($condition)) {
                    $processed_wp_meta_query[] = $condition;
                }
            }

            // Remove meta_query if no standard WP conditions remain
            $has_wp_clauses = count($processed_wp_meta_query) > (isset($processed_wp_meta_query['relation']) ? 1 : 0);
            if (!$has_wp_clauses) {
                unset($wp_query_args['meta_query']);
            } else {
                $wp_query_args['meta_query'] = $processed_wp_meta_query;
            }
        }

        // Set context for filters (same as before, but now works for all queries)
        self::set_query_context($post_type, $hybrid_conditions, $args);

        // Add filters - these now work for both main and custom queries
        add_filter('posts_join', [self::class, 'join_tables'], 10, 2);
        add_filter('posts_where', [self::class, 'where_clause'], 10, 2);
        add_filter('posts_orderby', [self::class, 'orderby_clause'], 10, 2);

        // Execute query
        $query_obj = new Country_Filtered_Query($wp_query_args);

        // Remove filters
        remove_filter('posts_join', [self::class, 'join_tables'], 10);
        remove_filter('posts_where', [self::class, 'where_clause'], 10);
        remove_filter('posts_orderby', [self::class, 'orderby_clause'], 10);

        // Clear context
        self::clear_query_context();

        return $query_obj;
    }


    // Private methods
    private static function load_data($post_id, $post_type)
    {
        global $wpdb;
        $table = $wpdb->prefix . $post_type;

        // Load main table data
        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE post_id = %d",
            $post_id
        ), ARRAY_A);

        if ($data) {
            unset($data['post_id'], $data['created_at'], $data['updated_at']);
        } else {
            $data = [];
        }

        // Load relationship data
        if (isset(self::$relations[$post_type])) {
            foreach (self::$relations[$post_type] as $relation) {
                $data[$relation] = self::get_relation($post_id, $relation);
            }
        }

        return $data;
    }

    private static function save_data($post_id, $post_type, $data)
    {
        global $wpdb;
        $table = $wpdb->prefix . $post_type;

        // Separate meta fields from relationships
        $meta_data = [];
        $relation_data = [];

        $allowed_fields = array_keys(self::$schemas[$post_type]);
        $allowed_relations = self::$relations[$post_type] ?? [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_relations)) {
                $relation_data[$key] = $value;
            } elseif (in_array($key, $allowed_fields)) {
                $meta_data[$key] = self::sanitize_value($value, self::$schemas[$post_type][$key]);
            }
        }

        $success = true;

        // Save meta fields
        if (!empty($meta_data)) {
            $meta_data['post_id'] = $post_id;

            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT post_id FROM {$table} WHERE post_id = %d",
                $post_id
            ));

            if ($exists) {
                unset($meta_data['post_id']);
                $success = $wpdb->update($table, $meta_data, ['post_id' => $post_id]) !== false;
            } else {
                $success = $wpdb->insert($table, $meta_data) !== false;
            }
        }

        // Save relationships
        foreach ($relation_data as $key => $values) {
            $success = self::save_relation($post_id, $post_type, $key, $values) && $success;
        }

        // Clear cache
        unset(self::$cache[$post_id . '_' . $post_type]);

        return $success;
    }

    private static function get_relation($post_id, $relation)
    {
        global $wpdb;
        $post_type = get_post_type($post_id);
        $table = $wpdb->prefix . $post_type . '_' . $relation;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT value FROM {$table} WHERE post_id = %d ORDER BY sort_order ASC, id ASC",
            $post_id
        ), ARRAY_A);

        $values = [];
        foreach ($results as $row) {
            $value = maybe_unserialize($row['value']);
            $values[] = $value;
        }

        return $values;
    }

    private static function save_relation($post_id, $post_type, $relation, $values)
    {
        global $wpdb;
        $table = $wpdb->prefix . $post_type . '_' . $relation;

        // Clear existing
        $wpdb->delete($table, ['post_id' => $post_id], ['%d']);

        // Insert new values
        if (is_array($values)) {
            foreach ($values as $index => $value) {
                $wpdb->insert($table, [
                    'post_id' => $post_id,
                    'value' => maybe_serialize($value),
                    'sort_order' => $index
                ]);
            }
        }

        return true;
    }

    private static function sanitize_value($value, $type)
    {
        if ($value === null || $value === '')
            return null;

        $type = strtoupper($type);

        if (strpos($type, 'INT') !== false) {
            return (int) $value;
        } elseif (strpos($type, 'DECIMAL') !== false || strpos($type, 'FLOAT') !== false) {
            return (float) $value;
        } elseif (strpos($type, 'TEXT') !== false) {
            return sanitize_textarea_field($value);
        } else {
            return sanitize_text_field($value);
        }
    }

    // Query filter methods

    private static function set_query_context($post_type, $hybrid_conditions, $args = [])
    {
        self::$current_query_post_type = $post_type;
        self::$current_hybrid_meta_query = $hybrid_conditions;
        self::$current_orderby_args = $args; // Store the full args for orderby
    }

    private static function clear_query_context()
    {
        self::$current_query_post_type = null;
        self::$current_hybrid_meta_query = null;
        self::$current_orderby_args = null;
    }

    public static function set_main_query_context($post_type, $hybrid_conditions)
    {
        self::$current_query_post_type = $post_type;
        self::$current_hybrid_meta_query = $hybrid_conditions;
    }

    public static function clear_main_query_context()
    {
        self::$current_query_post_type = null;
        self::$current_hybrid_meta_query = null;
    }

    /**
     * Initialize global filters - call this once when WordPress loads
     */
    public static function init_global_filters()
    {
        add_filter('posts_join', [self::class, 'join_tables'], 10, 2);
        add_filter('posts_where', [self::class, 'where_clause'], 10, 2);
        add_filter('posts_orderby', [self::class, 'orderby_clause'], 10, 2);
    }

    public static function join_tables($join, $query)
    {
        global $wpdb;


        $post_type = null;

        if (self::$current_query_post_type) {

            $post_type = self::$current_query_post_type;
        } elseif ($query->is_main_query()) {

            $query_post_types = $query->get('post_type');
            if (is_array($query_post_types)) {
                $post_type = $query_post_types[0];
            } else {
                $post_type = $query_post_types ?: get_post_type();
            }
        }

        if (!$post_type || !isset(self::$schemas[$post_type])) {
            return $join;
        }

        $table = $wpdb->prefix . $post_type;

        if (strpos($join, $table) !== false) {
            return $join;
        }

        $join .= " LEFT JOIN {$table} ON {$wpdb->posts}.ID = {$table}.post_id ";

        return $join;
    }

    public static function where_clause($where, $query)
    {
        global $wpdb;

        // Determine post type and conditions
        $post_type = null;
        $hybrid_conditions = [];

        if (self::$current_query_post_type && self::$current_hybrid_meta_query) {
            // Use context if available (for custom queries)
            $post_type = self::$current_query_post_type;
            $hybrid_conditions = self::$current_hybrid_meta_query;
        } elseif ($query->is_main_query()) {
            // For main queries, you might set context elsewhere
            // This maintains backward compatibility
            return $where;
        }

        // Only proceed if we have conditions to apply
        if (!$post_type || !isset(self::$schemas[$post_type]) || empty($hybrid_conditions)) {
            return $where;
        }

        $table = $wpdb->prefix . $post_type;

        foreach ($hybrid_conditions as $condition) {
            if (!is_array($condition) || !isset($condition['key'])) {
                continue;
            }

            $key = $condition['key'];
            $value = $condition['value'] ?? '';
            $compare = strtoupper($condition['compare'] ?? '=');

            if (!isset(self::$schemas[$post_type][$key])) {
                continue;
            }

            $column = "{$table}.`{$key}`";

            switch ($compare) {
                case 'LIKE':
                    $where .= $wpdb->prepare(" AND {$column} LIKE %s", "%" . $wpdb->esc_like($value) . "%");
                    break;
                case '>=':
                    $where .= $wpdb->prepare(" AND {$column} >= %s", $value);
                    break;
                case '<=':
                    $where .= $wpdb->prepare(" AND {$column} <= %s", $value);
                    break;
                case '>':
                    $where .= $wpdb->prepare(" AND {$column} > %s", $value);
                    break;
                case '<':
                    $where .= $wpdb->prepare(" AND {$column} < %s", $value);
                    break;
                case '!=':
                    $where .= $wpdb->prepare(" AND {$column} != %s", $value);
                    break;
                case 'IN':
                    if (is_array($value) && !empty($value)) {
                        $placeholders = implode(',', array_fill(0, count($value), '%s'));
                        $where .= $wpdb->prepare(" AND {$column} IN ({$placeholders})", ...$value);
                    }
                    break;
                case 'NOT IN':
                    if (is_array($value) && !empty($value)) {
                        $placeholders = implode(',', array_fill(0, count($value), '%s'));
                        $where .= $wpdb->prepare(" AND {$column} NOT IN ({$placeholders})", ...$value);
                    }
                    break;
                case 'BETWEEN':
                    if (is_array($value) && count($value) == 2) {
                        $where .= $wpdb->prepare(" AND {$column} BETWEEN %s AND %s", $value[0], $value[1]);
                    }
                    break;
                default: // '='
                    $where .= $wpdb->prepare(" AND {$column} = %s", $value);
            }
        }

        return $where;
    }


    public static function orderby_clause($orderby, $query)
    {
        global $wpdb;

        $post_type = null;
        $orderby_param = null;
        $order = 'ASC';

        if (self::$current_query_post_type && isset(self::$current_orderby_args)) {

            $post_type = self::$current_query_post_type;
            $orderby_param = self::$current_orderby_args['orderby'] ?? null;
            $order = strtoupper(self::$current_orderby_args['order'] ?? 'ASC');
        } elseif ($query->is_main_query()) {

            $query_post_types = $query->get('post_type');
            if (is_array($query_post_types)) {
                $post_type = $query_post_types[0];
            } else {
                $post_type = $query_post_types ?: get_post_type();
            }
            $orderby_param = $query->get('orderby');
            $order = strtoupper($query->get('order', 'ASC'));
        }

        if (!$post_type || !isset(self::$schemas[$post_type])) {
            return $orderby;
        }

        if (is_string($orderby_param) && isset(self::$schemas[$post_type][$orderby_param])) {
            $table = $wpdb->prefix . $post_type;
            return "{$table}.`{$orderby_param}` {$order}";
        }

        return $orderby;
    }

    // Add this property to store orderby args
    private static $current_orderby_args = null;

    // Database maintenance utilities
    public static function optimize_tables()
    {
        global $wpdb;

        foreach (array_keys(self::$schemas) as $post_type) {
            $table = $wpdb->prefix . $post_type;
            $wpdb->query("OPTIMIZE TABLE {$table}");

            // Optimize relationship tables
            if (isset(self::$relations[$post_type])) {
                foreach (self::$relations[$post_type] as $relation) {
                    $rel_table = $wpdb->prefix . $post_type . '_' . $relation;
                    $wpdb->query("OPTIMIZE TABLE {$rel_table}");
                }
            }
        }
    }

    // Helper methods
    public static function get_schema($post_type)
    {
        return self::$schemas[$post_type] ?? [];
    }

    public static function get_relations($post_type)
    {
        return self::$relations[$post_type] ?? [];
    }
}

// Initialize HybridMeta

add_action('init', function () {
    HybridMeta::init_global_filters();
}, 5); // Early priority to ensure filters are ready

// WordPress-style functions
function get_custom_meta($post_id, $key = '', $single = true)
{
    return HybridMeta::get($post_id, $key, $single);
}

function add_custom_meta($post_id, $key, $value)
{
    return HybridMeta::add($post_id, $key, $value);
}

function update_custom_meta($post_id, $key, $value = null)
{
    return HybridMeta::update($post_id, $key, $value);
}

function delete_custom_meta($post_id, $key = '')
{
    return HybridMeta::delete($post_id, $key);
}

function custom_query($args)
{
    return HybridMeta::query($args);
}

// Auto-save on post save
add_action('save_post', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    $post_type = get_post_type($post_id);
    $schema = HybridMeta::get_schema($post_type);
    $relations = HybridMeta::get_relations($post_type);

    if (empty($schema) && empty($relations))
        return;

    $data = [];

    // Collect meta fields
    foreach ($schema as $field => $type) {
        if (isset($_POST[$field])) {
            $data[$field] = $_POST[$field];
        }
    }

    // Collect relationships
    foreach ($relations as $relation) {
        if (isset($_POST[$relation])) {
            $data[$relation] = is_array($_POST[$relation]) ? $_POST[$relation] : [$_POST[$relation]];
        }
    }

    if (!empty($data)) {
        update_custom_meta($post_id, $data);
    }
}, 20);

// Delete on post deletion
add_action('before_delete_post', function ($post_id) {
    delete_custom_meta($post_id);
});

// Scheduled maintenance tasks
add_action('wp_loaded', function () {
    // Weekly optimization
    if (!wp_next_scheduled('hybrid_meta_daily_optimization')) {
        wp_schedule_event(time(), 'weekly', 'hybrid_meta_daily_optimization');
    }

});

add_action('hybrid_meta_daily_optimization', function () {
    HybridMeta::optimize_tables();
});


// Add admin menu
add_action('admin_menu', function () {
    add_management_page(
        'HybridMeta Schema',
        'Schema Manager',
        'manage_options',
        'hybrid-meta-schema',
        'hybrid_meta_admin_page'
    );
});

function hybrid_meta_admin_page()
{
    if (isset($_POST['update_schema']) && wp_verify_nonce($_POST['_wpnonce'], 'update_schema')) {
        $results = [];
        foreach (['events', 'awards', 'company', 'testimonials', 'ogtv'] as $post_type) {
            $results[$post_type] = HybridMeta::force_update_schema($post_type);
        }
        echo '<div class="notice notice-success"><p>Schema update completed. Check error log for details.</p></div>';

        // Show results
        foreach ($results as $post_type => $result) {
            $status = $result ? 'Success' : 'Failed';
            echo "<p>{$post_type}: {$status}</p>";
        }
    }

    $health = HybridMeta::health_check();

    echo '<div class="wrap">';
    echo '<h1>HybridMeta Schema Manager</h1>';
    echo '<p>Status: ' . $health['status'] . '</p>';

    if (!empty($health['issues'])) {
        echo '<ul>';
        foreach ($health['issues'] as $issue) {
            echo '<li>' . esc_html($issue) . '</li>';
        }
        echo '</ul>';
    }

    echo '<form method="post">';
    wp_nonce_field('update_schema');
    echo '<input type="submit" name="update_schema" value="Force Update Schema" class="button-primary">';
    echo '</form>';
    echo '</div>';
}


add_action('wp_loaded', function () {
    $health = HybridMeta::health_check();
    if ($health['status'] === 'error') {
        // Alert your monitoring system
        error_log('CRITICAL: HybridMeta schema issues detected');
    }
});

/*
========== COMPLETE DOCUMENTATION ==========

1. BASIC USAGE (WordPress Standard Pattern):

   // Get single value
   $date = get_cutom_meta(123, 'event_date');
   
   // Get all meta
   $all_meta = get_custom_meta(123);
   
   // Update single field
   update_custom_meta(123, 'event_date', '2024-06-01');
   
   // Update multiple fields
   update_custom_meta(123, [
       'event_date' => '2024-06-01',
       'start_time' => '10:00',
       'color' => '#ff0000'
   ]);
   
   // Delete specific field
   delete_custom_meta(123, 'event_date');
   
   // Delete all meta
   delete_custom_meta(123);

2. REPEATABLE DATA (Relationships):

   // Save repeatable data
   update_custom_meta(123, 'speakers', [
       'John Doe',
       'Jane Smith',
       ['name' => 'Bob Wilson', 'bio' => 'Expert in...'],
       ['name' => 'Alice Brown', 'company' => 'Tech Corp']
   ]);
   
   // Get repeatable data
   $speakers = get_custom_meta(123, 'speakers'); // Returns array
   
   // Add to existing repeatable data
   $current = get_custom_meta(123, 'speakers');
   $current[] = 'New Speaker';
   update_custom_meta(123, 'speakers', $current);

3. ADVANCED QUERIES:

   // Basic meta query
   $events = custom_query([
       'post_type' => 'events',
       'meta_query' => [
           [
               'key' => 'event_date',
               'value' => '2024-06-01',
               'compare' => '>='
           ]
       ]
   ]);
   
   // Multiple conditions
   $events = custom_query([
       'post_type' => 'events',
       'meta_query' => [
           [
               'key' => 'event_date',
               'value' => ['2024-06-01', '2024-12-31'],
               'compare' => 'BETWEEN'
           ],
           [
               'key' => 'color',
               'value' => '#ff0000',
               'compare' => '='
           ]
       ],
       'orderby' => 'event_date',
       'order' => 'ASC'
   ]);
   
   // Search in text fields
   $results = custom_query([
       'post_type' => 'events',
       'meta_query' => [
           [
               'key' => 'description',
               'value' => 'conference',
               'compare' => 'LIKE'
           ]
       ]
   ]);

4. WORKING WITH RELATIONSHIPS:

   // Company socials (repeatable)
   update_custom_meta(456, 'socials', [
       ['platform' => 'twitter', 'url' => 'https://twitter.com/company'],
       ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company'],
       ['platform' => 'facebook', 'url' => 'https://facebook.com/company']
   ]);
   
   // Get socials
   $socials = get_custom_meta(456, 'socials');
   foreach ($socials as $social) {
       echo $social['platform'] . ': ' . $social['url'];
   }
   
   // Update specific social
   $socials = get_custom_meta(456, 'socials');
   $socials[0]['followers'] = 1000;
   update_custom_meta(456, 'socials', $socials);

5. FORM INTEGRATION:

   // In your form
   <input name="event_date" type="date" value="<?php echo get_custom_meta($post->ID, 'event_date'); ?>">
   <input name="start_time" type="time" value="<?php echo get_custom_meta($post->ID, 'start_time'); ?>">
   
   // For repeatable fields
   <div id="speakers">
       <?php foreach (get_custom_meta($post->ID, 'speakers') as $speaker): ?>
           <input name="speakers[]" value="<?php echo esc_attr(is_array($speaker) ? $speaker['name'] : $speaker); ?>">
       <?php endforeach; ?>
   </div>

6. SCHEMA AND VALIDATION:

   // Get available fields for post type
   $fields = HybridMeta::get_schema('events');
   
   // Get available relationships
   $relations = HybridMeta::get_relations('events');
   
   // Custom validation (add to save_post hook)
   add_action('save_post', function($post_id) {
       if (get_post_type($post_id) === 'events') {
           $date = get_custom_meta($post_id, 'event_date');
           if ($date && strtotime($date) < time()) {
               // Handle past date validation
           }
       }
   });

7. PERFORMANCE TIPS:

   // Cache frequently accessed data
   $event_meta = get_custom_meta(123); // Gets all fields in one query
   $date = $event_meta['event_date'];
   $time = $event_meta['start_time'];
   
   // Use IN queries for multiple posts
   $events = custom_query([
       'post_type' => 'events',
       'meta_query' => [
           [
               'key' => 'color',
               'value' => ['#ff0000', '#00ff00', '#0000ff'],
               'compare' => 'IN'
           ]
       ]
   ]);

8. TABLE STRUCTURE:

   Tables created:
   - wp_events (main meta fields)
   - wp_events_speakers (relationship data)
   - wp_events_companies (relationship data)
   - wp_company (main meta fields)
   - wp_company_socials (relationship data)
   - wp_company_locations (relationship data)
   

This system provides:
- Standard WordPress function patterns
- Better performance than wp_postmeta
- Full support for repeatable/relationship data
- Simple, clean code structure
- Production-ready error handling
- Automatic data sanitization
- Built-in caching
*/