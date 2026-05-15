<?php 

namespace App\Data; 

use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use App\Models\Product;


class ProductData extends Data
{

    public function __construct(

        #[MapOutputName('i')]
        public int $id,

        #[MapOutputName('t')]
        public string $name,

        #[MapOutputName('d')]
        public string $description,
        
        #[MapOutputName('st')]
        public string $status,
        
        #[MapOutputName('q')]
        public ?string $quantity,
        
        #[MapOutputName('p')]
        public string $image,
        
        #[MapOutputName('s')]
        public ?SellerData $seller,

        #[Hidden]
        public ?int $category_id = null,
       
        #[Hidden]
        public ?int $transaction_id = null,

        #[Computed]
        #[MapOutputName('li')]
        public ?array $links,
    )
    {
        $this->links = $this->generateLinks();
    }

    protected function generateLinks():array
    {
        $links = [];
        $links['self'] = [
                'href' => route('product.show', $this->id),
                'method' => 'GET',
        ];
        $links['seller'] = [
                'href' => route('sellerProduct.show', $this->id),
                'method' => 'GET',
        ];
          if($this->transaction_id) {  
            $links['transactions'] = [
                'href' => route('product.transaction.show', ['product'=>$this->id, 'transaction'=>$this->transaction_id ]),
                'method' => 'GET',
            ];
          }
        
        
        if ($this->category_id) {
            $links['category'] = [
                'href' => route('product.category.show', [
                    'product' => $this->id, 
                    'category' => $this->category_id
                ]),
                'method' => 'GET',
            ];
        }

        return $links;
    }

    public static function fromModel(Product $product): self
    {
          $pro = $product->loadMissing('seller');

        return new self(
            id: $product->id,
            name: $product->name,
            description: $product->description ? $product->description : null,
            status: $product->status ? $product->status : 'unAvailable',
            quantity: $product->quantity ? $product->quantity : null,
            image: $product->image ? asset("public/imgs/{$product->image}") : null ,
            seller: $product->seller ? SellerData::from($product->seller) : null,
            category_id: $product->category_id,
            transaction_id: $product->transaction_id,
            links: $product->links,
        );
    }
}