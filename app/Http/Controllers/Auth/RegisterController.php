<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'alpha_num', 'min:3', 'max:16', 'unique:users'], //-- この行を変更
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }


    // リダイレクト先のregister.{provider}という名前のルーティングのURIは、ルーティングの表で確認した通り、
    // register/{provider}です。
    // そこにprovider, email, tokenという3つのパラメータが渡されていることで、具体的なURLの例としては、
    // localhost/register/google?email=foobar@gmail.com&token=xxx...となります。
    // 上記URLへのGETメソッドでのリクエストが行われることで、showProviderUserRegistrationFormアクションメソッドが実行されます。
    // この時のリクエストのパラメータは、Illuminate\Http\Requestクラスのインスタンスである$requestがプロパティとして持っています。


    public function showProviderUserRegistrationForm(Request $request, string $provider)
    {
        $token = $request->token;

        // Socialite::driver($provider)->userFromToken($token)により、Laravel\Socialite\Two\Userクラスのインスタンスを取得します。
        // userFromTokenメソッドでは、Googleから発行済みのトークンを使って、GoogleのAPIに再度ユーザー情報の問い合わせを行います。
        // その問い合わせにより取得したユーザー情報は、いったん変数$providerUserに代入します。

        $providerUser = Socialite::driver($provider)->userFromToken($token);

        return view('auth.social_register', [
            'provider' => $provider,
            'email' => $providerUser->getEmail(),
            'token' => $token,
        ]);
    }



    // ここでは、Illuminate\Http\Requestクラスのインスタンスである$requestのvalidateメソッドを使って、バリデーションをおこなっています。
    // nameについては、通常のユーザー登録時と同じバリデーションとしています。
    // また、Googleが発行したトークンについても必須(required)、文字列(string)といったバリデーションを行なっています。
    // なお、リクエストパラメーターには他にemailもありますが、これについてはバリデーションは行わないこととしました。
    // 実際にユーザー登録に使用するのは、この後の処理でGoogleのAPIから再取得するメールアドレスであるためです。
    public function registerProviderUser(Request $request, string $provider)
    {
        $request->validate([
            'name' => ['required', 'string', 'alpha_num', 'min:3', 'max:16', 'unique:users'],
            'token' => ['required', 'string'],
        ]);


        // $request->tokenにより、Googleから発行済みのトークンの値が取得できます。
        // このtokenの値は、変数$tokenに代入します。
        // そして、Socialite::driver($provider)->userFromToken($token)により、Laravel\Socialite\Two\Userクラスのインスタンスを取得します。
        // userFromTokenメソッドでは、Googleから発行済みのトークンを使って、GoogleのAPIに再度ユーザー情報の問い合わせを行います。
        // その問い合わせにより取得したユーザー情報は、いったん変数$providerUserに代入します。
        $token = $request->token;

        $providerUser = Socialite::driver($provider)->userFromToken($token);


        // ユーザーモデルのcreateメソッドを使って、ユーザーモデルのインスタンスを作成しています。
        // createメソッドでは、usersテーブルへのレコードの保存も行われます。
        // 保存する際の各カラムの値については以下の通りです。
        // nameは、リクエストパラメーターのname、つまりユーザー名登録画面に入力されたユーザー名としています。
        // emailは、トークンを使ってGoogleのAPIから取得したユーザー情報のメールアドレスとしています。
        // passwordは、10章のパート6の冒頭で説明した通り、パスワード登録不要とするので、一律nullとしています。
        $user = User::create([
            'name' => $request->name,
            'email' => $providerUser->getEmail(),
            'password' => null,
        ]);

        // laravel/vendor/laravel/framework/src/Illuminate/Foundation/Auth/RegistersUsers.phpのregisterメソッドを参考にしている。

        $this->guard()->login($user, true);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
}
