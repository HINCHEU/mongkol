<div style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid rgba(197, 160, 89, 0.35); background-color: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(88, 17, 26, 0.05); padding: 20px 24px; margin-bottom: 12px; box-sizing: border-box;">
    <!-- Top Gold Accent Line -->
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #D4AF37 50%, transparent); pointer-events: none;"></div>

    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px;">
        <!-- Left: Atelier Greeting & Title -->
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; border-radius: 12px; background: linear-gradient(135deg, #8B1426 0%, #7B1122 50%, #4C0A13 100%); border: 1.5px solid rgba(212, 175, 55, 0.6); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(123, 17, 34, 0.3); flex-shrink: 0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="width: 24px; height: 24px; display: block;">
                    <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" fill="#F5DE93" fill-opacity="0.35" stroke="#F5DE93" stroke-width="1.5" stroke-linejoin="round" />
                    <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" fill="none" stroke="#F5DE93" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" fill="none" stroke="#F5DE93" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="12" cy="18" r="1.4" fill="#F5DE93" />
                </svg>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-family: 'Plus Jakarta Sans', system-ui, sans-serif; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: #8C6D23;">រាជរោងមង្គល · Royal Atelier</span>
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; background-color: #F0FDF4; color: #166534; border: 1px solid #BBF7D0;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #22C55E;"></span>
                        Live Operations
                    </span>
                </div>
                <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 22px; font-weight: 700; color: #1A1412; margin: 2px 0 0 0; letter-spacing: -0.01em;">
                    Welcome back, {{ $user?->name ?? 'Admin' }}
                </h2>
                <p style="font-family: 'Kantumruy Pro', system-ui, sans-serif; font-size: 12px; color: #78716C; margin: 2px 0 0 0;">
                    Phnom Penh Time: <span style="font-weight: 600; color: #44403C;">{{ $phnomPenhTime }}</span>
                </p>
            </div>
        </div>

        <!-- Right: Fast Actions & Status Pills -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
            @if ($pendingPayments > 0)
                <a href="{{ route('filament.admin.resources.payments.index') }}"
                   style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; background-color: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background-color: #F59E0B;"></span>
                    <span>{{ $pendingPayments }} KHQR {{ $pendingPayments === 1 ? 'Payment' : 'Payments' }} to Review</span>
                </a>
            @endif

            <a href="{{ route('filament.admin.resources.invitations.index') }}"
               style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; color: #44403C; background-color: #FAF6EE; border: 1px solid rgba(197, 160, 89, 0.4); text-decoration: none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7B1122" stroke-width="2" style="width: 14px; height: 14px; display: block;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Invitations ({{ $liveInvitations }} Live)</span>
            </a>

            <a href="{{ route('filament.admin.pages.settings') }}"
               style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; color: #44403C; background-color: #FAF6EE; border: 1px solid rgba(197, 160, 89, 0.4); text-decoration: none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8C6D23" stroke-width="2" style="width: 14px; height: 14px; display: block;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Settings</span>
            </a>
        </div>
    </div>
</div>
