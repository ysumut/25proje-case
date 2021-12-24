<?php

namespace App\Http\Controllers;

use App\Models\Post\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'user_id' => ['string','exists:users,user_id'],
            'page' => ['integer'],
        ]);

        if($validator->fails()) {
            return response([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $user_id = $request->get('user_id');
        $page = $request->get('page');

        // Adjust page number
        if (!$page) {
            $page = 1;
        }
        else if($page > 0) {
            $page++;
        }

        // Get posts
        $posts = Post::with('user','postLike.user.tipolojiPoint.resultType','bookmark','source','content.type')
            ->select('id','cover','thumbnail','created_at','user_id')
            ->where('user_id', '=', $user_id)
            ->where('is_active', '=', true)
            ->paginate(1, ['*'],'page', $page);

        $data = $this->adjustData($posts, $user_id);

        return $this->adjustResponse($data);
    }

    private function adjustData($posts, $user_id): array
    {
        $i = 0;
        $data = [];

        foreach ($posts as $p) {
            //post
            $data[$i]['id'] = $p->id;
            $data[$i]['cover'] = $p->cover;
            $data[$i]['thumbnail'] = $p->thumbnail;
            $data[$i]['created_at'] = $p->created_at->format('d.m.Y H:i');

            // user
            $data[$i]['user_id'] = $p->user->user_id;
            $data[$i]['name'] = $p->user->name;
            $data[$i]['title'] = $p->user->title;
            $data[$i]['image'] = $p->user->image;

            // text
            $text = $p->content->filter(function ($value) {
                return $value->type->name == 'text';
            })->first();
            $data[$i]['text'] = $text ? $text->name_tr : '';

            // is like
            $data[$i]['isLike'] = (bool) $p->postLike->filter(function ($value) use ($user_id) {
                return $value->user_id == $user_id;
            })->first();

            // is bookmark
            $data[$i]['isBookmark'] = (bool) $p->bookmark->filter(function ($value) use ($user_id) {
                return $value->user_id == $user_id;
            })->first();

            // like count
            $data[$i]['likeCount'] = $p->postLike->count();

            // source
            $data[$i]['source'] = $p->source;

            // content
            $data[$i]['content'] = [];
            foreach ($p->content as $c) {
                $data[$i]['content'][] = [
                    'id' => $c->id,
                    'name' => $c->name_tr,
                    'content_type' => $c->type->name,
                    'order' => $c->order,
                    'url' => $c->url,
                    'width' => $c->width,
                    'height' => $c->height,
                    'source' => '',
                ];
            }

            // tree likes
            $like_count = 0;
            foreach ($p->postLike as $like) {
                if($like_count++ == 3) {
                    break;
                }

                $like_user = $like->user->toArray();
                $like_user['created_at'] = Carbon::make($like_user['created_at'])->format('d.m.Y H:i');

                $tipoloji = [];
                foreach ($like_user['tipoloji_point'] as $tip) {
                    $tipoloji[] = [
                        'value' => $tip['value'],
                        'reault_type_id' => $tip['reault_type_id'],
                        'name' => $tip['result_type']['name'],
                    ];
                }
                unset($like_user['tipoloji_point']);
                $like_user['tiploji'] = $tipoloji;

                $data[$i]['treeLikes'][] = $like_user;
            }
        }

        return array_values($data);
    }

    private function adjustResponse($data): array
    {
        $post_report_type = [
            ['id' => 1, 'name' => 'Şiddet'],
            ['id' => 2, 'name' => 'Küfür'],
            ['id' => 3, 'name' => 'Irkçı'],
            ['id' => 4, 'name' => 'Cinsellik'],
            ['id' => 5, 'name' => 'Diğer'],
        ];

        return [
            'result' => [
                'post' =>$data,
                'post_report_type' => $post_report_type,
                'matched' => null,
                'value_sended' => null
            ],
            'result_message' => [
                'type' => 'success',
                'title' => 'success',
                'message' => 'İçerik listelendi'
            ]
        ];
    }
}
