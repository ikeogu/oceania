CREATE TABLE `nshift` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `in` timestamp NULL DEFAULT NULL,
  `out` timestamp NULL DEFAULT NULL,
  `staff_systemid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cash_in` int(10) unsigned DEFAULT NULL,
  `cash_out` int(10) unsigned DEFAULT NULL,
  `sales_drop` int(10) unsigned DEFAULT NULL,
  `actual` int(10) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=Aria;
--
CREATE TABLE `locprodcost_qtydist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `locprodcost_id` int(10) unsigned NOT NULL,
  `csreceipt_id` int(11) DEFAULT NULL,
  `stockreport_id` int(11) DEFAULT NULL,
  `qty_taken` int(10) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=Aria;
--
CREATE TABLE `openitemcost_qtydist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `openitemcost_id` int(10) unsigned NOT NULL,
  `csreceipt_id` int(10) unsigned DEFAULT NULL,
  `stockreport_id` int(10) unsigned DEFAULT NULL,
  `qty_taken` int(10) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=Aria;
--
CREATE TABLE `returningnote` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `systemid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL,
  `recvnote_systemid` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=Aria;
--
CREATE TABLE `returningnote_list` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `returningnote_systemid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=Aria;
--
alter table auditedreport modify qty int null;
alter table auditedreport modify audited_qty int null;
alter table company add auto_stockin boolean default false after approved_at;
alter table creditact_ledger modify amount float default null;
alter table locationproduct modify value integer null;
alter table locationproduct modify costvalue bigint(20) null;
alter table locprod_productledger add csreceipt_id int unsigned null after stockreport_id;
alter table locprod_productledger modify stockreport_id int unsigned null;
alter table locprod_productledger modify status enum('pending','confirmed','in_progress','cancelled','received','active') default 'active';
alter table locprod_productledger modify type enum('voided','transfer','stockin','stockout','stocktake','cforward','refundcp','daily_variance','received','cash_sales','returned') default null;
alter table mtermsync add transactionid int null after litre;
alter table oneway add approved_at timestamp null after status;
alter table openitem_productledger modify stockreport_id int unsigned null;
alter table openitem_productledger add csreceipt_id int unsigned null after stockreport_id;
alter table openitem_productledger modify status enum('pending','confirmed','in_progress','cancelled','received','active') default 'active';
alter table openitem_productledger modify type enum('voided','transfer','stockin','stockout','stocktake','cforward','refundcp','daily_variance','received','cash_sales','returned') default null;
alter table prd_openitem modify costvalue bigint(20) null;
alter table prd_openitem modify profitloss int unsigned null;
alter table stockreport modify type enum('voided','transfer','stockin','stockout','stocktake','cforward','refundcp','daily_variance','received','returned') default null;
alter table users add remember_token varchar(255)  null after last_login;
