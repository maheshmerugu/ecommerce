@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Settings</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="payment-tab" data-toggle="tab" href="#payment" role="tab">
                <i class="fas fa-credit-card mr-1"></i> Payment (Razorpay)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab">
                <i class="fas fa-envelope mr-1"></i> Email (Resend)
                @if($emailSetting->is_active)
                    <span class="badge badge-success ml-1">Active</span>
                @else
                    <span class="badge badge-secondary ml-1">Not configured</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab">
                <i class="fas fa-truck mr-1"></i> Shipping
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="store-tab" data-toggle="tab" href="#store" role="tab">
                <i class="fas fa-store mr-1"></i> Store Info
            </a>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabContent">

        <!-- ─── PAYMENT TAB ─── -->
        <div class="tab-pane fade show active" id="payment" role="tabpanel">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-credit-card mr-2"></i>Razorpay Payment Gateway
                        </h6>
                        <a href="https://dashboard.razorpay.com/app/keys" target="_blank"
                           class="btn btn-sm btn-outline-info ml-auto">
                            <i class="fas fa-external-link-alt mr-1"></i>Get Keys from Razorpay Dashboard
                        </a>
                    </div>
                    <div class="card-body">

                        <!-- Mode selector -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label font-weight-bold">Mode</label>
                            <div class="col-sm-9">
                                <div class="d-flex">
                                    <div class="custom-control custom-radio mr-4">
                                        <input type="radio" id="mode_test" name="razorpay_mode" value="test"
                                               class="custom-control-input"
                                               {{ ($settings['razorpay_mode'] ?? 'test') === 'test' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="mode_test">
                                            <span class="badge badge-warning mr-1">TEST</span> Test Mode
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="mode_live" name="razorpay_mode" value="live"
                                               class="custom-control-input"
                                               {{ ($settings['razorpay_mode'] ?? '') === 'live' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="mode_live">
                                            <span class="badge badge-success mr-1">LIVE</span> Live Mode
                                        </label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Use Test mode during development.</small>
                            </div>
                        </div>

                        <div id="test-key-section">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Test Mode Keys</strong> — Get them from
                                <a href="https://dashboard.razorpay.com/app/keys" target="_blank">Razorpay Dashboard → Settings → API Keys</a>.
                                Test keys start with <code>rzp_test_</code>.
                            </div>
                        </div>
                        <div id="live-key-section" style="display:none;">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Live Mode Keys</strong> — Live keys start with <code>rzp_live_</code>.
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Key ID <span class="text-danger">*</span>
                                <br><small class="text-muted font-weight-normal">rzp_test_... or rzp_live_...</small>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="text" name="razorpay_key_id" id="razorpay_key_id"
                                           class="form-control @error('razorpay_key_id') is-invalid @enderror"
                                           value="{{ $settings['razorpay_key_id'] ?? '' }}"
                                           placeholder="rzp_test_XXXXXXXXXXXXXXXX" autocomplete="off">
                                </div>
                                @error('razorpay_key_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Key Secret <span class="text-danger">*</span>
                                <br><small class="text-muted font-weight-normal">Keep this secret!</small>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" name="razorpay_key_secret" id="razorpay_key_secret"
                                           class="form-control @error('razorpay_key_secret') is-invalid @enderror"
                                           value="{{ $settings['razorpay_key_secret'] ?? '' }}"
                                           placeholder="••••••••••••••••••••" autocomplete="new-password">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="toggleSecret()">
                                            <i class="fas fa-eye" id="secret-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('razorpay_key_secret')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="button" class="btn btn-outline-primary" onclick="testRazorpay()">
                                    <i class="fas fa-plug mr-2"></i>Validate Key Format
                                </button>
                                <span id="test-result" class="ml-3"></span>
                            </div>
                        </div>

                        <div class="card border-left-info mt-4">
                            <div class="card-body py-3">
                                <h6 class="font-weight-bold text-info mb-2">
                                    <i class="fas fa-question-circle mr-1"></i>How to get Razorpay API Keys
                                </h6>
                                <ol class="mb-0 small">
                                    <li>Go to <a href="https://dashboard.razorpay.com" target="_blank">dashboard.razorpay.com</a> and sign in</li>
                                    <li>Click <strong>Settings → API Keys</strong> in the left sidebar</li>
                                    <li>Click <strong>Generate Test Key</strong> for testing</li>
                                    <li>Copy the <strong>Key ID</strong> and <strong>Key Secret</strong> above</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-2"></i>Save Payment Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- ─── EMAIL TAB (Resend API) ─── -->
        <div class="tab-pane fade" id="email" role="tabpanel">
            <form method="POST" action="{{ route('admin.settings.update-email') }}">
                @csrf
                @method('PUT')

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-envelope mr-2"></i>Email Configuration (Resend)
                        </h6>
                        @if($emailSetting->is_active)
                            <span class="badge badge-success ml-3 px-3 py-2">
                                <i class="fas fa-check-circle mr-1"></i>Active — {{ $emailSetting->from_address }}
                            </span>
                        @else
                            <span class="badge badge-secondary ml-3 px-3 py-2">
                                <i class="fas fa-times-circle mr-1"></i>Not configured
                            </span>
                        @endif
                    </div>
                    <div class="card-body">

                        <div class="alert alert-info mb-4">
                            <h6 class="font-weight-bold"><i class="fas fa-info-circle mr-2"></i>Resend (recommended for GoDaddy / shared hosting)</h6>
                            <p class="mb-2 small">SMTP ports are blocked on GoDaddy. This site sends all emails via the <strong>Resend HTTP API</strong>.</p>
                            <ol class="mb-0 small">
                                <li>Sign up at <a href="https://resend.com" target="_blank"><strong>resend.com</strong></a></li>
                                <li>Add & verify your domain at <a href="https://resend.com/domains" target="_blank">resend.com/domains</a></li>
                                <li>Create an API key and paste it below</li>
                            </ol>
                        </div>

                        <input type="hidden" name="mail_mailer" value="resend">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        Resend API Key <span class="text-danger">*</span>
                                        @if($emailSetting->password)
                                            <small class="text-success font-weight-normal ml-2">
                                                <i class="fas fa-lock mr-1"></i>Saved (leave blank to keep current)
                                            </small>
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        </div>
                                        <input type="password" name="mail_password" id="mail_password" class="form-control"
                                               placeholder="{{ $emailSetting->password ? 'Leave blank to keep current key' : 're_xxxxxxxxxxxx' }}"
                                               autocomplete="new-password">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" onclick="toggleMailPass()">
                                                <i class="fas fa-eye" id="mail-pass-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Starts with <code>re_</code> — from Resend dashboard → API Keys</small>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-muted mb-3">Sender Details (shown to email recipients)</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">From Name</label>
                                    <input type="text" name="mail_from_name" class="form-control"
                                           value="{{ old('mail_from_name', $emailSetting->from_name ?: ($settings['store_name'] ?? $storeName)) }}"
                                           placeholder="Your Store Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">From Email <span class="text-danger">*</span></label>
                                    <input type="email" name="mail_from_address" class="form-control" required
                                           value="{{ old('mail_from_address', $emailSetting->from_address ?: $emailSetting->username) }}"
                                           placeholder="support@fourwheels.co.in">
                                    <small class="text-muted">Must be a verified domain on Resend.</small>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="card border-left-primary mb-0">
                            <div class="card-body py-3">
                                <h6 class="font-weight-bold mb-3">
                                    <i class="fas fa-paper-plane mr-2 text-primary"></i>Send Test Email
                                </h6>
                                <div class="row align-items-end">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="email" id="test_email_addr" class="form-control"
                                                   placeholder="recipient@example.com">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" onclick="sendTestEmail()">
                                                    <i class="fas fa-paper-plane mr-1"></i>Send Test
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Save settings first, then verify by sending a test email.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="test-email-result" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-2"></i>Save Email Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- ─── SHIPPING TAB ─── -->
        <div class="tab-pane fade" id="shipping" role="tabpanel">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-truck mr-2"></i>Shipping Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Flat Shipping Fee (₹)</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" name="shipping_fee" class="form-control"
                                           value="{{ $settings['shipping_fee'] ?? config('shop.shipping_fee', 150) }}"
                                           min="0" step="0.01">
                                </div>
                                <small class="form-text text-muted">Applied to every order.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Free Shipping Above (₹)</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" name="free_shipping_above" class="form-control"
                                           value="{{ $settings['free_shipping_above'] ?? '' }}"
                                           min="0" step="0.01" placeholder="Leave empty to disable">
                                </div>
                                <small class="form-text text-muted">Orders above this amount get free shipping.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-2"></i>Save Shipping Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- ─── STORE TAB ─── -->
        <div class="tab-pane fade" id="store" role="tabpanel">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-store mr-2"></i>Store Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Store Name</label>
                            <div class="col-sm-6">
                                <input type="text" name="store_name" class="form-control"
                                       value="{{ $settings['store_name'] ?? $storeName }}"
                                       placeholder="My Store">
                                <small class="text-muted">Displayed across the site header and emails.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Support Email</label>
                            <div class="col-sm-6">
                                <input type="email" name="store_email" class="form-control"
                                       value="{{ $settings['store_email'] ?? '' }}"
                                       placeholder="support@example.com">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Phone</label>
                            <div class="col-sm-4">
                                <input type="text" name="store_phone" class="form-control"
                                       value="{{ $settings['store_phone'] ?? '' }}"
                                       placeholder="+91 98765 43210">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Address</label>
                            <div class="col-sm-6">
                                <textarea name="store_address" rows="3" class="form-control"
                                          placeholder="Store address...">{{ $settings['store_address'] ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Currency Symbol</label>
                            <div class="col-sm-2">
                                <input type="text" name="currency_symbol" class="form-control"
                                       value="{{ $settings['currency_symbol'] ?? '₹' }}"
                                       maxlength="5">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-2"></i>Save Store Settings
                    </button>
                </div>
            </form>
        </div>

    </div><!-- end tab-content -->
</div>
@endsection

@section('scripts')
<script>
// Razorpay mode toggle
document.querySelectorAll('input[name="razorpay_mode"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.getElementById('test-key-section').style.display = this.value === 'test' ? '' : 'none';
        document.getElementById('live-key-section').style.display = this.value === 'live' ? '' : 'none';
    });
});
(function() {
    var checked = document.querySelector('input[name="razorpay_mode"]:checked');
    if (checked && checked.value === 'live') {
        document.getElementById('test-key-section').style.display = 'none';
        document.getElementById('live-key-section').style.display = '';
    }
})();

function toggleSecret() {
    var el = document.getElementById('razorpay_key_secret');
    var eye = document.getElementById('secret-eye');
    el.type = el.type === 'password' ? 'text' : 'password';
    eye.classList.toggle('fa-eye');
    eye.classList.toggle('fa-eye-slash');
}

function toggleMailPass() {
    var el = document.getElementById('mail_password');
    var eye = document.getElementById('mail-pass-eye');
    el.type = el.type === 'password' ? 'text' : 'password';
    eye.classList.toggle('fa-eye');
    eye.classList.toggle('fa-eye-slash');
}

function sendTestEmail() {
    var addr = document.getElementById('test_email_addr').value.trim();
    var resultEl = document.getElementById('test-email-result');
    if (!addr) {
        resultEl.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>Please enter a recipient email address.</span>';
        return;
    }
    resultEl.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Sending…</span>';

    fetch('{{ route("admin.settings.test-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ test_email: addr })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultEl.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i>' + data.message + '</span>';
        } else {
            resultEl.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>' + data.message + '</span>';
        }
    })
    .catch(() => {
        resultEl.innerHTML = '<span class="text-danger">Request failed. Check your network.</span>';
    });
}

function testRazorpay() {
    var keyId = document.getElementById('razorpay_key_id').value.trim();
    var resultEl = document.getElementById('test-result');
    if (!keyId) {
        resultEl.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Please enter a Key ID first.</span>';
        return;
    }
    if (keyId.startsWith('rzp_test_') || keyId.startsWith('rzp_live_')) {
        resultEl.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i>Key ID format is valid! Save and try a test order.</span>';
    } else {
        resultEl.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i>Key should start with <code>rzp_test_</code> or <code>rzp_live_</code>.</span>';
    }
}

// Re-activate the correct tab on page reload after form submission
(function() {
    var hash = window.location.hash;
    if (hash) {
        var tab = document.querySelector('a[href="' + hash + '"]');
        if (tab) { tab.click(); }
    }
})();
</script>
@endsection
