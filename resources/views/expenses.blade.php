@extends('layouts.app')

@section('script')
<script src="{{ asset('js/expenses.js') }}" defer></script>
@endsection

@section('content')
<expenses></expenses>
@endsection
