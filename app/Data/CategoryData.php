<?php 

namespace App\Data; 

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use App\Models\Categories;

class CategoryData extends Data
{

    public function __construct(

        #[MapOutputName('i')]
        public int $id,

        #[MapOutputName('t')]
        public string $name,
 
        #[MapOutputName('d')]
        public string $description,

        #[Computed]
        #[MapOutputName('li')]
        public ?array $links,
    )
    {
        $this->links = $this->generateLinks();
    }

    protected function generateLinks():array
    {
        // $cat = Categories::find($this->id);
        $links = [
            'self'=> [
                'href' => route('categories.show', $this->id),
                'method' => 'GET',
            ],
            'update' => [
                'href' => route('categories.update', $this->id),
                'method' => 'PUT',
            ],
            'store' => [
                'href' => route('categories.store', $this->id),
                'method' => 'POST',
            ],
            'destroy' => [
                'href' => route('categories.update', $this->id),
                'method' => 'Delete',
            ],
            'buyer' => [
                'href' => route('categoriesBuyer.index', $this->id),
                'method' => 'GET',
            ],
            'product' => [
                'href' => route('categoriesProduct.index', $this->id),
                'method' => 'GET',
            ],
            'seller' => [
                'href' => route('categoriesSeller.index', $this->id),
                'method' => 'GET',
            ],
            'transactions' => [
                'href' => route('categoriesTransaction.index', $this->id),
                'method' => 'GET',
            ],
        ];
        return $links;
    }
    public static function fromModel(Categories $category):self
    {
        return new self(
            id: $category->id,
            name: $category->name,
            description: $category->description ? $category->description : null,
            links: $category->links,
        );
    }
}