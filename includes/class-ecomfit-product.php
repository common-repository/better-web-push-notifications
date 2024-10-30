<?php

if (!class_exists('Ecomfit_Product')) {
    class Ecomfit_Product
    {
        public function get($product_id)
        {
            $post = get_post($product_id);
            if ($post->post_status == 'publish' || $post->post_status == 'trash') {
                $cat_product = get_the_terms($product_id, 'product_cat');
                $tag_product = get_the_terms($product_id, 'product_tag');
                $category = array();
                $tags = array();
                if ($cat_product) {
                    foreach ($cat_product as $key) {
                        array_push($category, $key->name);
                    }
                }
                if ($tag_product) {
                    foreach ($tag_product as $key) {
                        array_push($tags, $key->name);
                    }
                }
                $query_variant = array(
                    'post_type' => array('product_variation'),
                    'post_parent' => $product_id,
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                );
                $posts_product_variants = new WP_Query($query_variant);
                $posts_product_variants = $posts_product_variants->posts;
                $data_product_variants = array();
                foreach ($posts_product_variants as $post_variant) {
                    $data_variant = array(
                        'id' => $post_variant->ID,
                        'url' => get_permalink($post_variant->ID),
                        'title' => $post_variant->post_title,
                        'name' => $post_variant->post_title,
                        'image' => get_the_post_thumbnail_url($post_variant->ID),
                        'compare_at_price' => get_post_meta($post_variant->ID, '_regular_price', true),
                        'price' => get_post_meta($post_variant->ID, '_sale_price', true),
                    );
                    array_push($data_product_variants, $data_variant);
                }
                $data = array(
                    'id' => $post->ID,
                    'url' => get_permalink($post->ID),
                    'title' => $post->post_title,
                    'name' => $post->post_title,
                    'image' => get_the_post_thumbnail_url($post->ID),
                    'status' => $post->post_status,
                    'compare_at_price' => get_post_meta($post->ID, '_regular_price', true),
                    'price' => get_post_meta($post->ID, '_sale_price', true),
                    'postParent' => $post->post_parent,
                    'variantType' => $post->post_type,
                    'defaultAttributes' => get_post_meta($post->ID, '_default_attributes', true),
                    'variants' => $data_product_variants,
                    'tags' => ($tags && is_array($tags) && sizeof($tags)) ? implode($tags, ' ,') : ''
                );

                if (!$posts_product_variants) {
                    $variant = $data;
                    unset($variant['variantType']);
                    unset($variant['variants']);
                    unset($variant['defaultAttributes']);
                    array_push($data['variants'], $variant);
                }

                return $data;
            }

            return null;
        }

        public function gets($limit, $offset)
        {
            $query = array(
                'post_type' => array('product'),
                'posts_per_page' => intval($limit),
                'offset' => intval($offset),
                'post_status' => 'publish'
            );
            $posts_product = new WP_Query($query);
            $posts_product = $posts_product->posts;
            $data_products = array();
            foreach ($posts_product as $post) {
                array_push($data_products, $this->get($post->ID));
            }

            return $data_products;
        }

    }
}
