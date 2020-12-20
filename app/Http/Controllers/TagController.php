<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;


class TagController extends Controller
{
    // /tags/{name}の{name}の{name}が引数$nameに入ってくる
    public function show(string $name)
    {
        // Tag::where('name',$name)で取得できるのはコレクションなのでfirstメソッドを使ってモデル１個を取り出して$tagに代入する
        $tag = Tag::where('name', $name)->first();

        return view('tags.show', ['tag' => $tag]);
    }
}
