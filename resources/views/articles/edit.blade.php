@extends('app')

@section('title', '記事更新')

@include('nav')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="card mt-3">
          <div class="card-body pt-0">
            @include('error_card_list')
            <div class="card-text">
              {{-- route関数の第二引数には、連想配列形式でルーティングのパラメーターを渡すことができます。 --}}
              {{-- $articleは、idのような数値ではなくArticleクラスのインスタンスですが、それでもLaravelは問題なくURLを生成してくれます。 --}}
              {{-- updateのルーティングに対しては、クライアントからHTTPのPUTメソッドかPATCHメソッドでリクエストする必要があります。 --}}
            {{-- しかし、HTMLのformタグは、PUTメソッドやPATCHメソッドをサポートしていません(DELETEメソッドもサポートしていません)。
            その為、LaravelのBladeでPATCHメソッド等を使う場合は、formタグではmethod属性を"POST"のままとしつつ、@methodでPATCHメソッド等を指定するようにします。 --}}
            {{-- @method('PATCH') <input type="hidden" name="_method" value="PATCH"> --}}
              <form method="POST" action="{{ route('articles.update', ['article' => $article]) }}">
                @method('PATCH')
                @include('articles.form')
                <button type="submit" class="btn blue-gradient btn-block">更新する</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
