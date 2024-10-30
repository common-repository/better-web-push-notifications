<?php

if (!class_exists('Ecomfit_Order')) {
    class Ecomfit_Order
    {
        public function get($order_id)
        {
            $order = wc_get_order($order_id);
            if (!$order) {
                return null;
            }
            $result = array(
                'id' => $order->get_id(),
                'cart_token' => $order->get_meta('_cart_token'),
                'total_price' => $order->get_total(),
                'item_count' => 0,
                'items' => [],
                'session' => null,
                'customer' => null,
                'status' => $order->get_status(),
                'created_at' => $order->get_date_created()->format('c'),
                'data' => $order->get_data(),
            );

            $items = $order->get_items();
            foreach ($items as $item) {
                if ($item) {
                    $product = $item->get_product();
                    if ($product && is_a($product, 'WC_Product')) {
                        array_push($result['items'], array(
                            "product_id" => $item->get_product_id(),
                            "title" => $product->get_title(),
                            "name" => $item->get_name(),
                            "price" => floatval($product->get_price()),
                            "total" => floatval($item->get_total()),
                            "quantity" => intval($item->get_quantity()),
                            "variant_id" => $item->get_variation_id(),
                            "variant_url" => $product->get_permalink(),
                            "url" => get_permalink($item->get_product_id()),
                            "data" => $item->get_data(),
                        ));
                        $result['item_count']++;
                    }
                }
            }

            return $result;
        }

        public function gets($from, $to, $limit, $offset)
        {
            $date_query = array(
                'inclusive' => true,
            );
            if ($from) {
                $date_query = array_merge($date_query, array('after' => $from));
            }
            if ($to) {
                $date_query = array_merge($date_query, array('before' => $to));
            }
            $query = array(
                'post_type' => 'shop_order',
                'posts_per_page' => intval($limit),
                'offset' => intval($offset),
                'post_status' => array(
                    'wc-on-hold',
                    'wc-completed',
                    'wc-pending',
                    'wc-processing',
                    'wc-cancelled',
                    'wc-refunded',
                    'wc-failed',
                ),
                'date_query' => array($date_query),
                'orderby' => 'date_created',
                'order' => 'ASC',
            );
            $wp_query = new WP_Query($query);
            $orders = $wp_query->posts;
            $result = array();
            foreach ($orders as $order) {
                $item = $this->get($order->ID);
                if (sizeof($item)) {
                    array_push($result, $item);
                }
            }

            return $result;
        }

    }
}
