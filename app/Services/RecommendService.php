<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;

class RecommendService
{
    public function recommend(Order $order, int $count_to_recommend): array
    {
        $recommended_products = [];

        $product_ids = OrderProduct::where('order_id', $order->id)
            ->select('product_id')->pluck('product_id')->toArray();
        // dd($product_ids);
        foreach ($product_ids as $product_id) {
            if (count($recommended_products) >= $count_to_recommend) {
                return $recommended_products;
            }
            // create graph
            $this->createGraph($product_id);

        }

        // apply breadth first search
        $this->breadthFirstSearch([]);

        return $recommended_products;

    }

    private function breadthFirstSearch($graph): array
    {
        if (count($graph) == 0) {
            echo 'graph is empty';
            return [];
        }

        $search_queue = new \Ds\Queue();

        // var_dump($node);

        // enqueue the node neighbors

        foreach ($graph[array_key_first($graph)] as $nodes) {
            $search_queue->push($nodes);
        }

        $added_nodes = [];

        while (!$search_queue->isEmpty()) {
            $peek_value = $search_queue->pop();

            echo '***************** $peek_value =' . $peek_value . ' ***********<br>';

            // enqueue the node that got dequeued neighbors
            foreach ($graph[$peek_value] as $node) {
                $added = array_search($node, $added_nodes, true);

                if ($added === false) {
                    $search_queue->push($node);
                    array_push($added_nodes, $node);
                }

            }
        }

        return [];
    }

    private function createGraph($product_id)
    {
        // echo '$product_id=' . $product_id;

        $this->productsBoughtWith($product_id);

    }

    private function productsBoughtWith($product_id)
    {
        $orders_with_product_id = DB::select(
            DB::raw('SELECT bought_products.product_id AS original_product_id, bought_with_products.product_id AS
    bought_with,
    count(*) as times_bought_together
    FROM order_products AS bought_products
    INNER JOIN order_products AS bought_with_products
    ON bought_products.order_id = bought_with_products.order_id
    AND bought_products.product_id != bought_with_products.product_id
    where bought_products.product_id=?
    GROUP BY bought_products.product_id
    Order By times_bought_together DESC;'),
            [$product_id]);

        dd($orders_with_product_id);

    }
}