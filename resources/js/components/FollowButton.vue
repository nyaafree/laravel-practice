<template>
  <div>
    <button
      class="btn-sm shadow-none border border-primary p-2"
      :class="buttonColor"
      @click="clickFollow"
    >
      <i
        class="mr-1"
        :class="buttonIcon"
      ></i>
      {{ buttonText }}
    </button>
  </div>
</template>

<script>
  export default {
     //==========ここから追加==========
    props: {
      initialIsFollowedBy: {
        type: Boolean,
        default: false,
      },
      authorized: {
        type: Boolean,
        default: false,
      },
      endpoint: {
        type: String,
      },
    },
    //==========ここまで追加==========
    data() {
      //  Blade側で、initial-is-followed-byに渡した値は、プロパティinitialIsFollowedByに渡されます。
      // そして、プロパティinitialIsFollowdの値をそのままデータisFollowedByにセットしています。
      // 各算出プロパティ(computed)において、プロパティinitialIsFollowedByを直接使わず、データisFollowedByを定義して使用している理由は、本章の後半で、フォロー・フォロー解除するたびにデータisFollowedをtrueやfalseに切り替える予定であるためです。
      // 7章のパート5で説明したことの繰り返しとなりますが、Vue.jsでは、親コンポーネント(ここではBlade)からpropsへ渡されたプロパティの値を、子のコンポーネント側で変更することは推奨されていません。
      return {
        // データisFollowdByで、このボタンを表示しているユーザーページのユーザーを、ログイン中のユーザーがフォローしているのか、フォローしていないのかを管理します。
        isFollowedBy: this.initialIsFollowedBy,
      }
    },
    computed: {
      buttonColor() {
        return this.isFollowedBy
          ? 'bg-primary text-white'
          : 'bg-white'
      },
      buttonIcon() {
        return this.isFollowedBy
          ? 'fas fa-user-check'
          : 'fas fa-user-plus'
      },
      buttonText() {
        return this.isFollowedBy
          ? 'フォロー中'
          : 'フォロー'
      },
    },
    methods: {
      // clickFollowメソッドでは、ユーザーが未ログインであれば警告のポップアップを出して、早期リターンでメソッドを終了させるようにしています。
      // そして、そのif文を抜けた後は、データisFolloedByを使って現在フォロー中であるかどうかを三項演算子で判定します。
      // フォロー中であればunfollowメソッド、フォローしていなければfollowメソッドを実行します。
      clickFollow() {
        if (!this.authorized) {
          alert('フォロー機能はログイン中のみ使用できます')
          return
        }

        this.isFollowedBy
          ? this.unfollow()
          : this.follow()
      },
      // async await については https://www.codegrid.net/articles/2017-async-await-1
      // axios.put(this.endpoint)では、endopoint、つまりURI users/{name}/followに対して、HTTPのPUTメソッドでリクエストします。
      // axiosはHTTP通信を行うためのJavaScriptのライブラリです。
      // Laravelでは、標準でこのaxiosが使用できるようになっています。
      // HTTP通信を行った後は、isFollowedByにtrueを代入しています。この結果、フォローボタンはフォロー中の表示になります。
      // responseには、axiosによるHTTP通信の結果が代入されていますが、response.dataとすることでレスポンスのボディ部にアクセスできます。
      // レスポンスのボディ部には、Laravel側のfollowアクションメソッドの戻り値が代入されていますので、response.data.nameとすることで、フォローしたユーザー名を取得できます。
      // ただし、本教材のWebサービスでは、フォローしたユーザーの名前を使った後続処理は特に無いので、response.data.nameを使うということは特にしません。
      async follow() {
        const response = await axios.put(this.endpoint)

        this.isFollowedBy = true
      },
      async unfollow() {
        const response = await axios.delete(this.endpoint)

        this.isFollowedBy = false
      },
    },
  }
</script>
