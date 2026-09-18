<?php

namespace App\Livewire\Tools\Solr;

use App\Services\Solr\SolrQueryBuilderService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Workbench extends Component
{
    public string $query = '*:*';

    public string $filters = "status:active\ncategory:network";

    public string $fields = 'id,name,score';

    public string $sort = 'score desc';

    public int $start = 0;

    public int $rows = 10;

    public string $output = '';

    public string $error = '';

    public function build(): void
    {
        $this->error = '';
        $this->output = '';

        try {
            $this->output = (new SolrQueryBuilderService())->build(
                $this->query,
                $this->filters,
                $this->fields,
                $this->sort,
                $this->start,
                $this->rows,
            );
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
        }
    }

    public function clear(): void
    {
        $this->query = '';
        $this->filters = '';
        $this->fields = '*';
        $this->sort = '';
        $this->start = 0;
        $this->rows = 10;
        $this->output = '';
        $this->error = '';
    }

    public function render()
    {
        return view('livewire.tools.solr.workbench', ['title' => 'Solr Query Builder']);
    }
}
