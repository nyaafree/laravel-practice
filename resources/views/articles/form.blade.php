@csrf
<div class="md-form">
  <label>タイトル</label>
  {{-- ？？はNull合体演算子と呼ばれる --}}
  <input type="text" name="title" class="form-control" required value="{{ $article->title ?? old('title') }}"> {{--この行のvalue属性を変更--}}
</div>

{{-- null合体演算子は、式1 ?? 式2という形式で記述し、以下の結果となります。
- 式1がnullでない場合は、式1が結果となる
- 式1がnullである場合は、式2が結果となる --}}
<div class="form-group">
  <article-tags-input
    :initial-tags='@json($tagNames ?? [])'
    :autocomplete-items='@json($allTagNames ?? [])'
  >
  </article-tags-input>
</div>
{{----------ここまで追加----------}}
<div class="form-group">
  <label></label>
  <textarea name="body" required class="form-control" rows="16" placeholder="本文">{{ $article->body ?? old('body') }}</textarea> {{--この行のtextareaタグ内を編集--}}
</div>
