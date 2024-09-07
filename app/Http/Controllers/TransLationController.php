<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransLationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}
    /**
     * @OA\Get(
     *     path="/translation/index",
     *     tags={"Translation"},
     *     summary="Get translations by article ID and language",
     *     @OA\Parameter(
     *         name="article_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of translations",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="title", type="string", description="文章標題"),
     *                 @OA\Property(property="content", type="string", description="文章內容")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'article_id' => 'required|integer',
            'language' => 'nullable|string'
        ]);
        $language = $request->input('language', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en');
        return response()->json(Translation::select(['title', 'content'])
            ->where('article_id', $request->article_id)
            ->where('language', $language)
            ->get());
    }
    /**
     * @OA\Post(
     *     path="/translation/create",
     *     tags={"Translation"},
     *     summary="建立新文章",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="article_id", type="integer", description="Article ID"),
     *             @OA\Property(
     *                 property="article",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="title", type="string", description="文章標題"),
     *                     @OA\Property(property="content", type="string", description="文章內容"),
     *                     @OA\Property(property="language", type="string", description="語系")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="創建完成",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", description="Success message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="語言已存在",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", description="Error message")
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
            'article_id' => 'required|integer',
            'article' => 'array||required|string',
            'article.*.title' => 'required|string',
            'article.*.content' => 'required|string',
            'article.*.language' => 'required|string',
        ]);
        $articles = $request->input('article');

        DB::beginTransaction();
        try {
            foreach ($articles as $article) {
                if (Translation::where('language', $article['language'])->exists()) {
                    DB::rollBack();
                    return response()->json(['message' => $article['language'] . '語言已存在'], 400);
                }
                Translation::create([
                    'article_id' => $request->article_id,
                    'title' => $article['title'],
                    'content' => $article['content'],
                    'language' => $article['language'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => '創建完成'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => '創建失敗', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/translation/{id}",
     *     tags={"Translation"},
     *     summary="Get translation by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="Translation ID"),
     *             @OA\Property(property="title", type="string", description="Translation title"),
     *             @OA\Property(property="content", type="string", description="Translation content"),
     *             @OA\Property(property="language", type="string", description="Translation language")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found"
     *     )
     * )
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        return response()->json(Translation::select()->where('id', $id)->first());
    }
/**
 * @OA\Put(
 *     path="/translation/update/{id}",
 *     tags={"Translation"},
 *     summary="update Translation",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="article_id", type="integer", description="Article ID"),
 *             @OA\Property(property="title", type="string", description="Translation title"),
 *             @OA\Property(property="content", type="string", description="Translation content"),
 *             @OA\Property(property="language", type="string", description="Translation language")
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
 *     @OA\Response(
 *         response=400,
 *         description="語言已存在",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", description="Error message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="查無此資料"
 *     )
 * )
 *
 * @param Request $request
 * @param int $id
 *
 * @return \Illuminate\Http\JsonResponse
 */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'article_id' => 'required|integer',
            'title' => 'required|string',
            'content' => 'required|string',
            'language' => 'required|string',
        ]);
        if (Translation::where([
            ['article_id', '=', $request->article_id],
            ['id', '!=', $request->id],
            ['language', '=', $request->language]
        ])->exists())  return response()->json(['message' => $request->language . '語言已存在', 400]);
        if (Translation::where('id', $id)->update([
            'title' => $request->title,
            'content' => $request->content,
            'language' => $request->language
        ])) {
            return response()->json(['message' => '更新完成']);
        } else {
            return response()->json(['message' => '查無此資料']);
        }
    }
    /**
     * @OA\Delete(
     *     path="/translation/delete/{id}",
     *     tags={"Article"},
     *     summary="刪除文章",
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
        Translation::where('id', $id)->delete();
        return response()->json(['message' => '刪除完成']);
    }
}
