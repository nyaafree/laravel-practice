<?php

namespace App;

//==========ここから追加==========
use App\Mail\BareMail;
use Illuminate\Notifications\Notifiable;
//==========ここまで追加==========
//==========ここから追加==========
use Illuminate\Contracts\Auth\MustVerifyEmail;
//==========ここまで追加==========
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     //==========ここから追加==========
     public function sendPasswordResetNotification($token)
     {
         $this->notify(new PasswordResetNotification($token, new BareMail()));
     }

     public function articles(): HasMany
    {
        return $this->hasMany('App\Article');
    }

    // フォローにおけるユーザーモデルとユーザーモデルの関係は多対多となります。
    // そのため、belongsToManyメソッドを使用します。
    // 今回、belongsToManyメソッドの第三引数と第四引数は省略せずに記述しています。
    // 7章で作成した、記事モデルのlikesリレーションは、以下の関係性を持っていました。
    // リレーション元のarticlesテーブルのidは、中間テーブルのarticle_idと紐付く
    // リレーション先のusersテーブルのidは、中間テーブルのuser_idと紐付く
    // 中間テーブルのカラム名について、リレーション元/先のテーブル名の単数形_idという規則性がありました。
    // この場合、第三引数と第四引数は省略可能です。
    // しかし、本パートで作成する、ユーザーモデルのfollowersリレーションは、以下の関係性となります。
    // リレーション元のusersテーブルのidは、中間テーブルのfollowee_idと紐付く
    // リレーション先のusersテーブルのidは、中間テーブルのfollower_idと紐付く
    // 中間テーブルのカラム名と、リレーション元/先のテーブル名に前述の規則性がありません。
    // この場合、第三引数と第四引数を省略はできず、中間テーブルのカラム名を指定する必要があります。

    // 一人のユーザは複数のユーザにフォローされる事が可能だし、複数のユーザをフォローすることも可能なので多対多の関係になる。

    // followersはフォローされているユーザーのモデルにアクセスできる。
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'follows', 'followee_id', 'follower_id')->withTimestamps();
    }

    // followingsメソッドは、既存のfollowersメソッドとは、第三・第四引数が逆になっており、以下の関係性となります。
    // リレーション元のusersテーブルのidは、中間テーブルのfollower_idと紐付く
    // リレーション先のusersテーブルのidは、中間テーブルのfollowee_idと紐付く
    // ユーザーモデルにfollowingsメソッドを作成したことで、UserControllerのfollow/unfollowアクションメソッドは完成となります。

    // followingsはフォローする or しているユーザーのモデルにアクセス可能。
    // follower_idはフォローしているユーザのID、follwee_idはフォローされているユーザのID
    public function followings(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'follows', 'follower_id', 'followee_id')->withTimestamps();
    }

    // 一人のユーザは複数記事にいいねすることもあるし、また一つの記事もまた複数のユーザにいいねされることがあるから多対多の関係になる。
    // 中間テーブルとしてlikesが使われている。
    // この場合はlikesテーブルのuser_idカラムがusersテーブルのidカラムと、article_idカラムがarticlesテーブルのidカラムとひもづく。
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany('App\Article', 'likes')->withTimestamps();
    }


    public function isFollowedBy(?User $user): bool
    {
        return $user
            ? (bool)$this->followers->where('id', $user->id)->count()
            : false;
    }

    // $this->followersにより、このユーザーモデルのフォロワー(のユーザーモデル)が、コレクション(配列を拡張したもの)で返ります。
    // つまり、このユーザーモデルの全フォロワーがコレクションで返ります。
    // コレクションではcountメソッドを使うことができるので、countメソッドを使ってコレクションの要素数を数えます。
    // これにより、このユーザーモデルの全フォロワー数が求まります。
    // getCountFollowingsAttributeについても、同様にこのユーザーモデルが現在フォロー中のユーザー数が求まります。

    // 今回作った2つのメソッドは、その名前がget...Attributeという形式になっており、アクセサです。
    // そのため、実際にこのメソッドを使う時は、
    // $user->count_followers
    // といったように
    // getとAttributeの部分は除く
    // 残った部分をスネークケースにする(全て小文字で、単語と単語の間は_で繋ぐ書き方)
    // メソッドの呼び出し時に通常必要な()は記述しない
    // といった書き方をします。




    public function getCountFollowersAttribute(): int
    {
        return $this->followers->count();
    }

    public function getCountFollowingsAttribute(): int
    {
        return $this->followings->count();
    }
}
