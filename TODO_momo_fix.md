# TODO: Fix Momo QR Payment Failure

## Problem Analysis
- `process_momo.php` ignores `booking_id` from POST, generates random `orderId = time()` instead of using DB `order_id`
- `momo_return.php` only checks `resultCode`, does NOT verify signature, does NOT update DB, does NOT log errors
- `momo_ipn.php` does NOT verify signature, uses mismatched `orderId`
- Result: Momo returns with unknown orderId → DB update fails → "THANH TOÁN THẤT BẠI"

## Fix Plan

### Step 1: Fix `process_momo.php`

- [ ] Query DB to get correct `order_id` and `total_pay`
- [ ] Use DB `order_id` as Momo `orderId`
- [ ] Store `requestId` in `$_SESSION` for verification

### Step 2: Fix `momo_return.php`
- [ ] Log all `$_GET` params to `momo_return_log.txt`
- [ ] Verify Momo signature using secretKey
- [ ] If `resultCode == 0` and signature valid:
  - Update `booking_order` status to 'Đã Thanh Toán'
  - Show success message
- [ ] If failed: show specific error message from Momo

### Step 3: Fix `momo_ipn.php`
- [ ] Verify Momo signature before processing
- [ ] Use correct `orderId` to update DB
- [ ] Send email notification on success

### Step 4: Test
- [ ] Check `momo_debug.txt` for API response
- [ ] Check `momo_return_log.txt` for return params
- [ ] Verify DB update after payment

