<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//==========ここから追加==========
use Illuminate\Database\Eloquent\Relations\BelongsTo;
//==========ここまで追加==========
//==========ここから追加==========
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
//==========ここまで追加==========

class Article extends Model
{

     //==========ここから追加==========
     protected $fillable = [
        'title',
        'body',
    ];
    //==========ここまで追加==========

    //==========ここから追加==========

    // 戻り値の方を宣言している(BelongsToクラス)
    // https://www.techpit.jp/courses/11/curriculums/12/sections/107/parts/389
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }
    //==========ここまで追加==========

    //==========ここから追加==========

    // ログイン済みユーザがいいね済みかどうか判定するため、Userモデルにアクセスする必要がある。
    // 戻り値の型としてBelongsToManyクラスのインスタンスを設定している。
    // Userモデルにアクセスする中間テーブルとしてlikesテーブルを設定している。

    // 一つの記事は複数のユーザーにいいねされることもあるし、一人のユーザーは複数の記事にいいねすることもある
    // そのため、likesテーブルを中間テーブルとして多対多の関係が成り立っている。
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }
    //==========ここまで追加==========

     //===========ここから追加===========

    //  引数$userはUserモデルのインスタンスまたはnullである事が許容される
     public function isLikedBy(?User $user): bool
     {
        //  $userがtrue判定ならばこの記事にいいねを押したUserモデルをlikesメソッドで取得してその中にある$userのid(ログインユーザのID)の数をカウントする(1以上であればtrueにキャストされる)
        //  戻り値はコレクション（配列を拡張したものとして返ってくる）
        // コレクションのwhere,countメソッドをチェーンメソッドとして使っている
        //  $userがfalseの場合はfalseを返す。

        // $this->likesは、動的プロパティlikesを使用しています。
        // リレーションメソッドを()無しで呼び出すと、動的プロパティというものを呼び出していることになります。
        // $this->likesというコードで何を実現しているかというと、動的プロパティlikesを使用することで、
        // Articleモデルからlikesテーブル経由で紐付くUserモデルが、コレクション(配列を拡張したもの)で返ります。
         return $user
             ? (bool)$this->likes->where('id', $user->id)->count()
             : false;
     }
     //===========ここまで追加===========

     //===========ここから追加===========
    public function getCountLikesAttribute(): int
    {
        return $this->likes->count();
    }
    //===========ここまで追加===========

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }
}
