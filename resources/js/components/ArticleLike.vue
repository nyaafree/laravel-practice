<template>
  <div>
    <button
      type="button"
      class="btn m-0 p-1 shadow-none"
    >
      <i class="fas fa-heart mr-1"
         :class="{'red-text':this.isLikedBy, 'animated heartBeat fast':this.gotToLike}"
         @click="clickLike"
      />
    </button>
    {{ countLikes }}
  </div>
</template>

<script>
  export default {
    props: {
      initialIsLikedBy: {
        type: Boolean,
        default: false,
      },
      initialCountLikes: {
        type: Number,
        default: 0,
      },

      authorized: {
        type: Boolean,
        default: false,
      },
      endpoint: {
        type: String,
      },

    },
    data() {
      return {
        isLikedBy: this.initialIsLikedBy,
        countLikes: this.initialCountLikes,
         gotToLike: false,
      }
    },
    methods: {
      clickLike() {
        if (!this.authorized) {
          alert('いいね機能はログイン中のみ使用できます')
          return
        }

        this.isLikedBy
          ? this.unlike()
          : this.like()
      },
      async like() {
        // awaitはasync関数内でawaitの後に定義した処理が終わるまで他の処理を進めないことを定義する。
        // 今回のaxios.putメソッドでは何もデータは送信していない。
        const response = await axios.put(this.endpoint)
        console.log(response);

        this.isLikedBy = true
        // responseにはArticleControllerのlikesメソッドの返り血が返ってくる。
        this.countLikes = response.data.countLikes
        this.gotToLike = true
      },
      async unlike() {
        const response = await axios.delete(this.endpoint)
        console.log(response)

        this.isLikedBy = false
        this.countLikes = response.data.countLikes
        this.gotToLike = false
      },
    },
  }
</script>

