# Version History

## stable-v1.0
- **Date:** 2026-06-07
- **Changes:** Initial stable snapshot before architecture refactor. Contains complete feature set as-is.
- **Rollback:** `git checkout stable-v1.0`

## stable-v1.1 — Architecture Refactor
- **Date:** 2026-06-07
- **Changes:**
  - Created 8 missing migration files: `customers`, `orders`, `order_items`, `brands`, `colors`, `shapes`, `genders`, `settings`
  - Added stock management columns (`cost_price`, `stock`, `min_stock_threshold`) to products
  - Added foreign keys (`brand_id`, `color_id`, `shape_id`, `gender_id`) to products with auto-seeding from string values
  - Fixed existing migration to safely handle pre-existing `orders.items` column
  - Refactored order system: JSON `items` storage deprecated in favor of normalized `order_items` table
  - Created Service layer: `OrderService`, `StockService`, `NotificationService`, `SettingsService`
  - Refactored controllers to use service layer (thin controllers): `CheckoutController`, `Admin\OrderController`, `Admin\SettingController`, `Admin\NotificationController`, `LiveController`
  - Implemented atomic stock deduction with oversell prevention
  - Stock restoration on order cancellation via `OrderService::cancelOrder()`
  - Updated Product model with FK relationships (`brandModel`, `colorModel`, `shapeModel`, `genderModel`)
  - Added stock helper methods (`inStock()`, `hasLowStock()`, `isOutOfStock()`)
  - Updated Blades views to use `$order->orderItems` instead of `$order->items` JSON
  - Fixed export `price * 10` bug in `GlassesController`
  - Dashboard now queries from brands/colors/shapes/genders tables with fallback
  - Admin auth middleware created and registered
  - Updated env to use `sqlite` by default for fresh setups
  - Created `ProductFactory`, `OrderFactory`, `CustomerFactory`
  - Added 6 feature tests (order creation, stock deduction, oversell prevention, stock restoration, status flow, admin auth)
- **Rollback:** `git checkout stable-v1.0`

