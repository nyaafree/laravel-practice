<?php

namespace Tests\Feature;

//==========ここから追加==========
use App\User;
//==========ここまで追加==========
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    //==========ここから追加==========

    // TestCaseクラスを継承したクラスでRefreshDatabaseトレイトを使用すると、データベースをリセットします。
    // データベースの全テーブルを削除(DROP)した上で、マイグレーションを実施し全テーブルを作成します。
    // なお、RefreshDatabaseトレイトを使用すると、上記に加えて、テスト中にデータベースに実行したトランザクション(レコードの新規作成・更新・削除など)は、テスト終了後に無かったことになります。
    // トレイトについて https://www.php.net/manual/ja/language.oop5.traits.php
    use RefreshDatabase;




    // Laravelでは、テストにPHPUnitというテストフレームワークを使用します。
    // PHPUnitでは、テストのメソッド名の先頭にtestを付ける必要があります。

    // なお、メソッド名をtest始まりにしたくない場合は、以下の例のようにメソッドのドキュメントに@testと記述します。
    // @test - PHPUnit公式 https://phpunit.readthedocs.io/ja/latest/annotations.html#test

    public function testIndex()
    {

        // ここでの$thisは、TestCaseクラスを継承したArticleControllerTestクラスを指します。
        // TestCaseクラスおよびこれを継承したクラスでは、getメソッドが使用できます。
        // このgetメソッドは、引数に指定されたURLへGETリクエストを行い、そのレスポンス(Illuminate\Foundation\Testing\TestResponseクラス)を返します
        $response = $this->get(route('articles.index'));


        // getメソッドによって変数$responseには、Illuminate\Foundation\Testing\TestResponseクラスのインスタンスが代入されています。
        // TestResponseクラスは、assertStatusメソッドが使えます。
        // assertStatusメソッドの引数には、HTTPレスポンスのステータスコードを渡します。
        // ここでは、正常レスポンスを示す200を渡しています。

        // これにより、$responseのステータスコードが
        // 200であればテストに合格
        // 200以外であればテストに不合格
        // となります。
        // assertStatus(200)はassertOK()でも良い

        // assertStatusは、TestResponseクラスのインスタンス自身を返します。
        // ですので、上記のコードのように->を連結させて、そのままTestResponseクラスのメソッドを使用できます。
        //  assertViewIsの引数には、ビューファイル名を渡します。
        // 'articles.index'を渡すことで、$responseで使用されているビューが、 views/ariticles/index.blade.phpであるかをテストします。
        $response->assertStatus(200)
            ->assertViewIs('articles.index');
    }
    //==========ここまで追加==========



    //==========ここから追加==========

    // Laravelでは、未ログイン状態のユーザーのことをゲスト(guest)と呼びます。
    // そこで、テストメソッド名はtestGuestCreateとしました。


    public function testGuestCreate()
    {
        // route('articles.create')により、記事投稿画面のURLが返ります。
        // 特にログインするための処理を行なっていませんので、変数$responseには未ログイン状態で記事投稿画面にアクセスした時のレスポンスが代入されます。


        $response = $this->get(route('articles.create'));

        // assertRedirect
        // assertRedirectメソッドでは、引数として渡したURLにリダイレクトされたかどうかをテストします。
        // route('login')は、ログイン画面のURLを返します。
        // (サンプルアプリケーションのログイン画面のルーティングにはloginという名前を付けています)

        $response->assertRedirect(route('login'));
    }
    //==========ここまで追加==========

     //==========ここから追加==========
     public function testAuthCreate()
     {

        // factory関数を使用することで、テストに必要なモデルのインスタンスを、ファクトリというものを利用して生成できます。
        // factory(User::class)->create()とすることで、ファクトリによって生成されたUserモデルがデータベースに保存されます。
        // また、createメソッドは保存したモデルのインスタンスを返すので、これが変数$userに代入されます。

        // factory関数を使用するには、あらかじめそのモデルのファクトリが存在する必要があります。
        // Userモデルのファクトリは以下になります。

        // laravel-ci
        // └── database
        //     └── factories
        //         └── Userfactory.php

        // テストに必要なUserモデルを「準備」
         $user = factory(User::class)->create();

        //  actingAsメソッドは、引数として渡したUserモデルにてログインした状態を作り出します。
        // その上で、get(route('articles.create'))を行うことで、ログイン済みの状態で記事投稿画面へアクセスしたことになり、そのレスポンスは変数$responseに代入されます。

        // ログインして記事投稿画面にアクセスすることを「実行」
         $response = $this->actingAs($user)
             ->get(route('articles.create'));

        //  変数$responseには、ログイン済みの状態で記事投稿画面へアクセスしたレスポンスが代入されています。
        //  今度はログイン画面などへリダイレクトはされず、HTTPのステータスコードとしては200が返ってくるはずですので、assertStatus(200)でこれをテストします。
        //  (なお、リダイレクトの場合は、302が返ってきます)
        //  また、assertViewIs('articles.create')で、記事投稿画面のビューが使用されているかをテストします。

        // レスポンスを「検証」
         $response->assertStatus(200)
             ->assertViewIs('articles.create');


        //  テストの書き方のパターンとして、AAA(Arrange-Act-Assert)というものがあります。
        //  日本語で言うと、準備・実行・検証となります。
        //  本パートで書いたテストのtestAuthCreateもAAAに沿っています。


     }
     //==========ここまで追加==========
}
