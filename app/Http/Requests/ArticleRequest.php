<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // ?! で始まる正規表現を括弧 () で括ることにより、指定した文字列を含まないという条件（否定的先読み）でマッチングを行うことができます。
        // また、否定的先読みの前後に別のパターンをつなげて記述することも可能です。
        // * 直前の文字を０回以上繰り返す
        // . 任意の１文字
        // ^ 行頭
        // \s 半角スペース
        // + 直前の文字を１回以上繰り返す
        // $ 行末
        // /u UTF８として解釈する。
        // \/ スラッシュを意味する
        return [
            'title' => 'required|max:50',
            'body' => 'required|max:500',
            'tags' => 'json|regex:/^(?!.*\s).+$/u|regex:/^(?!.*\/).*$/u',

        ];


    }

       //==========ここから追加==========
       public function attributes()
       {
           return [
               'title' => 'タイトル',
               'body' => '本文',
               'tags' => 'タグ',
           ];
       }
       //==========ここまで追加==========


    //    passedValidationメソッドは、フォームリクエストのバリデーションが成功した後に自動的に呼ばれるメソッドです。
    //    バリデーション成功後に何か処理をしたければ、ここに処理を書きます。

    // まず、json_decode($this->tags)で、JSON形式の文字列であるタグ情報をPHPのjson_decode関数を使って連想配列に変換しています。

    // それをさらにLaravelのcollect関数を使ってコレクションに変換しています。
    // コレクションに変換する理由は、この後で使うsliceメソッドやmapメソッドといった、便利なコレクションメソッドを使うためです。

    // sliceメソッドを使うと、コレクションの要素が、第一引数に指定したインデックスから第二引数に指定した数だけになります。
    // slice(0, 5)とすると、もしコレクションの要素が6個以上あったとしても、最初の5個だけが残ります。（入力できるタグは５個までなので）

    // mapメソッドは、コレクションの各要素に対して順に処理を行い、新しいコレクションを作成します。
    // コールバックの引数$requestTagには、mapメソッドを使うコレクションの要素が入ります。


    public function passedValidation()
    {
        $this->tags = collect(json_decode($this->tags))
            ->slice(0, 5)
            ->map(function ($requestTag) {
                return $requestTag->text;
            });
    }
}
