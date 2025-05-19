@extends('layouts.app')

@section('content')
<div>
  <h1>Google Ads Integration</h1>

  @if(session('success'))
  <p style="color: green;">{{ session('success') }}</p>
  @endif

  @if($errors->has('google'))
  <p style="color: red;">{{ $errors->first('google') }}</p>
  @endif

  @if($connected)
  <p>Google Ads connected successfully.</p>
  {{-- Tampilkan tombol untuk disconnect jika perlu --}}
  @else
  <a href="{{ route('ads.google-auth') }}">
    <button>Connect Google Ads</button>
  </a>
  @endif
</div>
@endsection