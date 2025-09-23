# Manual Payment System Documentation

## Overview

The manual payment system allows users to purchase courses and bundles via bank transfer or mobile wallet payments. Admins can approve or reject these payments after verifying the transaction details.

## Features

✅ **User Features:**
- Manual payment form with transaction ID input
- Support for bank transfers and mobile wallets
- Payment status tracking
- Automatic enrollment after approval

✅ **Admin Features:**
- Payment approval/rejection interface
- Pending payments notification in dashboard
- Transaction verification tools
- Admin notes for approval decisions

## Configuration

### Environment Variables

Add these to your `.env` file to customize payment details:

```env
# Bank Transfer Details
PAYMENT_BANK_NAME="ABC Bank Limited"
PAYMENT_ACCOUNT_NAME="Learning Management System"
PAYMENT_ACCOUNT_NUMBER="1234567890123"
PAYMENT_ROUTING_NUMBER="123456789"
PAYMENT_SWIFT_CODE="ABCBBD23"

# Mobile Wallet Numbers
PAYMENT_BKASH_NUMBER="01700-123456"
PAYMENT_NAGAD_NUMBER="01700-123456"
PAYMENT_ROCKET_NUMBER="01700-1234567"
PAYMENT_UPAY_NUMBER="01700-123456"

# Support Contact
PAYMENT_SUPPORT_EMAIL="support@yoursite.com"
PAYMENT_SUPPORT_PHONE="+880-1700-123456"
```

### Payment Configuration

The payment details are configured in `config/payments.php`. You can modify:

- Bank account details
- Mobile wallet numbers
- Processing times
- Support contact information
- Payment instructions

## How It Works

### For Users:

1. **Browse & Select**: User finds a course or bundle they want to purchase
2. **Choose Payment**: Click "Purchase via Bank Transfer / Mobile Wallet"
3. **View Instructions**: See bank details and mobile wallet numbers
4. **Make Payment**: Transfer exact amount using preferred method
5. **Submit Details**: Fill form with transaction ID and payment details
6. **Wait for Approval**: Admin reviews and approves within 24 hours
7. **Get Access**: Automatic enrollment after approval

### For Admins:

1. **Dashboard Notification**: See pending payments count in admin dashboard
2. **Review Payments**: Go to Manual Payments section
3. **Verify Transaction**: Check transaction ID and payment details
4. **Approve/Reject**: Make decision with optional admin notes
5. **Auto Enrollment**: System automatically enrolls user if approved

## Admin Interface

### Accessing Manual Payments

1. Login as admin
2. Go to Admin Dashboard
3. Click "Manual Payments" (shows pending count as red badge)

### Payment Management

- **Filter by Status**: Pending, Approved, Rejected
- **Filter by Method**: Bank Transfer, Mobile Wallet
- **Quick Actions**: Approve/Reject buttons for pending payments
- **Admin Notes**: Add notes when approving/rejecting

### Approval Process

1. Review payment details:
   - User information
   - Course/Bundle details
   - Payment amount
   - Transaction ID
   - Sender details

2. Verify transaction:
   - Check transaction ID in bank/mobile wallet
   - Verify amount matches
   - Confirm sender details

3. Make decision:
   - **Approve**: User gets instant access, admin note optional
   - **Reject**: User notified, admin note required

## Database Changes

The system adds these fields to the `payments` table:

- `payment_method`: 'online', 'bank_transfer', 'mobile_wallet'
- `user_transaction_id`: User-provided transaction ID
- `payment_note`: User's payment notes
- `sender_name`: Name of person who sent money
- `sender_mobile`: Mobile number used for payment
- `approved_at`: When admin approved/rejected
- `approved_by`: Admin who made the decision
- `admin_note`: Admin's approval/rejection note

## Routes

### User Routes:
- `GET /payments/manual/course/{course}` - Manual payment form for course
- `GET /payments/manual/bundle/{bundle}` - Manual payment form for bundle
- `POST /payments/manual/submit` - Submit payment details
- `GET /payments/{payment}/status` - View payment status

### Admin Routes:
- `GET /admin/payments/manual/list` - List manual payments
- `POST /admin/payments/{payment}/approve` - Approve payment
- `POST /admin/payments/{payment}/reject` - Reject payment

## Security Features

- User can only view their own payment status
- Admin authentication required for approval/rejection
- Transaction ID uniqueness validation
- Amount verification against course/bundle price
- Prevention of duplicate pending payments

## Customization

### Payment Methods

To enable/disable payment methods, edit `config/payments.php`:

```php
'bank_transfer' => [
    'enabled' => true, // Set to false to disable
    // ... other settings
],

'mobile_wallet' => [
    'enabled' => true, // Set to false to disable
    // ... other settings
],
```

### Adding New Mobile Wallet Providers

Add to `config/payments.php`:

```php
'mobile_wallet' => [
    'providers' => [
        // ... existing providers
        'cellfin' => [
            'name' => 'CellFin',
            'number' => env('PAYMENT_CELLFIN_NUMBER', '01700-123456'),
        ],
    ],
],
```

## Troubleshooting

### Common Issues:

1. **Routes not working**: Run `php artisan route:clear`
2. **Config not updating**: Run `php artisan config:clear`
3. **Database errors**: Check migration ran successfully
4. **Payment not found**: Ensure Payment model relationships are working

### Migration Issues:

If migration fails, manually run:
```bash
php artisan migrate --path=/database/migrations/2025_09_19_140434_add_manual_payment_fields_to_payments_table.php
```

## Future Enhancements

Possible improvements:
- SMS notifications for payment status
- Bulk payment approval
- Payment receipt generation
- Integration with accounting systems
- Automated transaction verification APIs
- Payment analytics and reporting

---

For technical support, contact your development team or refer to the Laravel documentation.
