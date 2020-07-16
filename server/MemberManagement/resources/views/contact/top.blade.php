@extends('layout/user')
@section('content')
<div>
  <h1>お問い合わせ</h1>
  <form action="/user/contact/confirm" method="post">
    {{ csrf_field() }}
    <h4>お問い合わせ内容</h4>
    <textarea name="content" id="" cols="50" rows="10"></textarea>
    <br>
    <button type="submit" class="btn btn-primary">送信</button>
    <hr>
  </form>
  <a href="/user/my" class="btn btn-secondary">マイページに戻る</a>
</div>
@stop