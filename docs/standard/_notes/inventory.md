## Behaviours

- Stock is per warehouse; cost is a moving average per item per warehouse. Every movement carries its document's date; a back-dated or edited document recosts what came after it, never before the first open period.
- Items are of four types (inventory, non-inventory, service, group) with any number of units and conversion ratios, a selling price per price category, a purchase price, default accounts, a tax code, a minimum stock and an opening stock per warehouse.
- Adjustments change quantity and/or value per warehouse against an adjustment account, and record opening stock. Transfers move goods between warehouses with an in-transit stage. Stock opname orders a count per warehouse; the result is compared with the system and its variance posted as an adjustment, approved by someone other than the counter.
- Order fulfilment shows what is ordered and not yet delivered, per item; stock by warehouse the quantity and value per item per warehouse; minimum stock the items below their minimum.
