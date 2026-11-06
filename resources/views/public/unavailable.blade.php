{{-- Shown on a paused (trial over, not paid) or ended invitation. Plain on purpose; styling is the design pass's job. --}}
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $vm->coupleNames('km') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite('resources/css/app.css')
</head>
<body class="min-h-dvh grid place-items-center p-6 text-center">
    <main class="max-w-sm grid gap-3">
        <h1 class="text-xl font-semibold" lang="km">{{ $vm->coupleNames('km') }}</h1>
        @if ($state === \App\Enums\InvitationState::Ended)
            <p lang="km">ពិធីមង្គលការបានបញ្ចប់ហើយ។ សូមអរគុណចំពោះការចូលរួម!</p>
            <p lang="en">The wedding has passed. Thank you for celebrating with us!</p>
        @else
            <p lang="km">ធៀបការនេះមិនទាន់អាចមើលបាននៅឡើយទេ។ សូមព្យាយាមម្ដងទៀតនៅពេលក្រោយ។</p>
            <p lang="en">This invitation isn’t available right now. Please try again later.</p>
        @endif
    </main>
</body>
</html>
