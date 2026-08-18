<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $customSubject }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #FFFDF9; color: #2C2C2C; padding: 20px; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; background: #FFFFFF; border: 1px solid #EAE4D9; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        
        <!-- Header -->
        <div style="text-align: center; border-bottom: 2px solid #EAE4D9; padding-bottom: 20px; margin-bottom: 24px;">
            <h1 style="color: #890F14; font-family: 'Georgia', serif; font-size: 1.8rem; margin: 0 0 8px 0;">Desi Foods Hounslow</h1>
            <p style="color: #E67E22; font-weight: 700; margin: 0; font-size: 0.95rem;">Authentic Indian Groceries & Takeaway Store</p>
        </div>

        <!-- Content -->
        <h2 style="color: #890F14; font-size: 1.3rem; margin-bottom: 16px;">Hello {{ $user->name }},</h2>
        
        <div style="font-size: 1rem; color: #2C2C2C; margin-bottom: 28px; white-space: pre-line;">
            {!! nl2br(e($customMessage)) !!}
        </div>

        <!-- CTA Button -->
        <div style="text-align: center; margin-bottom: 32px;">
            <a href="{{ url('/') }}" style="background: #890F14; color: #FFFFFF; padding: 12px 28px; border-radius: 30px; text-decoration: none; font-weight: 700; display: inline-block;">
                Visit Desi Foods Store &rarr;
            </a>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #EAE4D9; padding-top: 16px; font-size: 0.82rem; color: #777; text-align: center;">
            Desi Foods Hounslow &bull; 3-4 Green Parade, Whitton Road, Hounslow TW3 2EN<br>
            Contact us: <a href="mailto:support@desifoods.co.uk" style="color: #E67E22;">support@desifoods.co.uk</a>
        </div>
    </div>
</body>
</html>
