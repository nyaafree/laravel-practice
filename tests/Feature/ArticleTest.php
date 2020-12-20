<?php

namespace Tests\Feature;

use App\Article;
//==========ここから追加==========
use App\User;
//==========ここまで追加==========
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function testIsLikedByNull()
    {

        // ここでは、factory(Article::class)->create()とすることで、ファクトリによって生成されたArticleモデルがデータベースに保存されます。
        $article = factory(Article::class)->create();

        // ここでは、Articleクラスのインスタンスが代入された$articleがisLikedByメソッドを使用しています。
        // 引数としてnullを渡し、その戻り値が変数$resultに代入されます。
        $result = $article->isLikedBy(null);


        // ここでの$thisは、TestCaseクラスを継承したArtcleTestクラスを指します。
        // TestCaseクラスは、assertFalseメソッドを持っています。
        // assertFalseメソッドは、引数がfalseかどうかをテストします。
        $this->assertFalse($result);
    }

     public function testIsLikedByTheUser()
     {
         $article = factory(Article::class)->create();
         $user = factory(User::class)->create();
        //  下記のコードでは、記事に「いいね」をしていることになります。
        // $article->likes()は()がついているのでbelongsToManyクラスのインスタンスが返る。
        //   likesテーブルは、usersテーブルとarticlesテーブルを紐付ける中間テーブルとなっており、「誰が」「どの記事を」いいねしているかを管理します。
        // このlikesテーブルにレコードを新規登録すると、いいねをしている状態を作ったことになります。

        // 例えば、user_idカラムが1、article_idが2のレコードを新規登録すると、「idが1であるユーザーが」「idが2である記事を」いいねしている状態、ということになります。
        // このようにレコードを新規登録するために以下を行います。
        // まず、$article->likes()とすることで、多対多のリレーション(BelongsToManyクラスのインスタンス)が返ります。
        // この多対多のリレーションでは、attachメソッドが使用できます。
        // $article->likes()->attach($user)とすることで、
        // likesテーブルのuser_idには、$userのidの値
        // likesテーブルのarticle_idには、$articleのidの値
        // を持った、likesテーブルのレコードが新規登録されます。

        // これは、つまり、
        // 「ファクトリで生成された$userが」「ファクトリで生成された$articleを」いいねしている
        // 状態となります。


         $article->likes()->attach($user);

        //  assertTrueメソッドは、引数がtrueかどうかをテストします。
        // https://phpunit.readthedocs.io/ja/latest/assertions.html#assertfalse


         $result = $article->isLikedBy($user);

         $this->assertTrue($result);
     }

    //  $ docker-compose exec workspace vendor/bin/phpunit --filter=theuser
    // メソッド名にtheuserを含むテストコードを実行する


     //==========ここから追加==========
    public function testIsLikedByAnother()
    {
        $article = factory(Article::class)->create();
        $user = factory(User::class)->create();
        $another = factory(User::class)->create();
        // ここでは、変数$anotherに代入されたUserモデルのインスタンスが、$articleをいいねしている状態を作り出しています。
        $article->likes()->attach($another);

        // ここでは、Articleクラスのインスタンスが代入された$articleで、isLikedByメソッドを使用しています。
        // 引数として$userを渡し、その戻り値が変数$resultに代入されます。
        // $anotherは、この$articleをいいねしているユーザーですが、$userは、この$articleをいいねしていないユーザーです。
        // そのため、$resultにはfalseが代入されるはずです。

        $result = $article->isLikedBy($user);

        $this->assertFalse($result);
    }
    //==========ここまで追加==========
}

// docker-compose exec workspace vendor/bin/phpunit --filter=null

// テストメソッドに null が含まれるメソッドのテストのみを実行する
