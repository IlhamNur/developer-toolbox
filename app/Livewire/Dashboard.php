<?php

namespace App\Livewire;

use App\Support\ToolRegistry;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public string $search = '';

    public string $category = 'All tools';

    public function render()
    {
        $query = strtolower(trim($this->search));

        $tools = collect(ToolRegistry::all())
            ->filter(function (array $tool) use ($query) {
                $matchesCategory = $this->category === 'All tools' || $tool['category'] === $this->category;
                $haystack = strtolower(implode(' ', [$tool['name'], $tool['description'], $tool['category'], ...$tool['tags']]));

                return $matchesCategory && ($query === '' || str_contains($haystack, $query));
            })
            ->values()
            ->all();

        return view('livewire.dashboard', [
            'tools' => $tools,
            'popularTools' => collect(ToolRegistry::all())->where('popular', true)->values()->all(),
            'categories' => ToolRegistry::categories(),
        ]);
    }
}