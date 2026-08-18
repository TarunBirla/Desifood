@extends('layouts.admin')

@section('title', 'Admin Profile Settings | Desi Foods Admin')
@section('page-title', 'My Admin Profile')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 32px; align-items: flex-start;">
        
        <!-- Profile Card Overview -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; text-align: center; box-shadow: var(--shadow-sm);">
            <div style="width: 90px; height: 90px; border-radius: 50%; background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%); color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; font-family: 'Playfair Display', serif; margin: 0 auto 16px; border: 4px solid var(--cream-warm); box-shadow: var(--shadow-sm);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.35rem; color: var(--maroon); margin-bottom: 4px;">{{ $user->name }}</h3>
            <span style="display: inline-block; background: rgba(230, 126, 34, 0.15); color: var(--saffron-deep); font-size: 0.78rem; font-weight: 700; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">System Administrator</span>
            
            <div style="border-top: 1px solid var(--cream-dark); padding-top: 16px; font-size: 0.88rem; color: var(--charcoal-light); text-align: left; display: flex; flex-direction: column; gap: 10px;">
                <div><i class="fa-regular fa-envelope me-2" style="color: var(--saffron);"></i> <strong>Email:</strong> {{ $user->email }}</div>
                <div><i class="fa-solid fa-phone me-2" style="color: var(--saffron);"></i> <strong>Phone:</strong> {{ $user->phone ?? 'Not specified' }}</div>
                <div><i class="fa-regular fa-calendar-check me-2" style="color: var(--saffron);"></i> <strong>Joined:</strong> {{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</div>
            </div>

            <div style="margin-top: 24px; border-top: 1px solid var(--cream-dark); padding-top: 20px;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-block btn-sm" style="background: rgba(192,57,43,0.1); color: #c0392b; border: 1px solid rgba(192,57,43,0.2); width: 100%; font-weight: 700; padding: 10px 16px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fa-solid fa-right-from-bracket"></i> Sign Out / Log Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Profile Update & Security Form -->
        <div style="display: flex; flex-direction: column; gap: 28px;">
            
            <!-- Personal Details Card -->
            <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; box-shadow: var(--shadow-sm);">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; color: var(--maroon); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid var(--cream); padding-bottom: 10px;">
                    <i class="fa-solid fa-user-pen" style="color: var(--saffron);"></i> Account & Profile Information
                </h3>

                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    
                    <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                        <div>
                            <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--charcoal); margin-bottom: 6px;">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control" style="width: 100%; padding: 12px 16px;">
                        </div>

                        <div>
                            <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--charcoal); margin-bottom: 6px;">Admin Email Address *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control" style="width: 100%; padding: 12px 16px;">
                        </div>

                        <div>
                            <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--charcoal); margin-bottom: 6px;">Contact Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+44 7123 456789" class="form-control" style="width: 100%; padding: 12px 16px;">
                        </div>
                    </div>

                    <!-- Security & Change Password Header -->
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; color: var(--maroon); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid var(--cream); padding-bottom: 10px; margin-top: 32px;">
                        <i class="fa-solid fa-shield-halved" style="color: var(--saffron);"></i> Security & Change Password
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                        <div>
                            <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--charcoal); margin-bottom: 6px;">Current Password (required only if changing password)</label>
                            <input type="password" name="current_password" placeholder="••••••••" class="form-control" style="width: 100%; padding: 12px 16px;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--charcoal); margin-bottom: 6px;">New Password</label>
                                <input type="password" name="new_password" placeholder="Min 8 characters" class="form-control" style="width: 100%; padding: 12px 16px;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--charcoal); margin-bottom: 6px;">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" placeholder="Confirm password" class="form-control" style="width: 100%; padding: 12px 16px;">
                            </div>
                        </div>
                    </div>

                    <div style="text-align: right; margin-top: 24px;">
                        <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-weight: 700; font-size: 0.95rem;">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
