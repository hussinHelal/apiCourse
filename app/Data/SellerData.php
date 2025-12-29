<?php 

namespace App\Data;


use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\Hidden;
use App\Models\Seller;


class SellerData extends Data
{
    public function __construct(
        
        #[MapOutputName("i")]
        public int $id,

        #[MapOutputName("n")]
        public string $name,

        #[MapOutputName("e")]
        public string $email,

        #[MapOutputName("v")]
        public string $verified,

        #[Computed]
        #[MapOutputName("li")]
        public ?array $links,
    ){
        $this->links = $this->generateLinks();
    }

    protected function generateLinks(): array
    {
        $sel = Seller::find($this->id);
        $links = [];

        $links['self'] = [
                'href' => route('seller.show',$this->id),
                'method' => 'GET',
        ];
            //  'store' => [
            //     'href' => route('seller.store', $this->id),
            //     'method' => 'POST',
            // ],
            // 'update' => [
            //     'href' => route('seller.update', $this->id),
            //     'method' => 'PUT',
            // ],
            // 'destroy' => [
            //     'href' => route('seller.destroy', $this->id),
            //     'method' => 'Delete',
            // ],
        $links['category'] = [
                'href' => route('sellerCategory.show', $this->id),
                'method' => 'GET',
        ];

        $links['buyer'] = [
                'href' => route('sellerBuyer.show', $this->id),
                'method' => 'GET',
        ];
          $links['product'] = [
                'href' => route('sellerProduct.show', $this->id),
                'method' => 'GET',
          ];
            $links['transaction'] = [
                'href' => route('sellerTransaction.show', $this->id),
                'method' => 'GET',
            ];
        
        return $links;
    }
    public static function fromModel(Seller $seller):self
    {
        return new self(
            id: $seller->id,
            name: $seller->name,
            email: $seller->email,
            verified: $seller->verified ? 'verified' : 'no verified' ,
            links: $seller->links,
        );
    }

}