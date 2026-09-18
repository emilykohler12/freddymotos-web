<?php

use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExpenseCategoryController as AdminExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Admin\ShippingController as AdminShippingController;
use App\Http\Controllers\Admin\SiteSettingController as AdminSiteSettingController;
use App\Http\Controllers\Admin\StockMovementController as AdminStockMovementController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MercadoPagoWebhookController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/producto/{product:slug}', [ProductController::class, 'show'])->name('products.show');

/* ---------------- Carrito (sesión, sin login) ---------------- */
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/agregar', [CartController::class, 'add'])->name('cart.add');
Route::patch('/carrito/{product:slug}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/{product:slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/carrito', [CartController::class, 'clear'])->name('cart.clear');

/* ---------------- Checkout ---------------- */
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/gracias/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

/* ---------------- Webhook Mercado Pago (sin CSRF, ver bootstrap/app.php) ---------------- */
Route::post('/webhook/mercadopago', MercadoPagoWebhookController::class)->name('webhook.mercadopago');

/* ---------------- Auth de clientes ---------------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [ClientAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ClientAuthController::class, 'login']);
    Route::get('/registro', [ClientAuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [ClientAuthController::class, 'register']);
});
Route::post('/logout', [ClientAuthController::class, 'logout'])->middleware('auth')->name('logout');

/* ---------------- Recuperar contraseña (código de 6 dígitos por email) ---------------- */
Route::middleware('guest')->prefix('recuperar-contrasena')->group(function () {
    Route::get('/', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/', [PasswordResetController::class, 'sendCode'])->name('password.send-code');
    Route::get('/codigo', [PasswordResetController::class, 'showCodeForm'])->name('password.code-form');
    Route::post('/codigo', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
});

/* ---------------- Panel de administración ---------------- */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware('admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        /* Categorías */
        Route::get('categorias', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('categorias', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('categorias/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('categorias/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        /* Productos / Inventario */
        Route::get('productos', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('productos/nuevo', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('productos', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('productos/{product}/editar', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('productos/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('productos/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        /* Promociones */
        Route::get('promociones', [AdminPromotionController::class, 'index'])->name('promotions.index');
        Route::get('promociones/nueva', [AdminPromotionController::class, 'create'])->name('promotions.create');
        Route::post('promociones', [AdminPromotionController::class, 'store'])->name('promotions.store');
        Route::get('promociones/{promotion}/editar', [AdminPromotionController::class, 'edit'])->name('promotions.edit');
        Route::put('promociones/{promotion}', [AdminPromotionController::class, 'update'])->name('promotions.update');
        Route::delete('promociones/{promotion}', [AdminPromotionController::class, 'destroy'])->name('promotions.destroy');

        /* Ventas / Pedidos */
        Route::get('pedidos', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('pedidos/nuevo', [AdminOrderController::class, 'create'])->name('orders.create');
        Route::post('pedidos', [AdminOrderController::class, 'store'])->name('orders.store');
        Route::get('pedidos/{order:uuid}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('pedidos/{order:uuid}', [AdminOrderController::class, 'update'])->name('orders.update');

        /* Clientes */
        Route::get('clientes', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('clientes/nuevo', [AdminCustomerController::class, 'create'])->name('customers.create');
        Route::post('clientes', [AdminCustomerController::class, 'store'])->name('customers.store');
        Route::get('clientes/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::get('clientes/{customer}/editar', [AdminCustomerController::class, 'edit'])->name('customers.edit');
        Route::put('clientes/{customer}', [AdminCustomerController::class, 'update'])->name('customers.update');
        Route::delete('clientes/{customer}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');

        /* Proveedores */
        Route::get('proveedores', [AdminSupplierController::class, 'index'])->name('suppliers.index');
        Route::get('proveedores/nuevo', [AdminSupplierController::class, 'create'])->name('suppliers.create');
        Route::post('proveedores', [AdminSupplierController::class, 'store'])->name('suppliers.store');
        Route::get('proveedores/{supplier}', [AdminSupplierController::class, 'show'])->name('suppliers.show');
        Route::get('proveedores/{supplier}/editar', [AdminSupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('proveedores/{supplier}', [AdminSupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('proveedores/{supplier}', [AdminSupplierController::class, 'destroy'])->name('suppliers.destroy');
        Route::post('proveedores/{supplier}/compras', [AdminSupplierController::class, 'storePurchase'])->name('suppliers.purchases.store');

        /* Envíos: zonas y empresas */
        Route::get('envios', [AdminShippingController::class, 'index'])->name('shipping.index');
        Route::post('envios/empresas', [AdminShippingController::class, 'storeCompany'])->name('shipping.companies.store');
        Route::put('envios/empresas/{company}', [AdminShippingController::class, 'updateCompany'])->name('shipping.companies.update');
        Route::delete('envios/empresas/{company}', [AdminShippingController::class, 'destroyCompany'])->name('shipping.companies.destroy');
        Route::post('envios/zonas', [AdminShippingController::class, 'storeZone'])->name('shipping.zones.store');
        Route::put('envios/zonas/{zone}', [AdminShippingController::class, 'updateZone'])->name('shipping.zones.update');
        Route::delete('envios/zonas/{zone}', [AdminShippingController::class, 'destroyZone'])->name('shipping.zones.destroy');

        /* Gastos / Otros ingresos */
        Route::get('gastos', [AdminExpenseController::class, 'index'])->name('expenses.index');
        Route::post('gastos', [AdminExpenseController::class, 'store'])->name('expenses.store');
        Route::put('gastos/{expense}', [AdminExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('gastos/{expense}', [AdminExpenseController::class, 'destroy'])->name('expenses.destroy');
        Route::post('gastos/{expense}/pagado', [AdminExpenseController::class, 'togglePaid'])->name('expenses.toggle-paid');
        Route::post('gastos/categorias', [AdminExpenseCategoryController::class, 'store'])->name('expense-categories.store');
        Route::delete('gastos/categorias/{expenseCategory}', [AdminExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy');

        /* Movimientos / actividad */
        Route::get('movimientos', [AdminActivityLogController::class, 'index'])->name('activity.index');
        Route::post('movimientos/inventario', [AdminStockMovementController::class, 'store'])->name('stock-movements.store');

        Route::get('configuracion', [AdminSiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('configuracion', [AdminSiteSettingController::class, 'update'])->name('settings.update');
    });
});
