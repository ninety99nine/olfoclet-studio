<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\TopicResource;
use App\Http\Resources\TopicResources;

class TopicController extends Controller
{
    public function showTopics(Project $project)
    {
        $searchWord = request()->input('search');
        $hasSearchWord = request()->filled('search');
        $time = $hasSearchWord ? now()->addHour() : now()->addDay();
        $pageNumber = ($number = (int) request()->input('page')) > 0 ? $number : 1;
        $perPage = ($number = (int) request()->input('per_page')) > 0 ? $number : 15;

        /// Retrieve the result from the cache or make a request and cache the response for one day
        $response = $project->topics()->whereIsRoot()->withCount('children')->search($searchWord)->latest()->paginate();

        return new TopicResources($response);
    }

    public function showTopic(Project $project, Topic $topic, $type = null)
    {
        $searchWord = request()->input('search');
        $hasSearchWord = request()->filled('search');
        $time = $hasSearchWord ? now()->addHour() : now()->addDay();
        $pageNumber = ($number = (int) request()->input('page')) > 0 ? $number : 1;
        $perPage = ($number = (int) request()->input('per_page')) > 0 ? $number : 15;

        if( $type == 'children') {

            $response = $topic->children()->withCount('children')->search($searchWord)->latest()->paginate($perPage);

        }else if( $type == 'descendants') {

            $response = $topic->descendants()->withCount('descendants')->search($searchWord)->latest()->paginate($perPage);

        }else if( $type == 'ancestors') {

            $response = $topic->ancestors()->withCount('ancestors')->search($searchWord)->latest()->paginate($perPage);

        }else if( $type == 'parent') {

            $response = $topic->parent;

        }else{

            $response = $topic;

        }

        if($response instanceOf Topic) {

            return new TopicResource($response);

        }else{

            return new TopicResources($response);

        }

    }
}
