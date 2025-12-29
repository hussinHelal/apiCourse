<?php 

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\Hidden;
use App\Models\Transactions;
use App\Models\Buyer;
use App\Models\Product;
use Spatie\LaravelData\Attributes\Computed;

class TransactionData extends Data
{

    public function __construct(

        #[MapOutputName("i")]
        public int $id,

        #[MapOutputName("q")]
        public int $quantity,

        #[MapOutputName("b")]
        public ?BuyerData $buyer,

        #[MapOutputName("p")]
        public ?ProductData $product,

        #[Hidden]
        public ?int $product_id,
        
        #[Hidden]
        public ?int $seller_id,

        #[Computed]
        #[MapOutputName("li")]
        public ?array $links,
    ){
        $this->links = $this->generateLinks();
    }

    protected function generateLinks(): array
    {
        
        $links = [];
        $links['self'] = [
                'href' => route('transactions.show',$this->id),
                'method' => 'GET',
        ];
        $links['category'] = [
                'href' => route('transactionsCategory.show', $this->id),
                'method' => 'GET',
        ];
        $links['buyer'] = [
                'href' => route('buyerTransactions.show', $this->id),
                'method' => 'GET',
        ];
        if($this->seller_id) {
        $links['seller'] = [
                'href' => route('transactionsSeller.show', ['transactions'=>$this->id, 'seller'=>$this->seller_id]),
                'method' => 'GET',
        ];
    }
            if($this->product_id){
            $link['product'] = [
                'href' => route('product.transaction.show', ['transactions'=>$this->id,'product',$this->product_id]),
                'method' => 'GET',
            ];
            }
        return $links;
    }
    public static function fromModel(Transactions $transaction, Buyer $buyer,Product $product):self
    {
        
        $transaction->loadMissing(['buyer','product']);

        return new self(
            id: $transaction->id,
            quantity: $transaction->quantity,
            buyer: $transaction->buyer ? BuyerData::from($transaction->buyer) : null,
            product: $transaction->product ? ProductData::from($transaction->product) : null,
            product_id: $transaction->product_id,
            seller_id: $transaction->seller_id,
            links: $transaction->links
        );

        
    }
}
