<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Article;
//==========ここから追加==========
use App\User;
//==========ここまで追加==========
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    return [
        //==========ここから追加==========
        'title' => $faker->text(50),
        'body' => $faker->text(500),
        // 下の解説を参照
        'user_id' => function() {
            return factory(User::class);
        }
        //==========ここまで追加==========
    ];
});

// ファクトリでは、上記のように連想配列でモデルの各プロパティの値を定義します。
// カラム名	属性	役割
// id	整数	記事を識別するID
// title	最大255文字の文字列	記事のタイトル
// body	制限無しの文字列	記事の本文
// user_id	整数	記事を投稿したユーザーのID
// created_at	日付と時刻	作成日時
// updated_at	日付と時刻	更新日時

// Fakerとは文章だけでなく、人名や住所、メールアドレスなどをランダムに生成してくれる、テストデータを作る時に便利な
// PHPのライブラリです。

// articlesテーブルのuser_idカラムは、その記事を投稿したユーザーのIDを持つことを想定したカラムです。
// そのため、サンプルアプリケーションではarticlesテーブルのuser_idカラムに、Userモデルの元となるusersテーブルのidカラムに対する外部キー制約を持たせています。
// articlesテーブルを作成するためのマイグレーションファイルを確認すると、以下の通りとなっています。

// $table->foreign('user_id')->references('id')->on('users');`

// articlesテーブルのuser_idカラムは、usersテーブルのidカラムを参照すること
// という制約になります。
// ですので、user_idカラムは、usersテーブルに存在しないidを持つことができません。
// つまり、「記事は存在するけれど、それを投稿したユーザーが存在しない」という状態を作れないようにしてあります。
// ファクトリでこのようなカラムを取り扱う時は、値として以下のように「参照先のモデルを生成するfactory関数」を返すクロージャ(無名関数)をセットするようにします。

// 'user_id' => function() {
//     return factory(User::class);
// }





