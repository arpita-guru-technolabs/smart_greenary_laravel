<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>OTP for Registration</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f4f4;">
        <div style="background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #333; text-align: center; margin-bottom: 20px;">OTP for Registration</h2>
            <p style="color: #555; font-size: 16px;">Dear Farmer,</p>
            <p style="color: #555; font-size: 16px;">Your OTP for registration is:</p>
            <div style="text-align: center; padding: 15px; margin: 20px 0; background: #f8f9fa; border-radius: 8px; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #333;">
                {{ $otp }}
            </div>
            <p style="color: #777; font-size: 14px;">This OTP is valid for 10 minutes. Please do not share it with anyone.</p>
            <p style="color: #777; font-size: 14px; margin-top: 20px;">If you didn't request this, please ignore this email.</p>
        </div>
    </div>
</body>
</html>