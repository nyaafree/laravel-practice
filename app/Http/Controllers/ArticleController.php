<?php


namespace App\Http\Controllers;

//==========ここから追加==========
use App\Article;
//==========ここまで追加==========
use App\Tag;
//===========ここから追加==========
use App\Http\Requests\ArticleRequest;
//===========ここまで追加==========

use Illuminate\Http\Request;

class ArticleController extends Controller
{

     public function __construct()
     {
         // Article::classはApp/Articleの事、第二引数にルーティングで渡ってくるパラメータを渡す。
         // php artisan route:listで調べればわかる
         $this->authorizeResource(Article::class, 'article');
     }


     public function index()
     {
         $articles = Article::all()->sortByDesc('created_at') ->load(['user', 'likes', 'tags']); 

         return view('articles.index', ['articles' => $articles]);
     }

    public function create()
    {
        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.create', [
            'allTagNames' => $allTagNames,
        ]);
    }


    // Laravelのコントローラーはメソッドの引数で型宣言を行うと、そのクラスのインスタンスが自動で生成されてメソッド内で使えるようになります。
    public function store(ArticleRequest $request, Article $article)
    {

        // dd($request);
        $article->fill($request->all()); //-- この行を追加
        $article->user_id = $request->user()->id;
        $article->save();

        // $request->tagsはコレクション形式
        // eachメソッドは、コレクションの各要素に対して順に処理を行うことができます。
        // また、このeachメソッドには、引数にコールバック(関数)を渡すことができます。
        // クロージャの第一引数にはコレクションの値が、第二引数にはコレクションのキーが入ります。
        // use ($article)とあるのは、クロージャの中の処理で変数$articleを使うためです。
        // 無名関数（クロージャについて）https://www.php.net/manual/ja/functions.anonymous.php#functions.anonymous

        // firstOrCreateメソッドは、引数として渡した「カラム名と値のペア」を持つレコードがテーブルに存在するかどうかを探し、もし存在すればそのモデルを返します。
        // テーブルに存在しなければ、そのレコードをテーブルに保存した上で、モデルを返します。
        // $article->tags()->attach($tag); で記事とタグの紐付けが行われる



        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });
        return redirect()->route('articles.index');
    }


     //  editアクションメソッドでは、引数でArticle $articleと型宣言をしています。
     // このことにより、4章で追加したstoreアクションメソッドの時と同様、LaravelではArticleモデルのインスタンスのDI(依存性の注入)が行われます。
    //  editアクションメソッドの場合は、$articleには、このeditアクションメソッドが呼び出された時のURIが例えばarticles/3/editであれば、idが3であるArticleモデルのインスタンスが代入されます。
     public function edit(Article $article)
     {
        $tagNames = $article->tags->map(function ($tag) {
            return ['text' => $tag->name];
        });

        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.edit', [
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
     }


    public function update(ArticleRequest $request, Article $article)
    {
        // モデルのfillメソッドの戻り値はそのモデル自身なので、そのままsaveメソッドを繋げて使うことができます。
        // fillメソッドを使うことでArticleモデルのfillableに設定しているプロパティに更新フォームで入力した値を詰める事ができる。


        $article->fill($request->all())->save();

        // detachメソッドを引数無しで使うと、そのリレーションを紐付ける中間テーブルのレコードが全削除されます。
        $article->tags()->detach();
        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    public function show(Article $article)
    {
      return view('articles.show', ['article' => $article]);
    }

    public function like(Request $request, Article $article)
    {
        // likes()と()を使っているのでbelongsToManyクラスのインスタンスが返ってくる
        // likesの後の()がなければUserモデルのコレクションが返ってくる。
        // attach,detachは多対多のリレーションで使用できるメソッド
        // detachしてからatacchしているのは２重いいね防止対策。
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        // Laravelでは、コントローラーのアクションメソッドで配列や連想配列を返すと、JSON形式に変換されてレスポンスされます。
        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

}
