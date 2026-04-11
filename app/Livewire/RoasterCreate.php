<?php

namespace App\Livewire;

use App\Models\Roaster;
use Illuminate\View\View;
use Livewire\Component;

class RoasterCreate extends Component
{
    public string $name        = '';
    public string $city        = '';
    public string $website     = '';
    public string $description = '';
    public bool   $saved       = false;

    protected array $rules = [
        'name'        => 'required|string|max:255',
        'city'        => 'required|string|max:100',
        'website'     => 'nullable|url|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public function save(): void
    {
        $this->validate();

        Roaster::create([
            'name'        => $this->name,
            'city'        => $this->city,
            'website'     => $this->website,
            'description' => $this->description,
        ]);

        $this->saved = true;
    }

    public function render(): View
    {
        return view('livewire.roaster-create');
    }
}
