@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Settings</h1>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="payment-tab" data-toggle="tab" href="#payment" role="tab">
                    <i class="fas fa-credit-card mr-1"></i> Payment (Razorpay)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab">
                    <i class="fas fa-envelope mr-1"></i> Email (SMTP)
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
                                <div class="d-flex gap-3">
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
                                <small class="form-text text-muted">Use Test mode during development. Switch to Live when your store is ready.</small>
                            </div>
                        </div>

                        <!-- Test Keys -->
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
                                <strong>Live Mode Keys</strong> — Use only when your store is ready to accept real payments.
                                Live keys start with <code>rzp_live_</code>.
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
                                           placeholder="rzp_test_XXXXXXXXXXXXXXXX"
                                           autocomplete="off">
                                </div>
                                @error('razorpay_key_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">This is your <strong>public</strong> key — safe to use in frontend.</small>
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
                                           placeholder="••••••••••••••••••••"
                                           autocomplete="new-password">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="toggleSecret()">
                                            <i class="fas fa-eye" id="secret-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('razorpay_key_secret')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Never share this with anyone. Used for server-side signature verification.</small>
                            </div>
                        </div>

                        <!-- Test connection button -->
                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="button" class="btn btn-outline-primary" onclick="testRazorpay()">
                                    <i class="fas fa-plug mr-2"></i>Test Connection
                                </button>
                                <span id="test-result" class="ml-3"></span>
                            </div>
                        </div>

                        <!-- How to get keys guide -->
                        <div class="card border-left-info mt-4">
                            <div class="card-body py-3">
                                <h6 class="font-weight-bold text-info mb-2">
                                    <i class="fas fa-question-circle mr-1"></i>How to get Razorpay API Keys
                                </h6>
                                <ol class="mb-0 small">
                                    <li>Go to <a href="https://dashboard.razorpay.com" target="_blank">dashboard.razorpay.com</a> and sign in (or create a free account)</li>
                                    <li>Click <strong>Settings</strong> in the left sidebar</li>
                                    <li>Click <strong>API Keys</strong> tab</li>
                                    <li>Click <strong>Generate Test Key</strong> (for testing) or <strong>Generate Live Key</strong> (for production)</li>
                                    <li>Copy the <strong>Key ID</strong> and <strong>Key Secret</strong> and paste them above</li>
                                    <li>Save settings and your checkout will start working!</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── SHIPPING TAB ─── -->
            <div class="tab-pane fade" id="shipping" role="tabpanel">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-truck mr-2"></i>Shipping Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Flat Shipping Fee (₹)
                            </label>
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
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                Free Shipping Above (₹)
                            </label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" name="free_shipping_above" class="form-control"
                                           value="{{ $settings['free_shipping_above'] ?? '' }}"
                                           min="0" step="0.01"
                                           placeholder="Leave empty to disable">
                                </div>
                                <small class="form-text text-muted">Orders above this amount get free shipping. Leave empty to disable.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── STORE TAB ─── -->
            <div class="tab-pane fade" id="store" role="tabpanel">
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
            </div>

            <!-- ─── EMAIL TAB ─── -->
            <div class="tab-pane fade" id="email" role="tabpanel">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-envelope mr-2"></i>Gmail SMTP Settings
                        </h6>
                    </div>
                    <div class="card-body">

                        <!-- Gmail guide -->
                        <div class="alert alert-info mb-4">
                            <h6 class="font-weight-bold"><i class="fab fa-google mr-2"></i>How to set up Gmail SMTP</h6>
                            <ol class="mb-0 small">
                                <li>Go to your Google Account → <strong>Security</strong></li>
                                <li>Enable <strong>2-Step Verification</strong> (required)</li>
                                <li>Go to <strong>Security → App passwords</strong></li>
                                <li>Select app: <em>Mail</em> → Select device: <em>Other</em> → type "Laravel"</li>
                                <li>Click <strong>Generate</strong> — copy the 16-character password</li>
                                <li>Use that password below (NOT your regular Gmail password)</li>
                            </ol>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">SMTP Host</label>
                            <div class="col-sm-5">
                                <input type="text" name="mail_host" class="form-control"
                                       value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}"
                                       placeholder="smtp.gmail.com">
                                <small class="text-muted">For Gmail use: <code>smtp.gmail.com</code></small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">SMTP Port</label>
                            <div class="col-sm-3">
                                <select name="mail_port" class="form-control">
                                    <option value="587" {{ ($settings['mail_port'] ?? '587') == '587' ? 'selected' : '' }}>587 — TLS (Recommended)</option>
                                    <option value="465" {{ ($settings['mail_port'] ?? '') == '465' ? 'selected' : '' }}>465 — SSL</option>
                                    <option value="25"  {{ ($settings['mail_port'] ?? '') == '25'  ? 'selected' : '' }}>25 — No encryption</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Encryption</label>
                            <div class="col-sm-3">
                                <select name="mail_encryption" class="form-control">
                                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Gmail Address <span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fab fa-google"></i></span>
                                    </div>
                                    <input type="email" name="mail_username" class="form-control"
                                           value="{{ $settings['mail_username'] ?? '' }}"
                                           placeholder="yourname@gmail.com">
                                </div>
                                <small class="text-muted">Your full Gmail address</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">App Password <span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="mail_password" id="mail_password" class="form-control"
                                           value="{{ $settings['mail_password'] ?? '' }}"
                                           placeholder="16-character App Password"
                                           autocomplete="new-password">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="toggleMailPass()">
                                            <i class="fas fa-eye" id="mail-pass-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-danger font-weight-bold">Use Gmail App Password — NOT your regular Gmail password!</small>
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-muted mb-3">From Details (shown to recipients)</h6>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">From Name</label>
                            <div class="col-sm-5">
                                <input type="text" name="mail_from_name" class="form-control"
                                       value="{{ $settings['mail_from_name'] ?? ($settings['store_name'] ?? $storeName) }}"
                                       placeholder="Your Store Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">From Email</label>
                            <div class="col-sm-5">
                                <input type="email" name="mail_from_address" class="form-control"
                                       value="{{ $settings['mail_from_address'] ?? ($settings['mail_username'] ?? '') }}"
                                       placeholder="noreply@yourdomain.com">
                                <small class="text-muted">Must match or be an alias of your Gmail address.</small>
                            </div>
                        </div>

                        <hr>
                        <!-- Test Email -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Send Test Email</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="email" id="test_email_addr" class="form-control"
                                           placeholder="recipient@example.com">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-primary" onclick="sendTestEmail()">
                                            <i class="fas fa-paper-plane mr-1"></i>Send Test
                                        </button>
                                    </div>
                                </div>
                                <div id="test-email-result" class="mt-2"></div>
                                <small class="text-muted">Save settings first, then send a test email to verify.</small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- end tab-content -->

        <div class="mt-3 mb-5">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save mr-2"></i>Save All Settings
            </button>
        </div>
    </form>
</div>

@section('scripts')
<script>
// Show/hide key info based on mode
document.querySelectorAll('input[name="razorpay_mode"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.getElementById('test-key-section').style.display = this.value === 'test' ? '' : 'none';
        document.getElementById('live-key-section').style.display = this.value === 'live' ? '' : 'none';
    });
});
// Set initial state
(function() {
    var checked = document.querySelector('input[name="razorpay_mode"]:checked');
    if (checked && checked.value === 'live') {
        document.getElementById('test-key-section').style.display = 'none';
        document.getElementById('live-key-section').style.display = '';
    }
})();

function toggleMailPass() {
    var el = document.getElementById('mail_password');
    var eye = document.getElementById('mail-pass-eye');
    if (el.type === 'password') {
        el.type = 'text';
        eye.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        el.type = 'password';
        eye.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function sendTestEmail() {
    var addr = document.getElementById('test_email_addr').value.trim();
    var resultEl = document.getElementById('test-email-result');
    if (!addr) {
        resultEl.innerHTML = '<span class="text-danger">Please enter a recipient email address.</span>';
        return;
    }
    resultEl.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Sending...</span>';

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

function toggleSecret() {
    var el = document.getElementById('razorpay_key_secret');
    var eye = document.getElementById('secret-eye');
    if (el.type === 'password') {
        el.type = 'text';
        eye.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        el.type = 'password';
        eye.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function testRazorpay() {
    var keyId = document.getElementById('razorpay_key_id').value.trim();
    var resultEl = document.getElementById('test-result');

    if (!keyId) {
        resultEl.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Please enter a Key ID first.</span>';
        return;
    }

    if (keyId.startsWith('rzp_test_') || keyId.startsWith('rzp_live_')) {
        resultEl.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i>Key ID format looks valid! Save settings and try a test order.</span>';
    } else {
        resultEl.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i>Key should start with <code>rzp_test_</code> or <code>rzp_live_</code>.</span>';
    }
}
</script>
@endsection
@endsection
