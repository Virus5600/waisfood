<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    {{-- 
        jquery
        bootstrap
        bootstrap 
    --}}
    @livewireStyles
    @yield('less_import')
    @yield('javascript')
    <title>@yield('page title')</title>
    <style>
        * {
            box-sizing: border-box;
        }
    </style>
</head>
    @yield('body')
    @livewireScripts
</html>



    {{-- PANG POPUP NG MODAL TO --}}
@if($errors->any())
    <script>
        $(document).ready(function(){
            $('#create_post_modal').modal('show');
        });
    </script>
@endif

{{-- SA AJAX TONG SCRIPT NA TO {{ for checking upvote // downvote}}--}}
<script src="{{ asset('js/ajax_home.js') }}" type="text/javascript"></script>

@if (isset($liked_posts[0]) && $liked_posts[0]->like == 1)
    {{-- <button id="upvote" class="d-block mt-5 fs-3 ms-5 border border-dark bg bg-primary" ><i class="fa-solid fa-arrow-up"></i></button><br>
    <button id="downvote" class="d-block mt-5 fs-3 ms-5 border border-dark" ><i class="fa-solid fa-arrow-down"></i></button><br> --}}
    <script type="text/javascript">
        $(document).ready(function(){
            $('#upvote').addClass("bg bg-primary");
            $('#downvote').removeClass("bg bg-danger")
        });
    </script>
@elseif(isset($liked_posts[0]) && $liked_posts[0]->like == -1)
    <script type="text/javascript">
        $(document).ready(function(){
                $('#upvote').removeClass("bg bg-primary");
                $('#downvote').addClass("bg bg-danger")
            });
    </script>
@endif
