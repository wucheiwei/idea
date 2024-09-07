<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}
    /**
     * @OA\Get(
     *     path="/article/index",
     *     tags={"Article"},
     *     summary="Get all articles",
     *     @OA\Response(
     *         response=200,
     *         description="A list of articles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="title", type="string", description="Article title")
     *             )
     *         )
     *     )
     * )
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json(Article::select(['title'])
            ->get());
    }
    /**
     * @OA\Post(
     *     path="/article/create",
     *     tags={"Article"},
     *     summary="創建新分類",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", description="文章分類"),
     *             @OA\Property(
     *                 property="article",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="title", type="string", description="文章標題"),
     *                     @OA\Property(property="content", type="string", description="文章內容"),
     *                     @OA\Property(property="language", type="string", description="文章語言")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="創建成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", description="Success message")
     *         )
     *     )
     * )
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'article' => 'required|array|',
            'article.*.title' => 'required|string',
            'article.*.content' => 'required|string',
            'article.*.language' => 'required|string',
        ]);
        $articleId = Article::create(['title' => $request->title])->id;
        $articles = $request->input('article');
        foreach ($articles as $article) {
            Translation::create([
                'article_id' => $articleId,
                'title' => $article['title'],
                'content' => $article['content'],
                'language' => $article['language'],
            ]);
        }
        return response()->json(['message' => '創建成功']);
    }
    /**
     * @OA\Get(
     *     path="/article/{id}",
     *     tags={"Article"},
     *     summary="Get article by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="Article ID"),
     *             @OA\Property(property="title", type="string", description="Article title")
     *         )
     *     )
     * )
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        return response()->json(Article::select(['id', 'title'])->where('id', $id)->first());
    }
    /**
     * @OA\Put(
     *     path="/article/update",
     *     tags={"Article"},
     *     summary="更新分類",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="Article ID"),
     *             @OA\Property(property="title", type="string", description="Article title")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="更新完成",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", description="Success message")
     *         )
     *     ),
     *
     *      @OA\Response(
     *         response=404,
     *         description="查無此資料"
     *     )
     * )
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'title' => 'required|string'
        ]);
        if (Article::where('id', $request->id)->update(['title' => $request->title])) {
            return response()->json(['message' => '更新完成']);
        } else {
            return response()->json(['message' => '查無資料']);
        }
    }
    /**
     * @OA\Delete(
     *     path="/article/delete/{id}",
     *     tags={"Article"},
     *     summary="刪除分類",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="刪除完成",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", description="Success message")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id)
    {
        Article::where('id', $id)->delete();
        Translation::where('article_id', $id)->delete();
        return response()->json(['message' => '刪除完成']);
    }
}
