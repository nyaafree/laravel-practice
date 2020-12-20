<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        // Socialite::driver($provider)->stateless()->user()により、Laravel\Socialite\Two\Userというクラスのインスタンスを取得できます。
        // Laravel\Socialite\Two\Userクラスのインスタンスでは、Googleから取得したユーザー情報をプロパティとして持っています。
        // これをいったん変数$providerUserに代入します。

        $providerUser = Socialite::driver($provider)->stateless()->user();

        // ここでは、Googleから取得したユーザー情報からメールアドレスを取り出し、そのメールアドレスが本教材のWebサービスのusersテーブルに存在するかを調べています。
        // $providerUser->getEmail()により、Googleから取得したユーザー情報からメールアドレスを取得できます。
        // 参考URL https://readouble.com/laravel/6.x/ja/socialite.html#retrieving-user-details
        $user = User::where('email', $providerUser->getEmail())->first();

        // ここでは、$userがnullでなければ、つまりGoogleから取得したメールアドレスと同じメールアドレスを持つユーザーモデルが存在すれば、そのユーザーでログイン処理を行なっています。

        if ($user) {
        // 下記のコードで、ユーザーをログイン状態にしています。
        // ここでloginメソッドの第二引数をtrueにしていますが、こうすることでログアウト操作をしない限り、ログイン状態が維持されるようになります。
        // remember meトークンが有効にな流のでログイン状態が維持されます。
            $this->guard()->login($user, true);
            // 下記のコードで、ログイン後の画面(記事一覧画面)へ遷移するようにしています。
            // laravel/vendor/laravel/framework/src/Illuminate/Foundation/Auth/AuthenticatesUsers.phpのloginメソッドを参考にしている。
            return $this->sendLoginResponse($request);
        }


        // $providerUser->tokenでは、Googleから発行されたトークンが返ります。
        // Laravel Socialiteでは、このトークンがあれば、任意のタイミングでGoogleアカウントのユーザー情報を取得できます。


        // リダイレクト先のregister.{provider}という名前のルーティングのURIは、ルーティングの表で確認した通り、
        // register/{provider}です。
        // そこにprovider, email, tokenという3つのパラメータが渡されていることで、具体的なURLの例としては、
        // localhost/register/google?email=foobar@gmail.com&token=xxx...となります。
        // 上記URLへのGETメソッドでのリクエストが行われることで、showProviderUserRegistrationFormアクションメソッドが実行されます。
        // この時のリクエストのパラメータは、Illuminate\Http\Requestクラスのインスタンスである$requestがプロパティとして持っています。
        return redirect()->route('register.{provider}', [
            'provider' => $provider,
            'email' => $providerUser->getEmail(),
            'token' => $providerUser->token,
        ]);
    }
}
