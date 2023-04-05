-- Truncate target tables first
--truncate serveraddr;
--truncate terminal;
truncate location;
truncate company;
truncate users;
truncate local_controller;
truncate local_pump;
truncate local_pumpnozzle;
truncate og_localfuelprice;
truncate prd_ogfuel;
truncate product;
truncate prd_openitem;
truncate prd_inventory;
truncate locationproduct;
truncate productbarcode;
truncate merchantproduct;
truncate stockreport;
truncate stockreportproduct;
truncate pshift;
truncate pshiftdetails;
truncate oneway;
truncate onewaylocation;
truncate onewayrelation;
truncate creditact;
truncate creditact_ledger;
truncate recvnote;
-- truncate recvnote_list;
truncate fuel_itemdetails;
truncate fuel_receipt;
truncate fuel_receiptdetails;
truncate fuel_receiptlist;
truncate fuel_receiptproduct;
truncate cstore_itemdetails;
truncate cstore_receipt;
truncate cstore_receiptdetails;
truncate cstore_receiptproduct;
truncate cstore_receiptrefund;

-- Copy from original DB to target
--insert into serveraddr select * from semporna_27jun22.terminal;
--insert into terminal select * from semporna_27jun22.terminal;
insert into location select * from semporna_27jun22.location;

insert into company select * from semporna_27jun22.company; -- add auto_stockin
insert into users select * from semporna_27jun22.users;	-- add remember_token

insert into local_controller select * from semporna_27jun22.local_controller;
insert into local_pump select * from semporna_27jun22.local_pump;
insert into local_pumpnozzle select * from semporna_27jun22.local_pumpnozzle;
insert into og_localfuelprice select * from semporna_27jun22.og_localfuelprice;
insert into prd_ogfuel select * from semporna_27jun22.prd_ogfuel;
insert into product select * from semporna_27jun22.product;
insert into prd_openitem select * from semporna_27jun22.prd_openitem;
insert into prd_inventory select * from semporna_27jun22.prd_inventory;
insert into locationproduct select * from semporna_27jun22.locationproduct;
insert into productbarcode select * from semporna_27jun22.productbarcode; -- add selected
insert into merchantproduct select * from semporna_27jun22.merchantproduct;

insert into stockreport select * from semporna_27jun22.stockreport;
insert into stockreportproduct select * from semporna_27jun22.stockreportproduct;
insert into pshift select * from semporna_27jun22.pshift;
insert into pshiftdetails select * from semporna_27jun22.pshiftdetails;

insert into oneway select * from semporna_27jun22.oneway;
insert into onewaylocation select * from semporna_27jun22.onewaylocation;
insert into onewayrelation select * from semporna_27jun22.onewayrelation;

insert into creditact select * from semporna_27jun22.creditact;
insert into creditact_ledger select * from semporna_27jun22.creditact_ledger;
insert into recvnote select * from semporna_27jun22.recvnote;
--insert into recvnote_list select * from semporna_27jun22.recvnote_list;

insert into fuel_itemdetails select * from semporna_27jun22.fuel_itemdetails;
insert into fuel_receipt select * from semporna_27jun22.fuel_receipt;
insert into fuel_receiptdetails select * from semporna_27jun22.fuel_receiptdetails;
insert into fuel_receiptlist select * from semporna_27jun22.fuel_receiptlist;
insert into fuel_receiptproduct select * from semporna_27jun22.fuel_receiptproduct;
insert into cstore_itemdetails select * from semporna_27jun22.cstore_itemdetails;
insert into cstore_receipt select * from semporna_27jun22.cstore_receipt;
insert into cstore_receiptdetails select * from semporna_27jun22.cstore_receiptdetails;
insert into cstore_receiptproduct select * from semporna_27jun22.cstore_receiptproduct;
insert into cstore_receiptrefund select * from semporna_27jun22.cstore_receiptrefund;
