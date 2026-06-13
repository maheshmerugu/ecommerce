<x-mail::message>
# Welcome to {{ $appName }}, {{ $customer->first_name }}! 🎉

Thank you for creating an account with us. We're thrilled to have you on board.

Your account is all set up and ready to go. Start exploring our collection of cars and accessories.

<x-mail::button :url="$shopUrl">
Start Shopping
</x-mail::button>

**Your account details:**
- **Name:** {{ $customer->first_name }} {{ $customer->last_name }}
- **Email:** {{ $customer->email }}

If you have any questions, feel free to reply to this email — we're always happy to help.

Thanks,<br>
The {{ $appName }} Team
</x-mail::message>
