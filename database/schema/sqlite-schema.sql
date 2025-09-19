CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "roles"(
  "role_id" integer primary key autoincrement not null,
  "name" varchar check("name" in('admin', 'apoteker', 'kurir', 'pelanggan')) not null default 'pelanggan',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "users"(
  "user_id" integer primary key autoincrement not null,
  "name" varchar not null,
  "username" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "email_verified_at" datetime,
  "password" varchar not null,
  "role_id" integer not null,
  "status" varchar check("status" in('active', 'inactive', 'suspended')) not null default 'active',
  "date_of_birth" date,
  "gender" varchar check("gender" in('male', 'female')),
  "avatar" varchar,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "verification_token" varchar,
  "verification_token_expires_at" datetime,
  foreign key("role_id") references "roles"("role_id")
);
CREATE UNIQUE INDEX "users_username_unique" on "users"("username");
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "notifications"(
  "notification_id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "type" varchar check("type" in('order_status', 'payment', 'prescription', 'chat', 'promo', 'system')) not null,
  "title" varchar not null,
  "message" text not null,
  "data" text,
  "read_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("user_id")
);
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "user_logs"(
  "user_log_id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "action" varchar check("action" in('login', 'logout', 'register', 'update_profile', 'order', 'payment')) not null,
  "timestamp" datetime not null,
  foreign key("user_id") references "users"("user_id")
);
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "store_settings"(
  "setting_id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value" text not null,
  "type" varchar not null default 'string',
  "description" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "store_settings_key_unique" on "store_settings"("key");
CREATE TABLE IF NOT EXISTS "categories"(
  "category_id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "image" varchar,
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "categories_slug_unique" on "categories"("slug");
CREATE TABLE IF NOT EXISTS "products"(
  "product_id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text not null,
  "short_description" text,
  "price" numeric not null,
  "discount_price" numeric,
  "stock" varchar check("stock" in('available', 'out_of_stock')) not null default 'available',
  "sku" varchar,
  "category_id" integer not null,
  "requires_prescription" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "unit" varchar check("unit" in('pcs', 'box', 'botol', 'strip', 'tube', 'sachet')) not null default 'pcs',
  "specifications" text,
  "weight" numeric,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_id") references "categories"("category_id")
);
CREATE UNIQUE INDEX "products_slug_unique" on "products"("slug");
CREATE UNIQUE INDEX "products_sku_unique" on "products"("sku");
CREATE TABLE IF NOT EXISTS "product_images"(
  "image_id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "image_path" varchar not null,
  "is_primary" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("product_id")
);
CREATE TABLE IF NOT EXISTS "order_items"(
  "order_item_id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "product_id" integer not null,
  "qty" integer not null,
  "price" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_id") references "orders"("order_id"),
  foreign key("product_id") references "products"("product_id")
);
CREATE TABLE IF NOT EXISTS "deliveries"(
  "delivery_id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "courier_id" integer,
  "delivery_address" text not null,
  "delivery_fee" numeric not null default '0',
  "delivery_type" varchar check("delivery_type" in('regular', 'express', 'standard')) not null default 'regular',
  "estimated_delivery" datetime,
  "delivered_at" datetime,
  "delivery_notes" text,
  "delivery_photo" varchar,
  "status" varchar check("status" in('pending', 'assigned', 'picked_up', 'in_transit', 'delivered', 'failed', 'ready_to_ship')) not null default 'pending',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_id") references "orders"("order_id"),
  foreign key("courier_id") references "users"("user_id")
);
CREATE TABLE IF NOT EXISTS "shipping_statuses"(
  "shipping_status_id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "status" varchar check("status" in('pending', 'picked_up', 'in_transit', 'delivered', 'failed')) not null,
  "notes" text,
  "updated_at" datetime not null,
  foreign key("order_id") references "orders"("order_id")
);
CREATE TABLE IF NOT EXISTS "prescriptions"(
  "prescription_id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "file" varchar not null,
  "status" varchar check("status" in('pending', 'approved', 'rejected', 'completed')) not null default 'pending',
  "note" text,
  "created_at" datetime not null,
  foreign key("user_id") references "users"("user_id")
);
CREATE TABLE IF NOT EXISTS "prescription_items"(
  "prescription_item_id" integer primary key autoincrement not null,
  "prescription_id" integer not null,
  "product_id" integer not null,
  "qty" integer not null,
  foreign key("prescription_id") references "prescriptions"("prescription_id"),
  foreign key("product_id") references "products"("product_id")
);
CREATE TABLE IF NOT EXISTS "messages"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "chat_sessions"(
  "session_id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "admin_id" integer not null,
  "status" varchar check("status" in('active', 'closed')) not null default 'active',
  "started_at" datetime not null,
  "ended_at" datetime,
  foreign key("user_id") references "users"("user_id"),
  foreign key("admin_id") references "users"("user_id")
);
CREATE TABLE IF NOT EXISTS "chat_messages"(
  "message_id" integer primary key autoincrement not null,
  "session_id" integer not null,
  "sender_id" integer not null,
  "message" text not null,
  "type" varchar check("type" in('text', 'image', 'file')) not null default 'text',
  "is_read" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("session_id") references "chat_sessions"("session_id"),
  foreign key("sender_id") references "users"("user_id")
);
CREATE TABLE IF NOT EXISTS "payment_methods"(
  "payment_method_id" integer primary key autoincrement not null,
  "name" varchar not null,
  "type" varchar check("type" in('cash', 'bank_transfer', 'e_wallet', 'qris', 'virtual_account')) not null,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "code" varchar not null,
  "config" text,
  "sort_order" integer not null default '0'
);
CREATE INDEX "payment_methods_is_active_sort_order_index" on "payment_methods"(
  "is_active",
  "sort_order"
);
CREATE UNIQUE INDEX "payment_methods_code_unique" on "payment_methods"("code");
CREATE TABLE IF NOT EXISTS "carts"(
  "cart_id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "created_at" datetime not null default CURRENT_TIMESTAMP,
  "updated_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("user_id") references "users"("user_id") on delete cascade
);
CREATE INDEX "carts_user_id_index" on "carts"("user_id");
CREATE TABLE IF NOT EXISTS "cart_items"(
  "cart_item_id" integer primary key autoincrement not null,
  "cart_id" integer not null,
  "product_id" integer not null,
  "quantity" integer not null default '1',
  "price" numeric not null,
  "created_at" datetime not null default CURRENT_TIMESTAMP,
  "updated_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("cart_id") references "carts"("cart_id") on delete cascade,
  foreign key("product_id") references "products"("product_id") on delete cascade
);
CREATE INDEX "cart_items_cart_id_product_id_index" on "cart_items"(
  "cart_id",
  "product_id"
);
CREATE UNIQUE INDEX "cart_items_cart_id_product_id_unique" on "cart_items"(
  "cart_id",
  "product_id"
);
CREATE TABLE IF NOT EXISTS "user_addresses"(
  "address_id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "label" varchar not null default('rumah'),
  "recipient_name" varchar not null,
  "phone" varchar not null,
  "address" text,
  "district" varchar not null,
  "city" varchar not null,
  "postal_code" varchar not null,
  "notes" text,
  "is_default" tinyint(1) not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "village" varchar not null,
  "sub_district" varchar,
  "regency" varchar,
  "province" varchar,
  "detailed_address" text not null,
  "province_key" varchar,
  "regency_key" varchar,
  "sub_district_key" varchar,
  "village_key" varchar,
  foreign key("user_id") references users("user_id") on delete no action on update no action
);
CREATE TABLE IF NOT EXISTS "order_payments"(
  "payment_id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "payment_method_id" integer not null,
  "amount" numeric not null,
  "status" varchar check("status" in('pending', 'paid', 'failed', 'refunded', 'cancelled')) not null default 'pending',
  "payment_proof" varchar,
  "notes" text,
  "paid_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "cancelled_at" datetime,
  "transaction_id" varchar,
  "payment_type" varchar,
  "snap_token" text,
  foreign key("payment_method_id") references payment_methods("payment_method_id") on delete no action on update no action,
  foreign key("order_id") references orders("order_id") on delete no action on update no action
);
CREATE TABLE IF NOT EXISTS "refunds"(
  "id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "payment_id" integer,
  "refund_key" varchar not null,
  "midtrans_transaction_id" varchar,
  "refund_amount" numeric not null,
  "original_amount" numeric not null,
  "refund_type" varchar check("refund_type" in('full', 'partial')) not null default 'full',
  "status" varchar check("status" in('pending', 'processing', 'completed', 'failed', 'cancelled')) not null default 'pending',
  "reason" varchar,
  "midtrans_response" text,
  "requested_at" datetime not null default CURRENT_TIMESTAMP,
  "processed_at" datetime,
  "requested_by" integer not null,
  "processed_by" integer,
  "admin_notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_id") references "orders"("id") on delete cascade,
  foreign key("payment_id") references "order_payments"("id") on delete set null,
  foreign key("requested_by") references "users"("id") on delete cascade,
  foreign key("processed_by") references "users"("id") on delete set null
);
CREATE INDEX "refunds_order_id_status_index" on "refunds"(
  "order_id",
  "status"
);
CREATE INDEX "refunds_status_requested_at_index" on "refunds"(
  "status",
  "requested_at"
);
CREATE INDEX "refunds_refund_key_index" on "refunds"("refund_key");
CREATE UNIQUE INDEX "refunds_refund_key_unique" on "refunds"("refund_key");
CREATE TABLE IF NOT EXISTS "orders"(
  "order_id" integer primary key autoincrement not null,
  "order_number" varchar not null,
  "user_id" integer not null,
  "subtotal" numeric not null,
  "delivery_fee" numeric not null default('0'),
  "discount_amount" numeric not null default('0'),
  "total_price" numeric not null,
  "status" varchar not null default('created'),
  "shipping_address" text not null,
  "notes" text,
  "confirmed_at" datetime,
  "shipped_at" datetime,
  "delivered_at" datetime,
  "cancelled_at" datetime,
  "cancellation_reason" text,
  "failed_at" datetime,
  "failure_reason" text,
  "confirmation_note" text,
  "processing_at" datetime,
  "ready_to_ship_at" datetime,
  "ready_for_pickup_at" datetime,
  "picked_up_at" datetime,
  "waiting_payment_at" datetime,
  "waiting_confirmation_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "shipping_type" varchar not null default('pickup'),
  "shipping_distance" numeric,
  "is_free_shipping" tinyint(1) not null default('0'),
  "receipt_image" varchar,
  "pickup_image" varchar,
  "completed_at" datetime,
  "refund_status" varchar,
  "cancelled_by" integer,
  "confirmed_by" integer,
  "receipt_photo" varchar,
  "delivery_photo" varchar,
  "receipt_photo_uploaded_by" integer,
  "delivery_photo_uploaded_by" integer,
  "receipt_photo_uploaded_at" datetime,
  "delivery_photo_uploaded_at" datetime,
  foreign key("confirmed_by") references users("user_id") on delete set null on update no action,
  foreign key("user_id") references users("user_id") on delete no action on update no action,
  foreign key("cancelled_by") references users("user_id") on delete set null on update no action,
  foreign key("receipt_photo_uploaded_by") references "users"("id") on delete set null,
  foreign key("delivery_photo_uploaded_by") references "users"("id") on delete set null
);
CREATE UNIQUE INDEX "orders_order_number_unique" on "orders"("order_number");

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_01_15_000001_create_store_settings_table',1);
INSERT INTO migrations VALUES(5,'2025_01_16_000001_add_detailed_address_to_store_settings',1);
INSERT INTO migrations VALUES(6,'2025_01_28_000001_update_store_settings_for_flexible_delivery',1);
INSERT INTO migrations VALUES(7,'2025_01_28_000002_remove_coordinates_from_user_addresses',1);
INSERT INTO migrations VALUES(8,'2025_01_28_000003_create_products_table',1);
INSERT INTO migrations VALUES(9,'2025_01_29_000001_create_orders_table',1);
INSERT INTO migrations VALUES(10,'2025_07_30_054258_add_shipping_fields_to_orders_table',1);
INSERT INTO migrations VALUES(11,'2025_07_30_054603_create_shipping_table',1);
INSERT INTO migrations VALUES(12,'2025_07_30_055041_create_prescriptions_table',1);
INSERT INTO migrations VALUES(13,'2025_07_30_055153_create_messages_table',1);
INSERT INTO migrations VALUES(14,'2025_07_30_055455_create_payments_table',1);
INSERT INTO migrations VALUES(15,'2025_07_30_055456_update_payment_methods_table',1);
INSERT INTO migrations VALUES(16,'2025_07_30_060000_create_cart_table',1);
INSERT INTO migrations VALUES(17,'2025_08_16_164702_add_detailed_address_fields_to_user_addresses_table',1);
INSERT INTO migrations VALUES(18,'2025_08_16_175220_remove_old_address_field_from_user_addresses_table',1);
INSERT INTO migrations VALUES(19,'2025_08_16_175255_migrate_address_data_to_detailed_address',1);
INSERT INTO migrations VALUES(20,'2025_08_20_072949_make_address_column_nullable_in_user_addresses_table',1);
INSERT INTO migrations VALUES(21,'2025_08_20_084243_add_cascading_dropdown_fields_to_user_addresses_table',1);
INSERT INTO migrations VALUES(22,'2025_08_22_100436_add_timestamps_to_order_items_table',1);
INSERT INTO migrations VALUES(23,'2025_08_22_211744_add_cancelled_status_to_order_payments_table',1);
INSERT INTO migrations VALUES(24,'2025_08_22_212039_add_cancelled_at_to_order_payments_table',1);
INSERT INTO migrations VALUES(25,'2025_08_26_054223_add_midtrans_fields_to_order_payments_table',1);
INSERT INTO migrations VALUES(26,'2025_08_30_061020_remove_coordinates_from_tables',1);
INSERT INTO migrations VALUES(27,'2025_09_07_010227_add_email_verification_fields_to_users_table',1);
INSERT INTO migrations VALUES(28,'2025_09_07_014918_add_verification_token_to_users_table',1);
INSERT INTO migrations VALUES(29,'2025_09_19_233602_update_payment_method_cod_name',2);
INSERT INTO migrations VALUES(30,'2025_09_19_235149_add_missing_columns_to_orders_table',3);
INSERT INTO migrations VALUES(31,'2025_09_20_001702_create_refunds_table',4);
INSERT INTO migrations VALUES(32,'2025_09_20_010223_add_refund_status_to_orders_table',5);
INSERT INTO migrations VALUES(33,'2025_01_30_000001_add_cancelled_by_to_orders_table',6);
INSERT INTO migrations VALUES(34,'2025_09_20_045219_add_confirmed_by_to_orders_table',6);
INSERT INTO migrations VALUES(35,'2025_09_20_045409_add_receipt_and_delivery_photos_to_orders_table',6);
