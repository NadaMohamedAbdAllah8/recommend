<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;

class RecommendService
{
    public function recommend(Order $order): array
    {
        $recommended_products = [];

        $graph = [];

        $product_ids = OrderProduct::where('order_id', $order->id)
            ->select('product_id')->pluck('product_id')->toArray();

        // create graph
        $graph = $this->createGraph($product_ids);

        // apply breadth first search
        $recommended = $this->breadthFirstSearch($graph, $product_ids);

        return $recommended;
    }

    private function breadthFirstSearch($graph, $bought_products_ids): array
    {
        $recommended = [];

        if (count($graph) == 0) {
            return [];
        }

        $search_queue = new \Ds\Queue();

        // var_dump($node);

        // enqueue the node neighbors

        foreach ($graph[array_key_first($graph)] as $nodes) {
            $search_queue->push($nodes);
        }

        $added_nodes = [];

        while (! $search_queue->isEmpty()) {
            $peek_value = $search_queue->pop();

            $peek_is_bought_already = array_search($peek_value, $bought_products_ids);

            if ($peek_is_bought_already === false) {
                array_push($recommended, $peek_value);
            }

            // enqueue the node that got dequeued neighbors
            foreach ($graph[$peek_value] as $node) {
                $added = array_search($node, $added_nodes, true);

                if ($added === false) {
                    $search_queue->push($node);
                    array_push($added_nodes, $node);
                }
            }
        }

        return $recommended;
    }

    private function createGraph($product_ids)
    {
        $graph = [];

        // for the product ids
        foreach ($product_ids as $product_id) {
            // create graph
            $graph[$product_id] = $this->productsBoughtWith($product_id);
        }

        // for the bought with product ids
        foreach ($graph as $key => $values) {
            foreach ($values as $value) {
                if (! array_key_exists($value, $graph)) {
                    $graph[$value] = $this->productsBoughtWith($value);
                }
            }
        }

        return $graph;
    }

    private function productsBoughtWith($product_id)
    {
        $bought_with_ids = [];

        $results = DB::select(
            DB::raw('select distinct t1.product_id as bought, t2.product_id as bought_with
from order_products  as t1 join
     order_products as  t2
     on t1.order_id = t2.order_id
where t1.product_id <> t2.product_id and t1.product_id=?;'),
            [$product_id]);

        foreach ($results as $result) {
            array_push($bought_with_ids, $result->bought_with);
        }

        return $bought_with_ids;
    }
}
