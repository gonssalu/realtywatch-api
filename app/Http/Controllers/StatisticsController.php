<?php

namespace App\Http\Controllers;

use App\Models\Characteristic;
use App\Models\Property;
use App\Models\PropertyList;
use App\Models\PropertyOffer;
use App\Models\Tag;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function statistics(Request $request)
    {
        $user = $request->user();
        $statistics = [
            'total' => [
                'properties' => 0,
                'tags' => 0,
                'lists' => 0,
                'offers' => 0,
            ],
            'properties' => [
                'no_type' => 0,
                'house' => 0,
                'apartment' => 0,
                'office' => 0,
                'shop' => 0,
                'warehouse' => 0,
                'garage' => 0,
                'land' => 0,
                'other' => 0,
            ],
            'listings' => [
                'none' => 0,
                'sale' => 0,
                'rent' => 0,
                'both' => 0,
            ],
            'tags' => [],
            'lists' => []
        ];

        $statistics['total']['properties'] = $user->properties()->count();
        $statistics['total']['offers'] = $user->allOffers()->count();
        $statistics['total']['tags'] = $user->tags()->count();
        $statistics['total']['lists'] = $user->lists()->count();

        foreach ($statistics['properties'] as $key => $value) {
            $keySearch = $key == 'no_type' ? null : $key;
            $qq = $user->properties()->where('type', $keySearch);
            $statistics['properties'][$key] = array(
                'count' => (clone $qq)->count(),
                'avg' => (clone $qq)->where('rating', '!=', 0)->where('rating', '!=', null)->avg('rating'),
                'price' => [
                    'sale' => (clone $qq)->where('current_price_sale', "!=", null)->avg('current_price_sale'),
                    'rent' => $qq->where('current_price_rent', "!=", null)->avg('current_price_rent'),
                ]
            );
        }

        foreach ($statistics['listings'] as $key => $value) {
            $statistics['listings'][$key] = $user->properties()->where('listing_type', $key)->count();
        }

        $tags = $user->tags()->get();
        foreach ($tags as $tag) {
            $statistics['tags'][] = array(
                'name' => $tag->name,
                'count' => $tag->properties()->count(),
                'avg' => $tag->properties()->where('rating', '!=', 0)->where('rating', '!=', null)->avg('rating'),
                'price' => [
                    'sale' => $tag->properties()->where('current_price_sale', "!=", null)->avg('current_price_sale'),
                    'rent' => $tag->properties()->where('current_price_rent', "!=", null)->avg('current_price_rent'),
                ]
            );
        }

        // order $statistics['tags'] by avg and take only the top 10
        usort($statistics['tags'], function ($a, $b) {
            return $a['avg'] < $b['avg'];
        });
        $statistics['tags'] = array_slice($statistics['tags'], 0, 5);

        $lists = $user->lists()->get();
        foreach ($lists as $list) {
            $statistics['lists'][] = array(
                'name' => $list->name,
                'count' => $list->properties()->count(),
                'avg' => $list->properties()->where('rating', '!=', 0)->where('rating', '!=', null)->avg('rating'),
                'price' => [
                    'sale' => $list->properties()->where('current_price_sale', "!=", null)->avg('current_price_sale'),
                    'rent' => $list->properties()->where('current_price_rent', "!=", null)->avg('current_price_rent'),
                ]
            );
        }

        // order $statistics['lists'] by avg and take only the top 10
        usort($statistics['lists'], function ($a, $b) {
            return $a['avg'] < $b['avg'];
        });
        $statistics['lists'] = array_slice($statistics['lists'], 0, 5);
        return $statistics;
    }
}
