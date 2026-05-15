<?php 

namespace App\Data;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use App\Models\Buyer;
use Spatie\LaravelData\Attributes\Hidden;

class BuyerData extends Data
{

    public function __construct(
        
        
        #[MapOutputName('i')]
        public int $id,

        #[MapOutputName('n')]
        public string $name,
        
        #[MapOutputName('e')]
        public string $email,
 
        #[Hidden] 
        public ?int $seller_id = null,
        
        #[Hidden] 
        public ?int $category_id = null,
        
        #[Computed]
        #[MapOutputName('li')]
        public ?array $links,
    )
    {
        $this->links = $this->generateLinks();
    }

    protected function generateLinks(): array
    {
        $links = [];
        if ($this->seller_id) {
            $links['self'] = [
                'href' => route('buyer.show', ['seller' => $this->seller_id, 'buyer' => $this->id]),
                'method' => 'GET',
            ];
            $links['update'] = [
                'href' => route('buyer.update', ['seller' => $this->seller_id, 'buyer' => $this->id]),
                'method' => 'PUT',
            ];
            $links['destroy'] = [
                'href' => route('buyer.destroy', ['seller' => $this->seller_id, 'buyer' => $this->id]),
                'method' => 'Delete',
            ];
            $links['store'] = [
                'href' => route('buyer.store',['seller'=>$this->seller_id, 'buyer'=> $this->id]),
                'method' => 'POST',
            ];
        }

        if($this->category_id) {
            $links['category'] = [
                'href' => route('buyerCategory.show', ['category'=>$this->category_id, 'buyer'=>$this->id]),
                'method' => 'GET',
            ];
        }
            $links['product'] = [
                'href' => route('buyerProduct.show', $this->id),
                'method' => 'GET',
            ];

            $links['seller'] = [
                'href' => route('buyerSeller.show', $this->id),
                'method' => 'GET',
            ];

            $links['transactions'] = [
                'href' => route('buyerTransactions.show', $this->id),
                'method' => 'GET',
            ];
        
        return $links;
    }
    public static function fromModel(Buyer $buyer):self
    {
        return new self(
            id: $buyer->id,
            name: $buyer->name,
            email: $buyer->email,
            seller_id: $buyer->seller_id,
            category_id: $buyer->category_id,
            links: $buyer->links,
        );
    }


}