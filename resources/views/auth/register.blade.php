@extends('frontend.layouts.master')
@section('meta_title', 'Register'. ' || ' . $setting->app_name)

@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb :title="__('Register')" :links="[['url' => route('home'), 'text' => __('Home')], ['url' => route('register'), 'text' => __('Register')]]" />
    <!-- breadcrumb-area-end -->

    <!-- singUp-area -->
    <section class="singUp-area section-py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8">
                    <div class="singUp-wrap">
                        <h2 class="title">{{ __('Create Your Account') }}</h2>
                        <p>{{ __('Hey there! Ready to join the party? We just need a few details from you to get') }}<br>{{ __('started Lets do this!') }}</p>
                        @if($setting->google_login_status == 'active')
                        <div class="account__social">
                            <a href="{{ route('auth.social', 'google') }}" class="account__social-btn">
                                <img src="{{ asset('frontend/img/icons/google.svg') }}" alt="img">
                                {{ __('Continue with google') }}
                            </a>
                        </div>
                        <div class="account__divider">
                            <span>{{ __('or') }}</span>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('register') }}" class="account__form" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row gutter-20">
                                <div class="col-md-12">
                                    <div class="form-grp">
                                        <label for="fast-name">{{ __('Full Name') }}</label>
                                        <input type="text" id="fast-name" placeholder="{{ __('full name') }}" name="name">
                                        <x-frontend.validation-error name="name" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-grp">
                                <label for="email">{{ __('Email') }}</label>
                                <input type="email" id="email" placeholder="{{ __('email') }}" name="email">
                                <x-frontend.validation-error name="email" />
                            </div>
                            <div class="form-grp">
                                <label for="password">{{ __('Password') }}</label>
                                <input type="password" id="password" placeholder="{{ __('password') }}" name="password">
                                <x-frontend.validation-error name="password" />
                            </div>
                            <div class="form-grp">
                                <label for="confirm-password">{{ __('Confirm Password') }}</label>
                                <input type="password" id="confirm-password" placeholder="{{ __('Confirm Password') }}" name="password_confirmation">
                                <x-frontend.validation-error name="password_confirmation" />
                            </div>

                            <div class="form-grp">
                                <label for="role">{{ __('Select Role') }}</label>
                                <select id="role" name="role" class="form-select">
                                    <option value="">{{ __('Select Role') }}</option>
                                    <option value="instructor">{{ __('Instructor') }}</option>
                                    <option value="student">{{ __('Student') }}</option>
                                </select>
                                <x-frontend.validation-error name="role" />
                            </div>

                            <!-- Become Instructor Section -->
                            <div id="become-instructor-section" class="d-none">
                                @if ($instructorRequestSetting?->need_identity_scan == 1)
                                <div class="form-group mb-3">
                                    <label for="identity_scan">{{ __('Identity Scan') }} <code>*</code></label>
                                    <input type="file" class="form-control" name="identity_scan" id="identity_scan" accept=".pdf,.png,.jpg,.jpeg">
                                    <small class="form-text text-muted">
                                        {{ __('Please upload a document in PDF, PNG, or JPG format, with a size below 1 MB.') }}
                                    </small>
                                </div>
                                @endif

                                @if ($instructorRequestSetting?->need_certificate == 1)
                                <div class="form-group mb-3" id="certificate-group">
                                    <label for="certificate">{{ __('Certificate and documents') }} <code>*</code></label>
                                    <input type="file" class="form-control mb-2" name="certificate[]" id="certificate" accept=".pdf,.png,.jpg,.jpeg">
                                    <small class="form-text text-muted">
                                        {{ __('Please upload a document in PDF, PNG, or JPG format, with a size below 1 MB.') }}
                                    </small>
                                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-certificate">{{ __('Add More') }}</button>
                                </div>
                                @endif
                                <div class="form-grp">
                                    <label for="payout_account">{{ __('Payout Account') }} <code>*</code></label>
                                    <select name="payout_account" id="payout_account" class="form-select">
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach ($withdrawMethods as $method)  
                                            <option value="{{ $method->name }}">{{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="payment_info_wrap d-none">
                                    <div class="form-grp">
                                        <label for="payment_information">{{ __('Payment Information') }} <code>*</code></label>
                                        @foreach ($withdrawMethods as $method)  
                                        <div class="normal-text payment-{{ $method->name }} payment-info">
                                            {!! clean($method->description) !!}
                                        </div>
                                        @endforeach
                                        <textarea name="payout_information" placeholder="{{ __('Information') }}"></textarea>
                                    </div>
                                </div>

                                <div class="form-grp">
                                    <label for="extra_information">{{ __('Extra Information') }}</label>
                                    <textarea name="extra_information" placeholder="{{ __('Extra Information') }}" id="extra_information"></textarea>
                                </div>
                            </div>

                            <!-- g-recaptcha -->
                            @if (Cache::get('setting')->recaptcha_status === 'active')
                                <div class="form-grp mt-3">
                                    <div class="g-recaptcha" data-sitekey="{{ Cache::get('setting')->recaptcha_site_key }}"></div>
                                    <x-frontend.validation-error name="g-recaptcha-response" />
                                </div>
                            @endif

                            <button type="submit" class="btn btn-two arrow-btn">{{ __('Sign Up') }}<img
                                    src="{{ asset('frontend/img/icons/right_arrow.svg') }}" alt="img"
                                    class="injectable"></button>
                        </form>

                        <div class="account__switch">
                            <p>{{ __('Already have an account?') }}<a href="{{ route('login') }}">{{ __('Login') }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.getElementById('role').addEventListener('change', function () {
        const instructorSection = document.getElementById('become-instructor-section');
        if (this.value === 'instructor') {
            instructorSection.classList.remove('d-none');
        } else {
            instructorSection.classList.add('d-none');
        }
    });

    document.getElementById('add-certificate').addEventListener('click', function () {
        const certificateGroup = document.getElementById('certificate-group');
        const newInput = document.createElement('div');
        newInput.classList.add('form-group', 'mt-2');

        newInput.innerHTML = `
            <label for="certificate">{{ __('Certificate and documents') }} <code>*</code></label>
            <input type="file" class="form-control mb-2" name="certificate[]" accept=".pdf,.png,.jpg,.jpeg">
            <small class="form-text text-muted">
                {{ __('Please upload a document in PDF, PNG, or JPG format, with a size below 1 MB.') }}
            </small>
            <button type="button" class="btn btn-sm btn-danger remove-certificate">{{ __('Remove') }}</button>
        `;

        certificateGroup.appendChild(newInput);
        newInput.querySelector('.remove-certificate').addEventListener('click', function () {
            this.parentElement.remove();
        });
    });
</script>
@endpush