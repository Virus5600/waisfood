@extends('layout.app')

@section('page title')
    WaisFood Community
@endsection

@section('less_import')
    <link rel="stylesheet/less" type="text/css" href="{{ asset('css/home.less') }}" />
    <script src="https://cdn.jsdelivr.net/npm/less" ></script>
    <script src="https://kit.fontawesome.com/4f2d93f234.js" crossorigin="anonymous"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap');</style>
@endsection

@section('body')

<body class="w-100">
    @php
        (!empty(Session::get('user_data'))) ? $user_data = Session::get('user_data') : ""
    @endphp 
    {{-- {{$user_data}} --}}
    {{-- THIS IS THE NAVIGATION BAR --}}
    <main id="user_content_container" class="border border-primary">

        <nav id="navigation_index" class="w-100 position-relative">
            @include('_partials._navigation_index')
        </nav>
        
        <div id="newsfeed_container" class="p-3 pt-1 border border-primary">
            {{-- DITO KAYO START CODE sa index or parang newsfeed nung community - --}}
            @include('_partials._posts')
        </div>

        <div id="to-do-list" class="p-3 pt-1 border border-primary rounded w-25 mt-5">
            
            {{-- eto ung sa view profile na page pag clinick redirect sa view profile page/design --}}
            <a href="{{route('profile.index' ,1)}}" class="d-block">View My Profile</a>

            {{-- eto ung sa view profile na page pag clinick redirect sa view profile page/design --}}
            <a href="{{route('saved.index')}}" class="d-block">View Saved</a>

            {{-- Eto naman kapag magcecreate ng post//recipe --}}
            <a href="{{route('post.create')}}" class="d-block">Create Post</a>

            {{-- eto naman sa mga post, may read more button kasi bawat post then kapag clinick redirect papunta sa complete details nung post/recipe 
                actually similar lang sila nung nacode na na view recipe --}}
            <a href="{{route('post.view', 1)}}" class="d-block">Read More</a>
        </div>
    </main>
</body>

