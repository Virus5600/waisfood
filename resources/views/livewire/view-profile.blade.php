<div>
 

    <select class="form-select w-25" aria-label="Default select example" wire:model="profile">
        <option value="posts">Post</option>
        <option value="reviews">Reviews</option>
        
       
    </select>
    {{-- {{dd($contents)}} --}}
    @if ($profile == "posts" || $profile == "")
        
        @include('_partials.profile._profile_posts')
    @else
        
        @include('_partials.profile._profile_reviews')
    @endif
    
    <div id="profile_info" class="border border-primary w-25 mt-5">
        <img src="" alt="merion">
        <div id="name_age"  class="border border-primary">
            
            full name<br>
            age
        </div>
        <div id="community_details" class="border border-primary mt-2">
            
            Posts contribution:<br>
            Community Upvotes Received:<br>
            Recipe Published:<br>
            Reviews made: <br>
        </div>

    </div>
</div>
