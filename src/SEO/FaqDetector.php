<?php

namespace ASO\SEO;

if (! defined('ABSPATH')) {
    exit;
}

class FaqDetector
{

    public function analyze()
    {

        $result = array(
            'faq_schema' => false,
            'faq_block'  => false,
            'source'     => '',
            'score'      => 0,
        );

        // $schema = $this->detect_schema();
        $schema = $this->has_faq_schema();

        if ($schema) {

            $result['faq_schema'] = true;

            $result['source'] = 'schema';

            $result['score'] += 10;
        }

        $block = $this->detect_blocks();

        if ($block) {

            $result['faq_block'] = true;

            $result['source'] = $block;

            $result['score'] += 5;
        }

        return $result;
    }

    private function detect_schema()
    {

        $response = wp_remote_get(
            home_url('/')
        );

        if (is_wp_error($response)) {
            return false;
        }

        $html = wp_remote_retrieve_body(
            $response
        );

        if (
            false !== stripos(
                $html,
                '"@type":"FAQPage"'
            )
        ) {
            return 'custom';
        }

        if (
            false !== stripos(
                $html,
                'acceptedAnswer'
            )
        ) {
            return 'schema';
        }

        return false;
    }

    private function detect_blocks()
    {

        $posts = get_posts(
            array(
                'post_type'      => 'any',
                'post_status'    => 'publish',
                'posts_per_page' => 20,
            )
        );

        $supported = array(
            'yoast/faq-block'      => 'yoast',
            'yoast-seo/faq-block'  => 'yoast',

            'rank-math/faq-block'  => 'rankmath',

            'aioseo/faq'           => 'aioseo',
            'aioseo/faq-block'     => 'aioseo',
            'aioseo/faq-page'      => 'aioseo',
        );

        foreach ($posts as $post) {

            foreach ($supported as $block => $source) {

                if (
                    has_block(
                        $block,
                        $post
                    )
                ) {
                    return $source;
                }
            }
        }

        return false;
    }
    private function has_faq_schema()
    {

        $posts = get_posts(
            array(
                'post_type'      => array(
                    'post',
                    'page',
                ),

                'post_status'    => 'publish',

                'posts_per_page' => 10,
            )
        );

        foreach ($posts as $post) {

            $response = wp_remote_get(
                get_permalink(
                    $post->ID
                )
            );

            if (is_wp_error($response)) {
                continue;
            }

            $html = wp_remote_retrieve_body(
                $response
            );

            $patterns = array(
                'FAQPage',

                '"@type":"FAQPage"',
                '"@type": "FAQPage"',

                '"@type":["FAQPage"]',
                '"@type": ["FAQPage"]',

                'acceptedAnswer',
                'mainEntity',
                'Question',
            );

            foreach ($patterns as $pattern) {

                if (
                    false !== stripos(
                        $html,
                        $pattern
                    )
                ) {
                    return true;
                }
            }
        }

        return false;
    }
}
