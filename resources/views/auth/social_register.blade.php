@extends('app')

@section('title', 'ユーザー登録')

@section('content')
  <div class="container">
    <div class="row">
      <div class="mx-auto col col-12 col-sm-11 col-md-9 col-lg-7 col-xl-6">
        <h1 class="text-center"><a class="text-dark" href="/">memo</a></h1>
        <div class="card mt-3">
          <div class="card-body text-center">
            <h2 class="h3 card-title text-center mt-2">ユーザー登録</h2>

            @include('error_card_list')
            <div class="card-text">
              <form method="POST"
              action="{{ route('register.{provider}', ['provider' => $provider]) }}"> {{---------この行を変更----------}}
              >
                @csrf
                {{-- 1つ目のinputタグでは、value属性にコントローラーから渡されたトークンを設定するとともに、type属性をhiddenとすることで隠し項目としました。 --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="md-form">
                  <label for="name">ユーザー名</label>
                  <input class="form-control" type="text" id="name" name="name" required>
                  <small>英数字3〜16文字(登録後の変更はできません)</small>
                </div>
                {{-- 最後のinputタグでは、value属性にコントローラーから渡されたメールアドレスを設定するとともに、disalbled属性を付けることで入力・変更不可としました。
                登録するメールアドレスは、Googleアカウントから取得したメールアドレスであるともう決まっています。
                そのため、メールアドレスはユーザーへの確認の意味で表示のみ行うこととしています。 --}}
                <div class="md-form">
                  <label for="email">メールアドレス</label>
                  <input class="form-control" type="text" id="email" name="email" value="{{ $email }}" disabled>
                </div>
                <button class="btn btn-block blue-gradient mt-2 mb-2" type="submit">ユーザー登録</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
