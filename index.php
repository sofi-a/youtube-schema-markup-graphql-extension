<?php
/*
Plugin Name: YouTube Schema Markup GraphQL Extension
Plugin URI: https://github.com/sofi-a/youtube-schema-markup-graphql-extension
Description: A WPGraphQL extension that scrapes a youtube video page and extracts the schema markup.
Version: 1.0
Author: Sofonias Abathun
Author URI: https://github.com/sofi-a/
Text Domain: wp-graph-ql/youtube-schema-markup
License: GPL3
YouTube Schema Markup GraphQL Extension is free software. You can redistribute it and/or modify
it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 2 of the license, or
any later version.
YouTube Schema Markup GraphQL Extension is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU GENERAL PUBLIC LICENSE for more details.
You should have received a copy of the GNU Public License
along with YouTube Schema Markup GraphQL Extension. If not see https://www.gnu.org/licenses/gpl.html.
*/

use WpGraphQL\YoutubeSchemaMarkup\Utils\Scrapper;

require_once __DIR__ . '/vendor/autoload.php';

add_action('admin_init', 'check_wp_graphql_activation');

function check_wp_graphql_activation()
{
    if (is_admin() && current_user_can('activate_plugins') && !is_plugin_active('wp-graphql/wp-graphql.php')) {
        add_action('admin_notices', 'youtube_schema_markup_plugin_notice');

        deactivate_plugins(plugin_basename(__FILE__));
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}

function youtube_schema_markup_plugin_notice()
{
?>
    <div class="error">
        <p>Sorry, but the YouTube Schema Markup GraphQL Extension plugin requires the WP GraphQL plugin to be installed and activated.</p>
    </div>
<?php
}

add_action('graphql_register_types', 'extend_wpgraphql_schema');

function extend_wpgraphql_schema()
{
    register_graphql_field('RootQuery', 'videoSchema', [
        'type' => 'String',
        'args' => [
            'videoId' => [
                'type' => 'String',
            ],
        ],
        'description' => __('Gets a youtube video schema', 'wp-graph-ql/youtube-schema-markup'),
        'resolve' => function ($source, $args, $context, $info) {
            if (isset($args['videoId'])) {
                $videoId = $args['videoId'];
                $url = "https://www.youtube.com/watch?v=$videoId";
            }
            $scrapper = new Scrapper($url);
            $schema = $scrapper->getSchemaMarkup();
            return $schema;
        }
    ]);
};
