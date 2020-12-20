<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    // 引数の$nameには/users/{name}の{name}の部分に入った文字列が渡ってきます。
    public function show(string $name)
    {

        // 下の$articlesで$user->articlesのリレーションを使っているのでここでリレーション先のarticlesのさらにリレーション先の情報を取得する。
        $user = User::where('name', $name)->first()->load(['articles.user', 'articles.likes', 'articles.tags']);


        $articles = $user->articles->sortByDesc('created_at');


        return view('users.show', [
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    // users/{$name}/likesでgetリクエストできた場合の{$name}がlikesメソッドの引数 string $name に入ってくる。
    public function likes(string $name)
    {
        // load()の部分について
        // まず最初のlikesリレーションの部分でこのUserモデルのインスタンスがいいねした記事のリレーションを取得する事ができる。
        // これについてはUserモデルを参照。
        // 次にuser,likes,tagsのリレーションを取得する事で記事を投稿したユーザー、記事にいいねをしたユーザー、記事についているタグ情報、のリレーションを取得する事ができる。
        // これについてはArticleモデルを参照。
        $user = User::where('name', $name)->first() ->load(['likes.user', 'likes.likes', 'likes.tags']);

        $articles = $user->likes->sortByDesc('created_at');

        return view('users.likes', [
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    public function followings(string $name)
    {
        $user = User::where('name', $name)->first()->load(['followings.followers']);

        $followings = $user->followings->sortByDesc('created_at');

        return view('users.followings', [
            'user' => $user,
            'followings' => $followings,
        ]);
    }

    // users/{name}/followersでgetリクエストできた場合の{$name}がfolllwersメソッドの引数 string $name に入ってくる。
    public function followers(string $name)
    {
        // // load()の部分について
        // まず最初のfollowersリレーションの部分でこのUserモデルのインスタンスがフォローされているユーザのリレーションを取得する事ができる。
        // これについてはUserモデルを参照。
        // 次にfollowersのリレーションを取得する事でこのユーザモデルのフォロワーのフォロワーのリレーションを取得する事ができる。
        // これについてはUserモデルを参照。
        $user = User::where('name', $name)->first()->load(['followers.followers']);

        $followers = $user->followers->sortByDesc('created_at');

        return view('users.followers', [
            'user' => $user,
            'followers' => $followers,
        ]);
    }

    //  引数$nameには、URLusers/{name}/followの{name}の部分が渡ってきます。
    // {name}の部分には、フォローされる側のユーザーの名前が入っています。
    public function follow(Request $request, string $name)
    {
        // usersテーブルのnameカラムはユニーク制約を付けてあるので、nameカラムの値が同じであるレコードが複数件存在するということはありません。
        // そのため、取得したコレクションの要素は0件か1件のどちらかであり、2件以上ということがありません。
        // 従って、firstメソッドで最初の1件を取得しています。
        $user = User::where('name', $name)->first();

        // 本教材のWebサービスでは、自分自身をフォローできないようにします。
        // そのため、フォローされる側のユーザーのidと、フォローのリクエストを行なったユーザーのidを比較しています。
        // idが一致した場合は、自分自身をフォローしようとしているということなので、Laravelのabort関数を使ってエラーのHTTPステータスコードをレスポンスしています。
        // abort関数は、第一引数にステータスコードを渡します。
        // ステータスコード404は、ユーザーからのリクエストが誤っている場合などに使われるエラーです。
        // また、第二引数にはクライアントにレスポンスするテキストを渡すことができます(こちらは省略することも可能です)。
        // ここでは、「あなた自身をフォローはできません」という意味のテキストをレスポンスすることにしました。

        // abort() - Laravel公式　https://readouble.com/laravel/6.x/ja/helpers.html#method-abort
        // HTTP レスポンスステータスコード - HTTP | MDN　https://developer.mozilla.org/ja/docs/Web/HTTP/Status
        if ($user->id === $request->user()->id)
        {
            return abort('404', 'Cannot follow yourself.');
        }

        // attachメソッドやdetachメソッドの使い方は、7章のパート7で作成したlikesメソッドと同様です。
        // 必ず削除(detach)してから新規登録(attach)しているのは、1人のユーザーがあるユーザーを複数回重ねてフォローできないようにするための考慮です。

        // attach,detachは多対多のリレーションで使用できるメソッド
        // detachしてからatacchしているのは２重いいね防止対策。

        // attach,detach https://readouble.com/laravel/6.x/ja/eloquent-relationships.html#updating-many-to-many-relationships
        $request->user()->followings()->detach($user);
        $request->user()->followings()->attach($user);


        // followsテーブルを更新した後は、上記の連想配列をクライアントにレスポンスしています。
        // Laravelでは、コントローラーのアクションメソッドで配列や連想配列を返すと、JSON形式に変換されてレスポンスされます。
        // ここでは、どのユーザーへのフォローが成功したかがわかるよう、ユーザーの名前を返しています。
        return ['name' => $name];
    }

    public function unfollow(Request $request, string $name)
    {
        $user = User::where('name', $name)->first();

        if ($user->id === $request->user()->id)
        {
            return abort('404', 'Cannot follow yourself.');
        }

        $request->user()->followings()->detach($user);

        return ['name' => $name];
    }
    //==========ここまで追加==========

}
