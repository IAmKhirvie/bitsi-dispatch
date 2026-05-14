@extends('layouts.app')

@section('title', 'Dispatch Board - BITSI Dispatch')

@section('content')
    @livewire('dispatch-board', ['date' => $date ?? now()->toDateString()])
@endsection
