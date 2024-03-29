<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\RecipeImage;
use Livewire\WithPagination;

class Pagination extends Component
{

    public $search;
    
    use WithPagination;


    protected $paginationTheme = 'bootstrap';

    protected $queryString = ['search'];
   
    protected $listeners = ['reload' => '$refresh'];

    
    public function render()
    {
        if($this->search)
        {
            $dish = Recipe::where('recipe_name', 'LIKE', "%{$this->search}%")->paginate(12);
        } else 
        {
            $dish = Recipe::paginate(12);
        }

        foreach ($dish->items() as $key => $value) {
    
            $dish[$key]->ingredient_count = Ingredient::where('recipe_id', $value->id)->count(); 
        }   


        return view('livewire.pagination', [
            'dish' => $dish,
            'query' => $this->search,
            'message' => (count($dish) == 0) ? true : false
        ]);

    }

    public function updated($property)
    {

        if($property == 'search')
        {
            $this->resetPage();
        }
    }

}