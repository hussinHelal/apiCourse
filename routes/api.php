<?php

use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CategorySellerController;
use App\Http\Controllers\CategoryTransactionController;
use App\Http\Controllers\CategoryBuyerController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SellerTransactionController;
use App\Http\Controllers\SellerCategoryController;
use App\Http\Controllers\SellerBuyerController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTransaction;
use App\Http\Controllers\ProductBuyerController;
use App\Http\Controllers\ProductBuyerTransactionController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\transactionCategoryController;
use App\Http\Controllers\transactionSellerController;
use App\Http\Controllers\BuyerTransactionController;
use App\Http\Controllers\BuyerProductController;
use App\Http\Controllers\BuyerSellerController;
use App\Http\Controllers\BuyerCategoryController;
use App\Http\Controllers\User;
use App\Http\Middleware\transformInput;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;


Route::middleware(['signature','client','auth:api'])->group(function () {

Route::resource("buyer",BuyerController::class)->only(['index','show']);

Route::resource("seller",SellerController::class)->only(['index','show']);

Route::resource("seller.transactions",SellerTransactionController::class)->only(['index','show']);

Route::resource("seller.category",SellerCategoryController::class);

Route::resource("seller/{seller}/buyer",SellerBuyerController::class);

Route::resource("sellerTransaction",SellerTransactionController::class);

Route::resource("sellerCategory",SellerCategoryController::class);

Route::resource("sellerBuyer",SellerBuyerController::class);

Route::resource("sellerProduct", SellerProductController::class);

Route::get('/seller/{seller}/products', [SellerProductController::class, 'index']);

Route::post('/seller/{seller}/product', [SellerProductController::class, 'store']);

Route::put('/seller/{seller}/products/{product}', [SellerProductController::class, 'update']);

Route::delete('/seller/{seller}/products/{product}', [SellerProductController::class, 'destroy']);

Route::resource("product",ProductController::class)->only(['index','show']);

Route::resource("product.transaction",ProductTransaction::class);

Route::get('/product.transaction',[ProductTransaction::class,'index'])->name('productTransaction');

Route::resource("product.buyer",ProductBuyerController::class);

Route::get("product.buyer",[ProductBuyerController::class,'index'])->name('productBuyer');

Route::resource("product.buyer.transaction",ProductBuyerTransactionController::class);

Route::resource("product.category",ProductCategoryController::class);

Route::get("product.category",[ProductCategoryController::class,'index'])->name('productCategory');

Route::put("product/{product}/{category}",[ProductCategoryController::class,'update']);

Route::delete("product/{product}/product/{category}",[ProductCategoryController::class,'destroy']);

Route::post("product/{product}/transaction",[ProductTransaction::class,'store']);

Route::delete("product/{product}/transaction/{transaction}",[ProductTransaction::class,'destroy']);

Route::resource("categories",CategoriesController::class)->except(['create','edit']);

Route::resource("categoriesProduct",CategoryProductController::class)->only(['index']);

Route::resource("categoriesSeller",CategorySellerController::class)->only(['index']);

Route::resource("categoriesBuyer",CategoryBuyerController::class)->only(['index']);

Route::resource("categoriesTransaction",CategoryTransactionController::class)->only(['index']);

Route::resource("transactions",TransactionsController::class)->only(['index','show']);

Route::resource("transactionsCategory",transactionCategoryController::class)->only(['index','show']);

Route::resource("transactionsSeller",transactionSellerController::class)->only(['index','show']);

Route::resource("buyerTransactions",BuyerTransactionController::class)->only(['index','show']);

Route::resource("buyerProduct",BuyerProductController::class)->only(['index','show']);

Route::resource("buyerSeller",BuyerSellerController::class)->only(['index','show']);

Route::resource("buyerCategory",BuyerCategoryController::class)->only(['index','show']);

Route::get('user/verify/{token}' , [User::class, 'verify'])->name('verify');

Route::get('user/{user}/resend' , [User::class, 'resendVerification'])->name('resend');


});

Route::get('user/verify/{token}' , [User::class, 'verify'])->name('verify');

Route::get('user/{user}/resend' , [User::class, 'resendVerification'])->name('resend');

Route::resource("user",User::class)->middleware('auth:api');

Route::post('oauth/token',[AccessTokenController::class,'issueToken'])->middleware(['client','auth:api']);

// Route::post('/test-direct-category', [App\Http\Controllers\TestCategoryController::class, 'testStore']);
// Route::POST('/categories', [CategoriesController::class, 'store'])->name('store');
// Route::post('/user', [User::class, 'store'])->name('userstore');
