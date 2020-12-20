<div class="card mt-3">
  <div class="card-body d-flex flex-row">
        <a href="{{ route('users.show', ['name' => $article->user->name]) }}" class="text-dark">
            <i class="fas fa-user-circle fa-3x mr-1"></i>
        </a>
        <div>
            <div class="font-weight-bold">
                <a href="{{ route('users.show', ['name' => $article->user->name]) }}" class="text-dark">
                    {{ $article->user->name }}
                </a>
            </div>
        <div class="font-weight-lighter">{{ $article->created_at->format('Y/m/d H:i') }}</div>
  </div>
  @if( Auth::id() === $article->user_id )
    <!-- dropdown -->
      <div class="ml-auto card-text">
        <div class="dropdown">
          <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-ellipsis-v"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route("articles.edit", ['article' => $article]) }}">
              <i class="fas fa-pen mr-1"></i>記事を更新する
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" data-toggle="modal" data-target="#modal-delete-{{ $article->id }}">
              <i class="fas fa-trash-alt mr-1"></i>記事を削除する
            </a>
          </div>
        </div>
      </div>
      <!-- dropdown -->
      <!-- modal -->
      <div id="modal-delete-{{ $article->id }}" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form method="POST" action="{{ route('articles.destroy', ['article' => $article]) }}">
              @csrf
              @method('DELETE')
              <div class="modal-body">
                {{ $article->title }}を削除します。よろしいですか？
              </div>
              <div class="modal-footer justify-content-between">
                <a class="btn btn-outline-grey" data-dismiss="modal">キャンセル</a>
                <button type="submit" class="btn btn-danger">削除する</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- modal -->
    @endif
  </div>
  <div class="card-body pt-0">
    <h3 class="h4 card-title">
      <a class="text-dark" href="{{ route('articles.show', ['article' => $article]) }}">
        {{ $article->title }}
      </a>
    </h3>
    <div class="card-text">
      {!! nl2br(e( $article->body )) !!}
    </div>
  </div>
  <div class="card-body pt-0 pb-2 pl-3">
    <div class="card-text">
       {{-- @jsonでJSON形式に変換 --}}
       {{-- https://readouble.com/laravel/6.x/ja/blade.html --}}
       <article-like
       :initial-is-liked-by='@json($article->isLikedBy(Auth::user()))'
       :initial-count-likes='@json($article->count_likes)'
        {{-- Auth::check()メソッドでログイン済みならture,違うならfalseが返ってくる --}}
        :authorized='@json(Auth::check())'
        {{-- ルーティングは文字列として渡している（動的に変化するものではない）のでv-bindしなくても良い。 --}}
        endpoint="{{ route('articles.like', ['article' => $article]) }}"
       >
      </article-like>
    </div>
  </div>
  {{--------ここから追加--------}}

  {{-- $loopは、@foreachの中で使える変数です。 --}}


  @foreach($article->tags as $tag)
    @if($loop->first)
      <div class="card-body pt-0 pb-4 pl-3">
        <div class="card-text line-height">
    @endif
    <a href="{{ route('tags.show', ['name' => $tag->name]) }}" class="border p-1 mr-1 mt-1 text-muted">
        {{ $tag->hashtag }} {{----------この行を変更----------}}
    </a>
    @if($loop->last)
        </div>
      </div>
    @endif
  @endforeach
</div>