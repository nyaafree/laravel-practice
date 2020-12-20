import './bootstrap'
import Vue from 'vue'
import ArticleLike from './components/ArticleLike'
import ArticleTagsInput from './components/ArticleTagsInput'
//==========ここから追加==========
import FollowButton from './components/FollowButton'
//==========ここまで追加==========

const app = new Vue({
  el: '#app',
  components: {
    ArticleLike,
    ArticleTagsInput,
    //==========ここから追加==========
    FollowButton,
    //==========ここまで追加==========
  }
})
